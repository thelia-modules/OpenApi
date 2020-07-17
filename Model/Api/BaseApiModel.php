<?php

namespace OpenApi\Model\Api;

use OpenApi\Exception\OpenApiException;
use OpenApi\OpenApi;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Thelia\Core\Translation\Translator;

abstract class BaseApiModel implements \JsonSerializable
{
    /** @var ValidatorInterface  */
    private $validator;

    public function __construct()
    {
        $this->validator = Validation::createValidatorBuilder()
            ->enableAnnotationMapping()
            ->getValidator();
    }

    /**
     * @param $groups
     *
     * @return BaseApiModel
     * @throws OpenApiException
     */
    public function validate($groups, $recursively = true)
    {
        $violations = $this->getViolations($groups, $recursively);

        if (empty($violations)) {
            return $this;
        }

        $error = new Error(
            Translator::getInstance()->trans('Invalid data', [], OpenApi::DOMAIN_NAME)
        );

        $error->setSchemaViolations($violations);

        throw new OpenApiException($error);
    }

    public function getViolations($groups, $recursively = true, $propertyPatchPrefix = "")
    {
        $violations = array_map(function ($violation) use ($propertyPatchPrefix) {
            return (new SchemaViolation())
                    ->setKey($propertyPatchPrefix.$violation->getPropertyPath())
                    ->setError($violation->getMessage());
        },
            iterator_to_array($this->validator->validate($this, $groups))
        );

        if ($recursively === true) {
            foreach (get_object_vars($this) as $key => $value) {
                if ($value instanceof BaseApiModel) {
                    $violations = array_merge($violations, $value->getViolations("read", true, $propertyPatchPrefix.$key."."));
                }
            }
        }

        return $violations;
    }

    public function jsonSerialize()
    {
        $normalizer = new ObjectNormalizer();
        $serializer = new Serializer([$normalizer]);

        return $serializer->normalize($this, null);
    }

    public function createFromData($data)
    {
        if (is_string($data)) {
            $data = json_decode($data, true);
        }

        foreach ($data as $key => $value) {
            $methodName = 'set'.ucfirst($key);
            if (method_exists($this, $methodName)) {
                $this->$methodName($value);
            }
        }

        return $this;
    }

    /**
     * Should return the Thelia model associated with the OpenApi model
     *
     * @return mixed
     */
    public function getTheliaModel()
    {
        return null;
    }

    public function toTheliaModel()
    {
        if (!$theliaModel = $this->getTheliaModel()) {
            throw new \Exception(Translator::getInstance()->trans('You need to override the getTheliaModel method to use the toTheliaModel method.', [], OpenApi::DOMAIN_NAME));
        }

        foreach (get_class_methods($this) as $methodName) {
            if (0 === strncasecmp('get', $methodName, 3)) {
                $theliaMethod = 'set' . substr($methodName, 3);

                if (method_exists($theliaModel, $theliaMethod)) {
                    $theliaModel->$theliaMethod($this->$methodName);
                }
            }
        }

        return $theliaModel;
    }
}
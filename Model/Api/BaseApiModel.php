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

    public function createFromJson($json)
    {
        $data = json_decode($json, true);

        return $this->createFromArray($data);
    }

    public function createFromArray($data)
    {
        foreach ($data as $key => $value) {
            $methodName = 'set'.ucfirst($key);
            if (method_exists($this, $methodName)) {
                $this->$methodName($value);
            }
        }

        return $this;
    }
}
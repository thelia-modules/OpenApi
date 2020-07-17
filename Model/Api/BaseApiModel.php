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
    protected $validator;

    /** @var ModelFactory */
    protected $modelFactory;

    public function __construct(ModelFactory $modelFactory)
    {
        $this->validator = Validation::createValidatorBuilder()
            ->enableAnnotationMapping()
            ->getValidator();

        $this->modelFactory = $modelFactory;
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

        /** @var Error $error */
        $error = $this->modelFactory->buildModel(
            'Error',
            ['title' => Translator::getInstance()->trans('Invalid data', [], OpenApi::DOMAIN_NAME)]
        );

        $error->setSchemaViolations($violations);

        throw new OpenApiException($error);
    }

    public function getViolations($groups, $recursively = true, $propertyPatchPrefix = "")
    {
        $modelFactory = $this->modelFactory;
        $violations = array_map(function ($violation) use ($modelFactory, $propertyPatchPrefix) {
            return $modelFactory->buildModel(
                'SchemaViolation',
                [
                    'key' => $propertyPatchPrefix.$violation->getPropertyPath(),
                    'error' => $violation->getMessage()
                ]
            );
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
                if (is_array($value)) {
                    $openApiModel = $this->modelFactory->buildModel(ucfirst($key), $value);
                    $value = null !== $openApiModel ? $openApiModel : $value;
                }
                $this->$methodName($value);
            }
        }

        return $this;
    }
}
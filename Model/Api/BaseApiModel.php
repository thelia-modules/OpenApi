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

    /**
     * Override to return the Thelia model associated with the OpenApi model instead of null
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
                    $theliaModel->$theliaMethod($this->$methodName());
                }
            }
        }

        return $theliaModel;
    }

    public function createFromTheliaModel($theliaModel)
    {
        foreach (get_class_methods($theliaModel) as $theliaMethod) {
            if (0 === strncasecmp('get', $theliaMethod, 3)) {
                $oaMethod = 'set' . substr($theliaMethod, 3);
                $theliaPossibleMethods = [
                    $theliaMethod,
                    $theliaMethod . 'Model',
                    'get' . substr(get_class($this), strrpos(get_class($this), "\\") + 1) . substr($theliaMethod, 3)
                ];
                
                if (method_exists($this, $oaMethod)) {
                    if ($this->modelFactory->modelExists(ucfirst(substr($theliaMethod, 3)))) {
                        $oaModel = $this->modelFactory->buildModel(ucfirst(substr($theliaMethod, 3)), null);

                        foreach ($theliaPossibleMethods as $theliaPossibleMethod) {
                            if (method_exists($theliaModel, $theliaPossibleMethod)) {
                                if (is_object($theliaModel->$theliaPossibleMethod())) {
                                    $this->$oaMethod($oaModel->createFromTheliaModel($theliaModel->$theliaPossibleMethod()));
                                    break;
                                }
                            }
                        }

                    } else {
                        $this->$oaMethod($theliaModel->$theliaMethod());
                    }
                }
            }
        }

        return $this;
    }
}
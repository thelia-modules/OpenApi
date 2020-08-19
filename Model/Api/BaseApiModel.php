<?php

namespace OpenApi\Model\Api;

use OpenApi\Exception\OpenApiException;
use OpenApi\OpenApi;
use Propel\Runtime\ActiveRecord\ActiveRecordInterface;
use Propel\Runtime\Collection\Collection;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Thelia\Core\Translation\Translator;
use Thelia\Model\AddressQuery;

abstract class BaseApiModel implements \JsonSerializable
{
    /** @var ValidatorInterface  */
    protected $validator;

    /** @var ModelFactory  */
    protected $modelFactory;

    protected $locale;

    public function __construct(ModelFactory $modelFactory, RequestStack $requestStack)
    {
        $this->validator = Validation::createValidatorBuilder()
            ->enableAnnotationMapping()
            ->getValidator();

        $this->modelFactory = $modelFactory;
        $this->locale = $requestStack->getCurrentRequest()->getSession()->getLang()->getLocale();
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

    public function createOrUpdateFromData($data, $locale = null)
    {
        if (is_object($data)) {
            $this->createFromTheliaModel($data, $locale);
        }

        if (is_string($data)) {
            $data = json_decode($data, true);
        }

        if (is_iterable($data)) {
            foreach ($data as $key => $value) {
                $setMethodName = 'set'.ucfirst($key);
                $getMethodName = 'get'.ucfirst($key);
                if (method_exists($this, $setMethodName)) {
                    if (is_array($value)) {
                        if (method_exists($this, $getMethodName) && $this->$getMethodName() instanceof BaseApiModel) {
                            $this->$setMethodName($this->$getMethodName()->updateFromData($value));
                            continue;
                        }
                        $openApiModel = $this->modelFactory->buildModel(ucfirst($key), $value);
                        $value = null !== $openApiModel ? $openApiModel : $value;
                    }
                    $this->$setMethodName($value);
                }
            }
        }

        return $this;
    }

    /**
     * Override to return the Thelia model associated with the OpenApi model instead of null
     *
     * @return mixed
     */
    protected function getTheliaModel()
    {
        $theliaModelName = "Thelia\Model\\".basename(str_replace('\\', '/', get_class($this)));

        if (!class_exists($theliaModelName)) {
            return null;
        }

        if (null !== $id = $this->getId()) {
            $theliaModelQueryName = $theliaModelName . 'Query';
            return $theliaModelQueryName::create()->filterById($id)->findOne();
        }

        /** @var ActiveRecordInterface $newTheliaModel */
        $newTheliaModel = new $theliaModelName;
        $newTheliaModel->setNew(true);

        return $newTheliaModel;
    }

    public function toTheliaModel($locale = null)
    {
        if (null === $theliaModel = $this->getTheliaModel()) {
            throw new \Exception(Translator::getInstance()->trans('You need to override the getTheliaModel method to use the toTheliaModel method.', [], OpenApi::DOMAIN_NAME));
        }

        // If model need locale, set it
        if (method_exists($theliaModel, "setLocale")) {
            $theliaModel->setLocale($locale !== null ? $locale : $this->locale);
        }

        // Look all method of Open API model
        foreach (get_class_methods($this) as $methodName) {
            // If it's not a getter skip it
            if (0 !== strncasecmp('get', $methodName, 3)) {
                continue ;
            }
            $getter = $methodName;

            // Build thelia setter name
            $setter = 'set' . substr($getter, 3);

            // Check if setter exist in Thelia model
            if (!method_exists($theliaModel, $setter)) {
                continue ;
            }

            $value = $this->$getter();


            // If Values are the same skip this property
            if (method_exists($theliaModel, $getter) && $theliaModel->$getter() === $value) {
                continue ;
            }

            // if the property is another Api model
            if ($value instanceof BaseApiModel) {
                // If it doesn't have a correspondant thelia model skip it
                if (null === $value->getTheliaModel())
                {
                    continue;
                }

                // Else try to set the model id
                $setModelIdMethod = $setter.'Id';
                if (!method_exists($theliaModel, $setModelIdMethod)) {
                    continue;
                }
                $setter = $setModelIdMethod;
                $value = $value->getId();
            }

            $theliaModel->$setter($value);
        }

        return $theliaModel;
    }

    public function createFromTheliaModel($theliaModel, $locale = null)
    {
        if (method_exists($theliaModel, "setLocale")) {
            $theliaModel->setLocale($locale !== null ? $locale : $this->locale);
        }

        foreach (get_class_methods($this) as $modelMethod) {
            if (0 === strncasecmp('set', $modelMethod, 3)) {
                $property = ucfirst(substr($modelMethod, 3));
                $lowercaseProperty = ucfirst(strtolower($property));

                $theliaPossibleMethods = [
                    'get' . $property,
                    'get' . $lowercaseProperty,
                    'get' . $property . 'Model',
                    'get' . $lowercaseProperty . 'Model',
                    'get' . substr(get_class($theliaModel), strrpos(get_class($theliaModel), "\\") + 1) . $property,
                    'get' . substr(get_class($theliaModel), strrpos(get_class($theliaModel), "\\") + 1) . $lowercaseProperty
                ];

                $availableMethods = array_filter(array_intersect($theliaPossibleMethods, get_class_methods($theliaModel)));

                if (empty($availableMethods)) {
                    continue;
                }

                $theliaValue = null;
                while (!empty($availableMethods) && ($theliaValue === null || empty($theliaValue))) {
                    $theliaMethod = array_pop($availableMethods);

                    $theliaValue = $theliaModel->$theliaMethod();

                    if ($theliaValue instanceof  Collection) {
                        $theliaValue = array_filter(array_map(function ($value) use ($property) {
                            return $this->modelFactory->buildModel($property, $value);
                        }, iterator_to_array($theliaValue)));
                        continue;
                    }

                    if (is_object($theliaValue) && $this->modelFactory->modelExists($property)) {
                        $theliaValue = $this->modelFactory->buildModel($property, $theliaValue);
                    }
                }

                $this->$modelMethod($theliaValue);
            }
        }

        return $this;
    }
}
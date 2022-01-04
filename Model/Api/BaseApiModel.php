<?php

namespace OpenApi\Model\Api;

use Doctrine\Common\Annotations\AnnotationRegistry;
use OpenApi\Events\ModelExtendDataEvent;
use OpenApi\Events\ModelValidationEvent;
use OpenApi\Exception\OpenApiException;
use OpenApi\Normalizer\ModelApiNormalizer;
use OpenApi\OpenApi;
use Propel\Runtime\ActiveRecord\ActiveRecordInterface;
use Propel\Runtime\Collection\Collection;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Core\Translation\Translator;
use Thelia\Model\Country;
use Thelia\Model\State;
use Thelia\TaxEngine\TaxEngine;

abstract class BaseApiModel implements \JsonSerializable
{
    /** @var ValidatorInterface */
    protected $validator;

    /** @var ModelFactory */
    protected $modelFactory;

    /** @var Request */
    protected $request;

    /** @var string */
    protected $locale;

    /** @var Country */
    protected $country;

    /** @var State|null */
    protected $state;

    /** @var EventDispatcherInterface */
    protected $dispatcher;

    protected $extendedData;

    public function __construct(
        ModelFactory $modelFactory,
        RequestStack $requestStack,
        TaxEngine $taxEngine,
        EventDispatcherInterface $dispatcher
    ) {
        if (class_exists(AnnotationRegistry::class)) {
            AnnotationRegistry::registerLoader('class_exists');
        }

        $this->dispatcher = $dispatcher;
        $this->validator = Validation::createValidatorBuilder()
            ->enableAnnotationMapping()
            ->getValidator();

        $this->modelFactory = $modelFactory;
        $this->request = $requestStack->getCurrentRequest();
        $this->locale = $this->request->getSession()->getLang(true)->getLocale();
        $this->country = $taxEngine->getDeliveryCountry();
        $this->state = $taxEngine->getDeliveryState();

        if (method_exists($this, 'initI18n')) {
            $this->initI18n($modelFactory);
        }
    }

    /**
     * @param $groups
     *
     * @return BaseApiModel
     *
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

    public function getViolations($groups, $recursively = true, $propertyPatchPrefix = '')
    {
        $modelFactory = $this->modelFactory;
        $violations = array_map(function ($violation) use ($modelFactory, $propertyPatchPrefix) {
            return $modelFactory->buildModel(
                'SchemaViolation',
                [
                    'key' => $propertyPatchPrefix.$violation->getPropertyPath(),
                    'error' => $violation->getMessage(),
                ]
            );
        },
            iterator_to_array($this->validator->validate($this, null, $groups))
        );

        if ($recursively === true) {
            foreach (get_object_vars($this) as $key => $value) {
                if ($value instanceof self) {
                    $violations = array_merge($violations, $value->getViolations('read', true, $propertyPatchPrefix.$key.'.'));
                }
            }
        }

        $event = new ModelValidationEvent($this, $modelFactory, $propertyPatchPrefix);
        $this->dispatcher->dispatch($event, ModelValidationEvent::MODEL_VALIDATION_EVENT_PREFIX.$this->snakeCaseName());

        return array_merge($violations, $event->getViolations());
    }

    public function jsonSerialize()
    {
        $normalizer = new ModelApiNormalizer();
        $serializer = new Serializer([$normalizer]);

        return $serializer->normalize($this, null);
    }

    public function createOrUpdateFromData($data, $locale = null): void
    {
        if (null === $locale) {
            $locale = $this->locale;
        }

        if (\is_object($data)) {
            $this->createFromTheliaModel($data, $locale);
        }

        if (\is_string($data)) {
            $data = json_decode($data, true);
        }

        if (\is_array($data) || $data instanceof \Traversable) {
            foreach ($data as $key => $value) {
                $setMethodName = 'set'.ucfirst($key);
                $getMethodName = 'get'.ucfirst($key);
                if (method_exists($this, $setMethodName)) {
                    if (\is_array($value)) {
                        if (method_exists($this, $getMethodName) && $this->$getMethodName() instanceof self) {
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

        $modelExtendEvent = (new ModelExtendDataEvent())
            ->setData($data)
            ->setLocale($locale)
            ->setModel($this);

        $this->dispatcher->dispatch(
            $modelExtendEvent,
            ModelExtendDataEvent::ADD_EXTEND_DATA_PREFIX.$this->snakeCaseName()
        );

        $this->setExtendData($modelExtendEvent->getExtendData());
    }

    /**
     * Override to return the propel model associated with the OpenApi model instead of null.
     *
     * @return mixed
     */
    protected function getTheliaModel($propelModelName = null)
    {
        if (null === $propelModelName) {
            $propelModelName = "Thelia\Model\\".basename(str_replace('\\', '/', static::class));
        }

        if (!class_exists($propelModelName)) {
            return null;
        }

        if (method_exists($this, 'getId') && null !== $id = $this->getId()) {
            $theliaModelQueryName = $propelModelName.'Query';

            return $theliaModelQueryName::create()->filterById($id)->findOne();
        }

        /** @var ActiveRecordInterface $newTheliaModel */
        $newTheliaModel = new $propelModelName();
        $newTheliaModel->setNew(true);

        return $newTheliaModel;
    }

    public function toTheliaModel($locale = null)
    {
        if (null === $theliaModel = $this->getTheliaModel()) {
            throw new \Exception(Translator::getInstance()->trans('Propel model not found automatically for class %className%. Please override the getTheliaModel method to use the toTheliaModel method.', ['%className%' => basename(static::class)], OpenApi::DOMAIN_NAME));
        }

        // If model need locale, set it
        if (method_exists($theliaModel, 'setLocale')) {
            $theliaModel->setLocale($locale !== null ? $locale : $this->locale);
        }

        // Look all method of Open API model
        foreach (get_class_methods($this) as $methodName) {
            $getter = $methodName;
            $setter = null;

            // If it's not a getter skip it
            if (0 === strncasecmp('get', $methodName, 3)) {
                // Build thelia setter name
                $setter = 'set'.substr($getter, 3);
            }

            // For boolean method like "isVisible"
            if ($setter === null && 0 === strncasecmp('is', $methodName, 2)) {
                // Build thelia setter name
                $setter = 'set'.substr($getter, 2);
            }

            // Check if setter exist in Thelia model
            if (null === $setter || !method_exists($theliaModel, $setter)) {
                continue;
            }

            $value = $this->$getter();

            // If Values are the same skip this property
            if (method_exists($theliaModel, $getter) && $theliaModel->$getter() === $value) {
                continue;
            }

            // if the property is another Api model
            if ($value instanceof self) {
                // If it doesn't have a correspondant thelia model skip it
                if (null === $value->getTheliaModel()) {
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

            // Todo transform array to collection
            if (is_array($value)) {
                continue;
            }

            $theliaModel->$setter($value);
        }

        return $theliaModel;
    }

    public function createFromTheliaModel($theliaModel, $locale = null)
    {
        if (method_exists($theliaModel, 'setLocale')) {
            $theliaModel->setLocale($locale !== null ? $locale : $this->locale);
        }

        foreach (get_class_methods($this) as $modelMethod) {
            if (0 === strncasecmp('set', $modelMethod, 3)) {
                $property = ucfirst(substr($modelMethod, 3));
                $lowercaseProperty = ucfirst(strtolower($property));

                // List all possible getters for this property in propel
                $propelPossibleMethods = [                                                                                                       //  EXAMPLE :
                    'get'.$property,                                                                                                           //  getProductSaleElements
                    'get'.$property.'s',                                                                                                       //  getProductSaleElementss
                    'get'.$lowercaseProperty,                                                                                                  //  getProductsaleelements
                    'get'.$lowercaseProperty.'s',                                                                                              //  getProductsaleelementss
                    'get'.$property.'Model',                                                                                                 //  getProductSaleElementsModel
                    'get'.$lowercaseProperty.'Model',                                                                                        //  getProductsaleelementsModel
                    'get'.substr(\get_class($theliaModel), strrpos(\get_class($theliaModel), '\\') + 1).$property,                //  getCartProductSaleElements
                    'get'.substr(\get_class($theliaModel), strrpos(\get_class($theliaModel), '\\') + 1).$lowercaseProperty,        //  getCartProductsaleelements
                ];

                $availableMethods = array_filter(array_intersect($propelPossibleMethods, get_class_methods($theliaModel)));

                if (empty($availableMethods)) {
                    continue;
                }

                $theliaValue = null;
                while (!empty($availableMethods) && ($theliaValue === null || empty($theliaValue))) {
                    $theliaMethod = array_pop($availableMethods);

                    $theliaValue = $theliaModel->$theliaMethod();

                    if ($theliaValue instanceof Collection) {
                        $theliaValue = array_filter(array_map(fn ($value) => $this->modelFactory->buildModel($property, $value), iterator_to_array($theliaValue)));
                        continue;
                    }

                    if (\is_object($theliaValue) && $this->modelFactory->modelExists($property)) {
                        $theliaValue = $this->modelFactory->buildModel($property, $theliaValue);
                    }
                }

                $this->$modelMethod($theliaValue);
            }
        }

        return $this;
    }

    public function setExtendData($extendedData)
    {
        $this->extendedData = $extendedData;

        return $this;
    }

    public function extendedDataValue()
    {
        return $this->extendedData;
    }

    protected function snakeCaseName()
    {
        $name = basename(str_replace('\\', '/', static::class));

        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $name, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }

        return implode('_', $ret);
    }
}

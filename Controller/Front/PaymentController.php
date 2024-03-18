<?php

namespace OpenApi\Controller\Front;

use OpenApi\Annotations as OA;
use OpenApi\Events\OpenApiEvents;
use OpenApi\Events\PaymentModuleOptionEvent;
use OpenApi\Model\Api\ModelFactory;
use OpenApi\Model\Api\PaymentModule;
use OpenApi\Service\OpenApiService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Thelia\Core\Event\Payment\IsValidPaymentEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Model\Cart;
use Thelia\Model\Lang;
use Thelia\Model\Module;
use Thelia\Model\ModuleQuery;
use Thelia\Module\BaseModule;

/**
 * @Route("/payment", name="payment")
 */
class PaymentController extends BaseFrontOpenApiController
{
    /**
     * @Route("/modules", name="payment_modules", methods="GET")
     *
     * @OA\Get(
     *     path="/payment/modules",
     *     tags={"payment", "modules"},
     *     summary="List all available payment modules",
     *     @OA\Parameter(
     *          name="orderId",
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\Parameter(
     *          name="moduleId",
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="Success",
     *          @OA\JsonContent(
     *                  type="array",
     *                  @OA\Items(
     *                      ref="#/components/schemas/PaymentModule"
     *                  )
     *          )
     *     ),
     *     @OA\Response(
     *          response="400",
     *          description="Bad request",
     *          @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function getPaymentModules(
        EventDispatcherInterface $dispatcher,
        ModelFactory $modelFactory,
        Request $request
    ) {
        $cart = $request->getSession()->getSessionCart($dispatcher);
        $lang = $request->getSession()->getLang();
        $moduleQuery = ModuleQuery::create()
            ->filterByActivate(1)
            ->filterByType(BaseModule::PAYMENT_MODULE_TYPE)
            ->orderByPosition();

        if (null !== $moduleId = $request->get('moduleId')) {
            $moduleQuery->filterById($moduleId);
        }

        $modules = $moduleQuery->find();

        // Return formatted valid payment
        return OpenApiService::jsonResponse(
            array_map(
                fn ($module) => $this->getPaymentModule($dispatcher, $modelFactory, $module, $cart, $lang),
                iterator_to_array($modules)
            )
        );
    }

    protected function getPaymentModule(
        EventDispatcherInterface $dispatcher,
        ModelFactory $modelFactory,
        Module $paymentModule,
        Cart $cart,
        Lang $lang
    ) {
        $paymentModule->setLocale($lang->getLocale());
        $moduleInstance = $paymentModule->getPaymentModuleInstance($this->container);

        $isValidPaymentEvent = new IsValidPaymentEvent($moduleInstance, $cart);
        $dispatcher->dispatch(
            $isValidPaymentEvent,
            TheliaEvents::MODULE_PAYMENT_IS_VALID
        );

        $paymentModuleOptionEvent = new PaymentModuleOptionEvent($paymentModule, $cart);

        $dispatcher->dispatch(
            $paymentModuleOptionEvent,
            OpenApiEvents::MODULE_PAYMENT_GET_OPTIONS
        );

        /** @var PaymentModule $paymentModule */
        $paymentModule = $modelFactory->buildModel('PaymentModule', $paymentModule);

        $paymentModule->setValid($isValidPaymentEvent->isValidModule())
            ->setCode($moduleInstance->getCode())
            ->setMinimumAmount($isValidPaymentEvent->getMinimumAmount())
            ->setMaximumAmount($isValidPaymentEvent->getMaximumAmount())
            ->setOptionGroups($paymentModuleOptionEvent->getPaymentModuleOptionGroups());

        return $paymentModule;
    }
}

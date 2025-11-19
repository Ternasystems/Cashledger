<?php

namespace API_Payments_Controller;

use API_Payments_Contract\IPaymentMethodService;
use API_Payments_Facade\PaymentFacade;
use Exception;
use TS_Controller\Classes\BaseController;
use TS_Http\Classes\Request;
use TS_Http\Classes\Response;

/**
 * The concrete PaymentController.
 * It extends the AbstractController and is now extremely simple.
 */
class PaymentController extends BaseController
{
    /**
     * This tells the parent class what the default resource type is
     * if the 'controller' query param is not provided.
     */
    protected string $defaultResourceType = 'PaymentMethod';

    /**
     * We inject our specific PaymentFacade for type safety.
     * We then pass it up to the parent constructor.
     */
    public function __construct(PaymentFacade $facade)
    {
        parent::__construct($facade);
    }
}
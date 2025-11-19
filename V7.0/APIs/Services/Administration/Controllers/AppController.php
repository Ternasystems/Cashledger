<?php

namespace API_Administration_Controller;

use API_Administration_Facade\AppFacade;

/**
 * The concrete AppController.
 * It extends the AbstractController and becomes incredibly simple.
 * Its only job is to:
 * 1. Define the default resource type ('App').
 * 2. Inject the *specific* facade (AppFacade) and pass it to the parent.
 */
class AppController extends AbstractController
{
    /**
     * This tells the parent class what the default resource type is
     * if the 'controller' query param is not provided.
     */
    protected string $defaultResourceType = 'App';

    /**
     * We inject our specific AppFacade for type safety.
     * We then pass it up to the parent constructor, which
     * accepts the generic IFacade interface.
     */
    public function __construct(AppFacade $facade)
    {
        parent::__construct($facade);
    }
}
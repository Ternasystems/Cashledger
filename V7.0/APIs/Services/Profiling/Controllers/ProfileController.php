<?php

namespace API_Profiling_Controller;

use API_Administration_Controller\AbstractController;
use API_Profiling_Facade\ProfileFacade;

/**
 * The concrete ProfileController.
 * It extends the AbstractController and is now extremely simple.
 */
class ProfileController extends AbstractController
{
    /**
     * This tells the parent class what the default resource type is
     * if the 'controller' query param is not provided.
     */
    protected string $defaultResourceType = 'Profile';

    /**
     * We inject our specific ProfileFacade for type safety.
     * We then pass it up to the parent constructor.
     */
    public function __construct(ProfileFacade $facade)
    {
        parent::__construct($facade);
    }
}
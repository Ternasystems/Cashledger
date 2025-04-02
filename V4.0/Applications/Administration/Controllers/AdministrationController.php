<?php

namespace APP_Administration_Controller;

use API_Administration_Controller\ParameterController;
use TS_Utility\Classes\UrlGenerator;

class AdministrationController extends Controller
{
    private UrlGenerator $urlGenerator;
    private ParameterController $parameterController;
    private array $json;
    public function __construct(ParameterController $_parameterController)
    {
        $this->urlGenerator = new UrlGenerator(dirname(__DIR__, 2).'\Assets\Data\json\config.json');
        parent::__construct($this->urlGenerator);

        // Set the Exception property
        $this->SetException();
        $this->parameterController = $_parameterController;
        $this->json = json_decode(file_get_contents(dirname(__DIR__, 1).'\Assets\Configs\config.json'), true);
    }

    /* Actions */

    // Get AppVersion
    public function GetAppVersion(): string
    {
        $fn = $this->json['ParameterFunctions']['AppVersion'];
        return $this->parameterController->GetFrom($fn);
    }
}
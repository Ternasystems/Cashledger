<?php

namespace APP_Presentation_Controller;

use API_Administration_Controller\AppController;
use APP_Administration_Controller\Controller;
use ReflectionException;
use TS_Utility\Classes\UrlGenerator;

class DashboardController extends Controller
{
    private UrlGenerator $urlGenerator;
    private array $config;
    private AppController $appController;

    public function __construct(AppController $_appController)
    {
        $this->urlGenerator = new UrlGenerator(dirname(__DIR__, 2).'\Assets\Data\json\config.json');
        parent::__construct($this->urlGenerator);

        // Set the Exception property
        $this->SetException();
        $this->config = json_decode(file_get_contents(dirname(__DIR__, 2).'\Assets\Data\json\config.json'), true);
        $this->appController = $_appController;
    }

    /* Actions */

    // Home page
    /**
     * @throws ReflectionException
     */
    public function Index(): void
    {
        $languages = $this->languageController->Get();
        $apps = $this->config["apps"];
        $ft = [
            'app' => $this->urlGenerator->application($_SERVER['REQUEST_URI']),
            'ctrl' => $this->urlGenerator->controller($_SERVER['REQUEST_URI']),
            'action' => $this->urlGenerator->action($_SERVER['REQUEST_URI'])
        ];
        $this->view('Dashboard', ['languages' => $languages, 'apps' => $apps, 'registeredApps' => $this->appController->Get(), 'ft' => $ft]);
    }
}
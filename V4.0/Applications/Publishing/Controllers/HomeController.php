<?php

namespace APP_Publishing_Controller;

use APP_Administration_Controller\Controller;
use ReflectionException;
use TS_Utility\Classes\UrlGenerator;

class HomeController extends Controller
{
    private UrlGenerator $urlGenerator;
    private array $config;

    public function __construct()
    {
        $this->urlGenerator = new UrlGenerator(dirname(__DIR__, 2).'\Assets\Data\json\config.json');
        parent::__construct($this->urlGenerator);

        // Set the Exception property
        $this->SetException();
        $this->config = json_decode(file_get_contents(dirname(__DIR__, 2).'\Assets\Data\json\config.json'), true);
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
        $this->view('Home', ['languages' => $languages, 'apps' => $apps, 'ft' => $ft]);
    }
}
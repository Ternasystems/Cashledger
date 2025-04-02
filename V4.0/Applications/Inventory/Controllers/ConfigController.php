<?php

namespace APP_Inventory_Controller;

use APP_Administration_Controller\Controller;
use ReflectionException;
use TS_Utility\Classes\UrlGenerator;

class ConfigController extends Controller
{
    private UrlGenerator $urlGenerator;
    private array $config;

    public function __construct()
    {
        $this->urlGenerator = new UrlGenerator(dirname(__DIR__, 2).'\Assets\Data\json\config.json');
        parent::__construct($this->urlGenerator);

        // Set the Exception property
        $this->SetException();
        $this->config = json_decode(file_get_contents(dirname(__DIR__, 1).'\Assets\Configs\config.json'), true);
    }

    /* Actions */

    // Category page
    /**
     * @throws ReflectionException
     */
    public function Category()
    {
        $languages = $this->languageController->Get();
        $apps = $this->config["apps"];
        $components = $this->config["components"];
        $ft = [
            'app' => $this->urlGenerator->application($_SERVER['REQUEST_URI']),
            'ctrl' => $this->urlGenerator->controller($_SERVER['REQUEST_URI']),
            'action' => $this->urlGenerator->action($_SERVER['REQUEST_URI'])
        ];
        $this->view('Category', ['languages' => $languages, 'apps' => $apps, 'components' => $components, 'ft' => $ft]);
    }

    public function NewCategory()
    {
        $components = $this->config["components"];
        $this->viewComponent('NewCategory', ['components' => $components]);
    }

    public function ModifyCategory()
    {
        $components = $this->config["components"];
        $this->viewComponent('ModifyCategory', ['components' => $components]);
    }

    public function DeleteCategory()
    {
        $components = $this->config["components"];
        $this->viewComponent('DeleteCategory', ['components' => $components]);
    }
}
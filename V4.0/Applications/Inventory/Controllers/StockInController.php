<?php

namespace APP_Inventory_Controller;

use APP_Administration_Controller\Controller;
use TS_Utility\Classes\UrlGenerator;

class StockInController extends Controller
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

    // Home page
    /**
     * @throws ReflectionException
     */
    public function Index(): void
    {

    }
}
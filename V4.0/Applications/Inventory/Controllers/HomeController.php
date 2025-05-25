<?php

namespace APP_Inventory_Controller;

use API_Inventory_Controller\InventoryController;
use API_Inventory_Controller\ProductController;
use API_Inventory_Controller\StockController;
use API_Inventory_Controller\WarehouseController;
use APP_Administration_Controller\Controller;
use ReflectionException;
use TS_Utility\Classes\UrlGenerator;

class HomeController extends Controller
{
    private UrlGenerator $urlGenerator;
    private array $config;
    private WarehouseController $warehouseController;
    private ProductController $productController;
    private StockController $stockController;
    private InventoryController $inventoryController;

    public function __construct(WarehouseController $_warehouseController, ProductController $_productController, StockController $_stockController,
                                InventoryController $_inventoryController)
    {
        $this->urlGenerator = new UrlGenerator(dirname(__DIR__, 2).'\Assets\Data\json\config.json');
        parent::__construct($this->urlGenerator);

        // Set the Exception property
        $this->SetException();
        $this->config = json_decode(file_get_contents(dirname(__DIR__, 1).'\Assets\Configs\config.json'), true);

        $this->warehouseController = $_warehouseController;
        $this->productController = $_productController;
        $this->stockController = $_stockController;
        $this->inventoryController = $_inventoryController;
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
        $components = $this->config["components"];
        $component = $this->getFlashMessage('component', 'GeneralOverview');
        $ft = [
            'app' => $this->urlGenerator->application($_SERVER['REQUEST_URI']),
            'ctrl' => $this->urlGenerator->controller($_SERVER['REQUEST_URI']),
            'action' => $this->urlGenerator->action($_SERVER['REQUEST_URI'])
        ];
        $this->view('Home', ['languages' => $languages, 'apps' => $apps, 'components' => $components, 'component' => $component, 'ft' => $ft]);
    }

    /**
     * @throws ReflectionException
     */
    public function GeneralOverview(): void
    {
        $warehouses = $this->warehouseController->Get();
        $products = $this->productController->Get();
        $stocks = $this->stockController->Get();
        $inventories = $this->inventoryController->Get();
        $components = $this->config["components"];
        $languages = $this->languageController->Get();
        $this->viewComponent('GeneralOverview', ['warehouses' => $warehouses, 'products' => $products, 'stocks' => $stocks, 'inventories' => $inventories,
            'components' => $components, 'languages' => $languages]);
    }
}
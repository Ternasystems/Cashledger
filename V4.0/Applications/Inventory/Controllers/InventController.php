<?php

namespace APP_Inventory_Controller;

use API_Inventory_Controller\InventoryController;
use API_Inventory_Controller\ProductController;
use API_Inventory_Controller\StockController;
use API_Inventory_Controller\WarehouseController;
use APP_Administration_Controller\Controller;
use Exception;
use ReflectionException;
use TS_Utility\Classes\UrlGenerator;

class InventController extends Controller
{
    private UrlGenerator $urlGenerator;
    private array $config;
    private ProductController $productController;
    private WarehouseController $warehouseController;
    private StockController $stockController;
    private InventoryController $inventoryController;

    public function __construct(ProductController $_productController, WarehouseController $_warehouseController, StockController $_stockController,
                                InventoryController  $_inventoryController)
    {
        $this->urlGenerator = new UrlGenerator(dirname(__DIR__, 2).'\Assets\Data\json\config.json');
        parent::__construct($this->urlGenerator);

        // Set the Exception property
        $this->SetException();
        $this->config = json_decode(file_get_contents(dirname(__DIR__, 1).'\Assets\Configs\config.json'), true);

        $this->productController = $_productController;
        $this->warehouseController = $_warehouseController;
        $this->stockController = $_stockController;
        $this->inventoryController = $_inventoryController;
    }

    /**
     * @throws ReflectionException
     */
    public function Index(): void
    {
        $languages = $this->languageController->Get();
        $apps = $this->config["apps"];
        $components = $this->config["components"];
        $component = $this->getFlashMessage('component', 'StockInvent');
        $ft = [
            'app' => $this->urlGenerator->application($_SERVER['REQUEST_URI']),
            'ctrl' => $this->urlGenerator->controller($_SERVER['REQUEST_URI']),
            'action' => $this->urlGenerator->action($_SERVER['REQUEST_URI'])
        ];
        $this->view('Inventory', ['languages' => $languages, 'apps' => $apps, 'components' => $components, 'component' => $component, 'ft' => $ft]);
    }

    /**
     * @throws ReflectionException
     */
    public function StockInvent(): void
    {
        $languages = $this->languageController->Get();
        $stocks = $this->stockController->Get();
        $this->viewComponent('StockInvent', ['stocks' => $stocks, 'languages' => $languages]);
    }

    /**
     * @throws ReflectionException
     * @throws Exception
     */
    public function AddStockDetails(string $stockId): void
    {
        $languages = $this->languageController->Get();
        $stock = $this->stockController->GetById($stockId);
        $productId = $stock->Product()->It()->Id;
        $inventories = $this->inventoryController->GetByProductId($productId);
        $this->viewComponent('StockDetails', ['stock' => $stock, 'inventories' => $inventories, 'languages' => $languages]);
    }

    /**
     * @throws ReflectionException
     */
    public function NewStockInventory(): void
    {
        $warehouses = $this->warehouseController->Get();
        $components = $this->config["components"];
        $languages = $this->languageController->Get();
        $this->viewComponent('NewInventory', ['warehouses' => $warehouses, 'components' => $components, 'languages' => $languages]);
    }

    /**
     * @throws ReflectionException
     * @throws Exception
     */
    public function AddInventStock(string $_warehouseId): void
    {
        $stocks = $this->stockController->GetByWarehouseId($_warehouseId);
        $languages = $this->languageController->Get();
        $this->viewComponent('InventStock', ['languages' => $languages, 'stocks' => $stocks]);
    }
}
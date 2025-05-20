<?php

namespace APP_Inventory_Controller;

use API_Inventory_Controller\InventoryController;
use API_Inventory_Controller\ManufacturerController;
use API_Inventory_Controller\ProductController;
use API_Inventory_Controller\StockController;
use API_Inventory_Controller\UnitController;
use API_Inventory_Controller\WarehouseController;
use APP_Administration_Controller\Controller;
use APP_Inventory_Model\InventoryModel;
use APP_Inventory_Model\InventStockModel;
use APP_Inventory_Model\StockInventModel;
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
    private ManufacturerController $manufacturerController;
    private UnitController $unitController;

    public function __construct(ProductController $_productController, WarehouseController $_warehouseController, StockController $_stockController,
                                InventoryController  $_inventoryController, ManufacturerController $_manufacturerController, UnitController $_unitController)
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
        $this->manufacturerController = $_manufacturerController;
        $this->unitController = $_unitController;
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
        $manufacturers = $this->manufacturerController->Get();
        $this->viewComponent('StockDetails', ['stock' => $stock, 'inventories' => $inventories, 'manufacturers' => $manufacturers, 'languages' => $languages]);
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

    /**
     * @throws ReflectionException
     */
    public function AddInventory(InventStockModel $model): void
    {
        if (!isset($_SESSION['InventStockModel']) || !$model->state)
            $_SESSION['InventStockModel'] = [];
        //
        $json = json_decode($_POST['InventStockModel']);
        $model->stockid = $json->Id;
        $model->json = $_POST['InventStockModel'];
        //
        $_SESSION['InventStockModel'][] = $model;
        $languages = $this->languageController->Get();
        $product = $this->stockController->GetById($model->stockid)->Product();
        $model->productid = $product->It()->Id;
        $model->stockavailable = $this->stockController->GetById($model->stockid)->It()->Quantity;
        $unit = $this->unitController->GetById($json->UnitId);
        $this->viewComponent('InventoryItem', ['stockNumber' => count($_SESSION['InventStockModel']), 'model' => $model, 'product' => $product, 'unit' => $unit,
            'languages' => $languages]);
    }

    /**
     * @throws Exception
     */
    public function AddStockInventory(InventoryModel $model): void
    {
        $stocks = [];
        foreach ($_POST['StockModel'] as $json){
            $obj = json_decode($json);
            $obj->inventorydate = $obj->inventorydate->date;
            $obj->state = $obj->state ? 'true' : 'false';
            $stockModel = new InventStockModel();
            $stockModel = $this->Mapping($stockModel, (array)$obj);
            $stockObj = json_decode($stockModel->json);
            //
            $inventModel = new StockInventModel();
            $inventModel->stockid = $stockModel->stockid;
            $inventModel->unitid = $stockObj->UnitId;
            $inventModel->partnerid = $_SESSION['CredentialId'];
            $inventModel->inventorytype = 'INVENT';
            $inventModel->quantity = $stockModel->stockavailable - $stockModel->stockquantity;
            $inventModel->unitcost = $stockObj->UnitCost;
            $inventModel->credentialid = $_SESSION['CredentialId'];
            //
            $stockModel->stockinvent = $inventModel;
            $stocks[$stockModel->stockid] = $stockModel;
        }
        $model->stocks = $stocks;
        $this->stockController->SetInventories($model);
        $this->setFlashMessage('component', 'NewStockInventory');
        $this->redirectToAction('Index');
    }
}
<?php

namespace APP_Inventory_Controller;

use API_Inventory_Controller\CustomerController;
use API_Inventory_Controller\InventoryController;
use API_Inventory_Controller\ManufacturerController;
use API_Inventory_Controller\PackagingController;
use API_Inventory_Controller\ProductController;
use API_Inventory_Controller\StockController;
use API_Inventory_Controller\SupplierController;
use API_Inventory_Controller\UnitController;
use API_Inventory_Controller\WarehouseController;
use APP_Administration_Controller\Controller;
use APP_Inventory_Model\StockDispatchModel;
use APP_Inventory_Model\StockInventModel;
use APP_Inventory_Model\StockItemModel;
use Exception;
use ReflectionException;
use TS_Utility\Classes\UrlGenerator;

class StockOutController extends Controller
{
    private UrlGenerator $urlGenerator;
    private array $config;
    private ProductController $productController;
    private WarehouseController $warehouseController;
    private ManufacturerController $manufacturerController;
    private UnitController $unitController;
    private PackagingController $packagingController;
    private SupplierController $supplierController;
    private CustomerController $customerController;
    private StockController $stockController;
    private InventoryController $inventoryController;

    public function __construct(ProductController $_productController, WarehouseController $_warehouseController, ManufacturerController $_manufacturerController,
                                UnitController $_unitController, PackagingController $_packagingController, SupplierController $_supplierController,
                                CustomerController $_customerController, StockController $_stockController, InventoryController $_inventoryController)
    {
        $this->urlGenerator = new UrlGenerator(dirname(__DIR__, 2).'\Assets\Data\json\config.json');
        parent::__construct($this->urlGenerator);

        // Set the Exception property
        $this->SetException();
        $this->config = json_decode(file_get_contents(dirname(__DIR__, 1).'\Assets\Configs\config.json'), true);

        $this->productController = $_productController;
        $this->warehouseController = $_warehouseController;
        $this->manufacturerController = $_manufacturerController;
        $this->unitController = $_unitController;
        $this->packagingController = $_packagingController;
        $this->supplierController = $_supplierController;
        $this->customerController = $_customerController;
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
        $component = $this->getFlashMessage('component', 'DispatchList');
        $ft = [
            'app' => $this->urlGenerator->application($_SERVER['REQUEST_URI']),
            'ctrl' => $this->urlGenerator->controller($_SERVER['REQUEST_URI']),
            'action' => $this->urlGenerator->action($_SERVER['REQUEST_URI'])
        ];
        $this->view('StockOut', ['languages' => $languages, 'apps' => $apps, 'components' => $components, 'component' => $component, 'ft' => $ft]);
    }

    /**
     * @throws ReflectionException
     */
    public function DispatchList(): void
    {
        $dispatches = $this->stockController->GetDispatches();
        $inventories = $this->inventoryController->Get();
        $this->viewComponent('DispatchList', ['dispatches' => $dispatches, 'inventories' => $inventories]);
    }

    /**
     * @throws ReflectionException
     * @throws Exception
     */
    public function AddListItem(string $dispatchId): void
    {
        $inventories = $this->inventoryController->GetByDispatchId($dispatchId);
        $stocks = $this->stockController->Get();
        $products = $this->productController->Get();
        $customers = $this->customerController->Get();
        $units = $this->unitController->Get();
        $languages = $this->languageController->Get();
        $this->viewComponent('DispatchItems', ['inventories' => $inventories, 'stocks' => $stocks, 'products' => $products, 'customers' => $customers, 'units' => $units,
            'languages' => $languages]);
    }

    /**
     * @throws ReflectionException
     */
    public function NewStockDispatch(): void
    {
        $products = $this->productController->Get();
        $units = $this->unitController->Get();
        $warehouses = $this->warehouseController->Get();
        $manufacturers = $this->manufacturerController->Get();
        $packagings = $this->packagingController->Get();
        $suppliers = $this->supplierController->Get();
        $customers = $this->customerController->Get();
        $components = $this->config["components"];
        $languages = $this->languageController->Get();
        $this->viewComponent('NewStockDispatch', ['languages' => $languages, 'products' => $products, 'units' => $units, 'warehouses' => $warehouses,
            'manufacturers' => $manufacturers, 'packagings' => $packagings, 'suppliers' => $suppliers, 'customers' => $customers, 'components' => $components]);
    }

    /**
     * @throws ReflectionException
     * @throws Exception
     */
    public function AddStockItem(string $_productId): void
    {
        $stocks = $this->stockController->GetByProductId($_productId);
        $languages = $this->languageController->Get();
        $this->viewComponent('StockItem', ['languages' => $languages, 'stocks' => $stocks]);
    }

    /**
     * @throws ReflectionException
     */
    public function RemoveStock(StockItemModel $model): void
    {
        if (!isset($_SESSION['StockItemModel']) || !$model->state)
            $_SESSION['StockItemModel'] = [];
        //
        $json = json_decode($_POST['StockItemModel']);
        $model->stockid = $json->Id;
        $model->json = $_POST['StockItemModel'];
        //
        $_SESSION['StockItemModel'][] = $model;
        $languages = $this->languageController->Get();
        $product = $this->productController->GetById($model->productid);
        $warehouse = $this->warehouseController->GetById($json->WarehouseId);
        $packaging = $this->packagingController->GetById($json->PackagingId);
        $unit = $this->unitController->GetById($json->UnitId);
        $customer = $this->customerController->GetById($model->customerid);
        $this->viewComponent('DispatchItem', ['stockNumber' => count($_SESSION['StockItemModel']), 'model' => $model, 'product' => $product, 'unit' => $unit,
            'warehouse' => $warehouse, 'packaging' => $packaging, 'customer' => $customer,'languages' => $languages]);
    }

    /**
     * @throws Exception
     */
    public function AddStockDispatch(StockDispatchModel $model): void
    {
        $stocks = [];
        foreach ($_POST['StockModel'] as $json) {
            $obj = json_decode($json);
            $obj->dispatchdate = $obj->dispatchdate->date;
            $obj->state = $obj->state ? 'true' : 'false';
            $stockModel = new StockItemModel();
            $stockModel = $this->Mapping($stockModel, (array)$obj);
            $stockObj = json_decode($stockModel->json);
            //
            $inventModel = new StockInventModel();
            $inventModel->unitid = $stockObj->UnitId;
            $inventModel->quantity = $obj->stockquantity;
            $inventModel->unitcost = $stockObj->UnitCost;
            $inventModel->inventorytype = 'OUT';
            $inventModel->partnerid = $obj->customerid;
            $inventModel->credentialid = $_SESSION['CredentialId'];
            //
            $stockModel->batchnumber = $stockObj->BatchNumber;
            $stockModel->unitid = $stockObj->UnitId;
            $stockModel->warehouseid = $stockObj->WarehouseId;
            $stockModel->packagingid = $stockObj->PackagingId;
            $stockModel->unitcost = $stockObj->UnitCost;
            $stockModel->stockinvent = $inventModel;
            $stocks[$stockModel->stockid][] = $stockModel;
        }
        $model->stocks = $stocks;
        $this->stockController->SetDispatch($model);
        $this->setFlashMessage('component', 'NewStockDispatch');
        $this->redirectToAction('Index');
    }
}
<?php

namespace APP_Inventory_Controller;

use API_Inventory_Controller\InventoryController;
use API_Inventory_Controller\ManufacturerController;
use API_Inventory_Controller\PackagingController;
use API_Inventory_Controller\ProductController;
use API_Inventory_Controller\StockController;
use API_Inventory_Controller\SupplierController;
use API_Inventory_Controller\UnitController;
use API_Inventory_Controller\WarehouseController;
use APP_Administration_Controller\Controller;
use APP_Inventory_Model\StockDeliveryModel;
use APP_Inventory_Model\StockInventModel;
use APP_Inventory_Model\StockModel;
use Exception;
use ReflectionException;
use TS_Utility\Classes\UrlGenerator;

class StockInController extends Controller
{
    private UrlGenerator $urlGenerator;
    private array $config;
    private ProductController $productController;
    private WarehouseController $warehouseController;
    private ManufacturerController $manufacturerController;
    private UnitController $unitController;
    private PackagingController $packagingController;
    private SupplierController $supplierController;
    private StockController $stockController;
    private InventoryController $inventoryController;

    public function __construct(ProductController $_productController, WarehouseController $_warehouseController, ManufacturerController $_manufacturerController,
                                UnitController $_unitController, PackagingController $_packagingController, SupplierController $_supplierController,
                                StockController $_stockController, InventoryController $_inventoryController)
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
        $component = $this->getFlashMessage('component', 'DeliveryList');
        $ft = [
            'app' => $this->urlGenerator->application($_SERVER['REQUEST_URI']),
            'ctrl' => $this->urlGenerator->controller($_SERVER['REQUEST_URI']),
            'action' => $this->urlGenerator->action($_SERVER['REQUEST_URI'])
        ];
        $this->view('StockIn', ['languages' => $languages, 'apps' => $apps, 'components' => $components, 'component' => $component, 'ft' => $ft]);
    }

    /**
     * @throws ReflectionException
     */
    public function DeliveryList(): void
    {
        $deliveries = $this->stockController->GetDeliveries();
        $inventories = $this->inventoryController->Get();
        $this->viewComponent('DeliveryList', ['deliveries' => $deliveries, 'inventories' => $inventories]);
    }

    /**
     * @throws ReflectionException
     * @throws Exception
     */
    public function AddListItem(string $deliveryId): void
    {
        $inventories = $this->inventoryController->GetByDeliveryId($deliveryId);
        $stocks = $this->stockController->Get();
        $products = $this->productController->Get();
        $units = $this->unitController->Get();
        $languages = $this->languageController->Get();
        $this->viewComponent('DeliveryItems', ['inventories' => $inventories, 'stocks' => $stocks, 'products' => $products, 'units' => $units, 'languages' => $languages]);
    }

    /**
     * @throws ReflectionException
     */
    public function NewStockDelivery(): void
    {
        $products = $this->productController->Get();
        $attributes = $this->productController->GetAttributes();
        $units = $this->unitController->Get();
        $warehouses = $this->warehouseController->Get();
        $manufacturers = $this->manufacturerController->Get();
        $packagings = $this->packagingController->Get();
        $suppliers = $this->supplierController->Get();
        $components = $this->config["components"];
        $languages = $this->languageController->Get();
        $this->viewComponent('NewStockDelivery', ['languages' => $languages, 'products' => $products, 'attributes' => $attributes, 'units' => $units,
            'warehouses' => $warehouses, 'manufacturers' => $manufacturers, 'packagings' => $packagings, 'suppliers' => $suppliers, 'components' => $components]);
    }

    /**
     * @throws ReflectionException
     */
    public function AddStock(StockModel $model): void
    {
        if (!isset($_SESSION['StockModel']) || !$model->state)
            $_SESSION['StockModel'] = [];
        //
        $_SESSION['StockModel'][] = $model;
        $languages = $this->languageController->Get();
        $product = $this->productController->GetById($model->productid);
        $unit = $this->unitController->GetById($model->unitid);
        $warehouse = $this->warehouseController->GetById($model->warehouseid);
        $packaging = $this->packagingController->GetById($model->packagingid);
        $this->viewComponent('StockElement', ['stockNumber' => count($_SESSION['StockModel']), 'model' => $model, 'product' => $product, 'unit' => $unit,
            'warehouse' => $warehouse, 'packaging' => $packaging,'languages' => $languages]);
    }

    /**
     * @throws Exception
     */
    public function AddStockDelivery(StockDeliveryModel $model): void
    {
        $warehouses = [];
        foreach ($_POST['StockModel'] as $json) {
            $obj = json_decode($json);
            $obj->deliverydate = $obj->deliverydate->date;
            $obj->state = $obj->state ? 'true' : 'false';
            $inventModel = new StockInventModel();
            $inventModel->unitid = $obj->unitid;
            $inventModel->quantity = $obj->stockquantity;
            $inventModel->unitcost = $obj->unitcost;
            $inventModel->inventorytype = 'IN';
            $inventModel->partnerid = $model->supplierid;
            $inventModel->credentialid = $_SESSION['CredentialId'];
            $stockModel = new StockModel();
            $stockModel = $this->Mapping($stockModel, (array)$obj);
            $stockModel->stockinvent = $inventModel;
            $warehouses[$stockModel->warehouseid][] = $stockModel;
        }
        $model->warehouses = $warehouses;
        $this->stockController->SetDelivery($model);
        $this->setFlashMessage('component', 'NewStockDelivery');
        $this->redirectToAction('Index');
    }
}
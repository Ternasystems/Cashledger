<?php

namespace APP_Inventory_Controller;

use API_Inventory_Controller\ManufacturerController;
use API_Inventory_Controller\PackagingController;
use API_Inventory_Controller\ProductController;
use API_Inventory_Controller\UnitController;
use API_Inventory_Controller\WarehouseController;
use APP_Administration_Controller\Controller;
use APP_Inventory_Model\ManufacturerModel;
use APP_Inventory_Model\PackagingModel;
use APP_Inventory_Model\ProductAttributeModel;
use APP_Inventory_Model\ProductCategoryModel;
use APP_Inventory_Model\ProductModel;
use APP_Inventory_Model\UnitModel;
use APP_Inventory_Model\WarehouseModel;
use Exception;
use ReflectionException;
use TS_Domain\Classes\Linq;
use TS_Utility\Classes\UrlGenerator;

class ConfigController extends Controller
{
    private UrlGenerator $urlGenerator;
    private array $config;
    private ProductController $productController;
    private WarehouseController $warehouseController;
    private ManufacturerController $manufacturerController;
    private UnitController $unitController;
    private PackagingController $packagingController;

    public function __construct(ProductController $_productController, WarehouseController $_warehouseController, ManufacturerController $_manufacturerController,
    UnitController $_unitController, PackagingController $_packagingController)
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
    }

    /* Methods */

    // Load Categories
    /**
     * @throws Exception
     */
    public function LoadCategory(string $_categoryId): void
    {
        $category = $this->productController->GetCategoryById($_categoryId);
        $relations = $category->LanguageRelations();
        $languages = $this->languageController->Get();
        $arr = ['Id' => $category->It()->Id, 'Name' => $category->It()->Name, 'Description' => $category->It()->Description];
        foreach ($relations as $relation) {
            $langId = $relation->LangId;
            $label = $languages->FirstOrDefault(fn($n) => $n->It()->Id == $langId)->It()->Label;
            $arr[$label] = $relation->Label;
        }
        echo json_encode($arr);
        exit;
    }

    /* Actions */

    // Category page
    /**
     * @throws ReflectionException
     */
    public function Category(): void
    {
        $languages = $this->languageController->Get();
        $apps = $this->config["apps"];
        $components = $this->config["components"];
        $component = $this->getFlashMessage('component', 'NewCategory');
        $ft = [
            'app' => $this->urlGenerator->application($_SERVER['REQUEST_URI']),
            'ctrl' => $this->urlGenerator->controller($_SERVER['REQUEST_URI']),
            'action' => $this->urlGenerator->action($_SERVER['REQUEST_URI'])
        ];
        $this->view('Category', ['languages' => $languages, 'apps' => $apps, 'components' => $components, 'component' => $component, 'ft' => $ft]);
    }

    /**
     * @throws ReflectionException
     */
    public function NewCategory(): void
    {
        $categories = $this->productController->GetCategories();
        $components = $this->config["components"];
        $languages = $this->languageController->Get();
        $this->viewComponent('NewCategory', ['languages' => $languages, 'categories' => $categories, 'components' => $components]);
    }

    /**
     * @throws ReflectionException
     */
    public function ModifyCategory(): void
    {
        $categories = $this->productController->GetCategories();
        $components = $this->config["components"];
        $languages = $this->languageController->Get();
        $this->viewComponent('ModifyCategory', ['languages' => $languages, 'categories' => $categories, 'components' => $components]);
    }

    /**
     * @throws ReflectionException
     */
    public function DeleteCategory(): void
    {
        $categories = $this->productController->GetCategories();
        $components = $this->config["components"];
        $languages = $this->languageController->Get();
        $this->viewComponent('DeleteCategory', ['languages' => $languages, 'categories' => $categories, 'components' => $components]);
    }

    /**
     * @throws Exception
     */
    public function AddCategory(ProductCategoryModel $model): void
    {
        $this->productController->SetCategory($model);
        $this->redirectToAction('Category');
    }

    /**
     * @throws Exception
     */
    public function UpdateCategory(ProductCategoryModel $model): void
    {
        $this->productController->PutCategory($model);
        $this->setFlashMessage('component', 'ModifyCategory');
        $this->redirectToAction('Category');
    }

    public function RemoveCategory(string $categoryId): void
    {
        $this->productController->DeleteCategory($categoryId);
    }

    // Load Attributes

    /**
     * @throws Exception
     */
    public function LoadAttribute(string $_attributeId): void
    {
        $attribute = $this->productController->GetAttributeById($_attributeId);
        $linq = new Linq();
        $constraintType = $linq->constraintType($attribute->It()->AttributeConstraint);
        $constraint = $constraintType != 'none' ? $linq->linq($constraintType, $attribute->It()->AttributeConstraint) : $attribute->It()->AttributeConstraint;
        $relations = $attribute->LanguageRelations();
        $languages = $this->languageController->Get();
        $arr = ['Id' => $attribute->It()->Id, 'Name' => $attribute->It()->Name, 'AttributeType' => $attribute->It()->AttributeType, 'ConstraintType' => $constraintType,
            'AttributeConstraint' => $constraint, 'Description' => $attribute->It()->Description];
        foreach ($relations as $relation) {
            $langId = $relation->LangId;
            $label = $languages->FirstOrDefault(fn($n) => $n->It()->Id == $langId)->It()->Label;
            $arr[$label] = $relation->Label;
        }
        echo json_encode($arr);
        exit;
    }

    /* Actions */

    // Attribute page
    /**
     * @throws ReflectionException
     */
    public function Attribute(): void
    {
        $languages = $this->languageController->Get();
        $apps = $this->config["apps"];
        $components = $this->config["components"];
        $component = $this->getFlashMessage('component', 'NewAttribute');
        $ft = [
            'app' => $this->urlGenerator->application($_SERVER['REQUEST_URI']),
            'ctrl' => $this->urlGenerator->controller($_SERVER['REQUEST_URI']),
            'action' => $this->urlGenerator->action($_SERVER['REQUEST_URI'])
        ];
        $this->view('Attribute', ['languages' => $languages, 'apps' => $apps, 'components' => $components, 'component' => $component, 'ft' => $ft]);
    }

    /**
     * @throws ReflectionException
     */
    public function NewAttribute(): void
    {
        $attributes = $this->productController->GetAttributes();
        $constraints = $this->config["constraints"];
        $components = $this->config["components"];
        $languages = $this->languageController->Get();
        $this->viewComponent('NewAttribute', ['languages' => $languages, 'attributes' => $attributes, 'constraints' => $constraints, 'components' => $components]);
    }

    /**
     * @throws ReflectionException
     */
    public function ModifyAttribute(): void
    {
        $attributes = $this->productController->GetAttributes();
        $constraints = $this->config["constraints"];
        $components = $this->config["components"];
        $languages = $this->languageController->Get();
        $this->viewComponent('ModifyAttribute', ['languages' => $languages, 'attributes' => $attributes, 'constraints' => $constraints, 'components' => $components]);
    }

    /**
     * @throws ReflectionException
     */
    public function DeleteAttribute(): void
    {
        $attributes = $this->productController->GetAttributes();
        $components = $this->config["components"];
        $languages = $this->languageController->Get();
        $this->viewComponent('DeleteAttribute', ['languages' => $languages, 'attributes' => $attributes, 'components' => $components]);
    }

    /**
     * @throws Exception
     */
    public function AddAttribute(ProductAttributeModel $model): void
    {
        $this->productController->SetAttribute($model);
        $this->redirectToAction('Attribute');
    }

    /**
     * @throws Exception
     */
    public function UpdateAttribute(ProductAttributeModel $model): void
    {
        $this->productController->PutAttribute($model);
        $this->setFlashMessage('component', 'ModifyAttribute');
        $this->redirectToAction('Attribute');
    }

    public function RemoveAttribute(string $attributeId): void
    {
        $this->productController->DeleteAttribute($attributeId);
    }

    // Load Products
    public function LoadProduct(string $_productId): void
    {
        $product = $this->productController->GetById($_productId);
        $arr = ['Id' => $product->It()->Id, 'Name' => $product->It()->Name, 'CategoryId' => $product->It()->CategoryId, 'UnitId' => $product->It()->UnitId,
            $product->It()->MinStock, $product->It()->MaxStock, 'Description' => $product->It()->Description];
        echo json_encode($arr);
        exit;
    }

    /* Actions */

    // Product page
    /**
     * @throws ReflectionException
     */
    public function Product(): void
    {
        $languages = $this->languageController->Get();
        $apps = $this->config["apps"];
        $components = $this->config["components"];
        $component = $this->getFlashMessage('component', 'NewProduct');
        $ft = [
            'app' => $this->urlGenerator->application($_SERVER['REQUEST_URI']),
            'ctrl' => $this->urlGenerator->controller($_SERVER['REQUEST_URI']),
            'action' => $this->urlGenerator->action($_SERVER['REQUEST_URI'])
        ];
        $this->view('Product', ['languages' => $languages, 'apps' => $apps, 'components' => $components, 'component' => $component, 'ft' => $ft]);
    }

    /**
     * @throws ReflectionException
     */
    public function NewProduct(): void
    {
        $products = $this->productController->Get();
        $categories = $this->productController->GetCategories();
        $attributes = $this->productController->GetAttributes();
        $units = $this->unitController->Get();
        $components = $this->config["components"];
        $languages = $this->languageController->Get();
        $this->viewComponent('NewProduct', ['languages' => $languages, 'products' => $products, 'categories' => $categories, 'units' => $units,
            'attributes' => $attributes, 'components' => $components]);
    }

    /**
     * @throws ReflectionException
     */
    public function ModifyProduct(): void
    {
        $products = $this->productController->Get();
        $components = $this->config["components"];
        $languages = $this->languageController->Get();
        $this->viewComponent('ModifyProduct', ['languages' => $languages, 'products' => $products, 'components' => $components]);
    }

    /**
     * @throws ReflectionException
     */
    public function DeleteProduct(): void
    {
        $products = $this->productController->Get();
        $components = $this->config["components"];
        $languages = $this->languageController->Get();
        $this->viewComponent('DeleteProduct', ['languages' => $languages, 'products' => $products, 'components' => $components]);
    }

    /**
     * @throws Exception
     */
    public function AddProduct(ProductModel $model): void
    {
        var_dump($_POST);
        /*$this->productController->Set($model);
        $this->redirectToAction('Product');*/
    }

    /**
     * @throws Exception
     */
    public function UpdateProduct(ProductModel $model): void
    {
        $this->productController->Put($model);
        $this->setFlashMessage('component', 'ModifyProduct');
        $this->redirectToAction('Product');
    }

    public function RemoveProduct(string $productId): void
    {
        $this->productController->Delete($productId);
    }

    // Load Warehouses
    public function LoadWarehouse(string $_warehouseId): void
    {
        $warehouse = $this->warehouseController->GetById($_warehouseId);
        $arr = ['Id' => $warehouse->It()->Id, 'Name' => $warehouse->It()->Name, 'Location' => $warehouse->It()->Location, 'Description' => $warehouse->It()->Description];
        echo json_encode($arr);
        exit;
    }

    /**
     * @throws ReflectionException
     */
    public function Warehouse(): void
    {
        $languages = $this->languageController->Get();
        $apps = $this->config["apps"];
        $components = $this->config["components"];
        $component = $this->getFlashMessage('component', 'NewWarehouse');
        $ft = [
            'app' => $this->urlGenerator->application($_SERVER['REQUEST_URI']),
            'ctrl' => $this->urlGenerator->controller($_SERVER['REQUEST_URI']),
            'action' => $this->urlGenerator->action($_SERVER['REQUEST_URI'])
        ];
        $this->view('Warehouse', ['languages' => $languages, 'apps' => $apps, 'components' => $components, 'component' => $component, 'ft' => $ft]);
    }

    /**
     * @throws ReflectionException
     */
    public function NewWarehouse(): void
    {
        $warehouses = $this->warehouseController->Get();
        $components = $this->config["components"];
        $this->viewComponent('NewWarehouse', ['warehouses' => $warehouses, 'components' => $components]);
    }

    /**
     * @throws ReflectionException
     */
    public function ModifyWarehouse(): void
    {
        $warehouses = $this->warehouseController->Get();
        $components = $this->config["components"];
        $this->viewComponent('ModifyWarehouse', ['warehouses' => $warehouses, 'components' => $components]);
    }

    /**
     * @throws ReflectionException
     */
    public function DeleteWarehouse(): void
    {
        $warehouses = $this->warehouseController->Get();
        $components = $this->config["components"];
        $this->viewComponent('DeleteWarehouse', ['warehouses' => $warehouses, 'components' => $components]);
    }

    /**
     * @throws Exception
     */
    public function AddWarehouse(WarehouseModel $model): void
    {
        $this->warehouseController->Set($model);
        $this->redirectToAction('Warehouse');
    }

    /**
     * @throws Exception
     */
    public function UpdateWarehouse(WarehouseModel $model): void
    {
        $this->warehouseController->Put($model);
        $this->setFlashMessage('component', 'ModifyWarehouse');
        $this->redirectToAction('Warehouse');
    }

    public function RemoveWarehouse(string $warehouseId): void
    {
        $this->warehouseController->Delete($warehouseId);
    }

    // Load Manufacturer
    public function LoadManufacturer(string $_manufacturerId): void
    {
        $manufacturer = $this->manufacturerController->GetById($_manufacturerId);
        $arr = ['Id' => $manufacturer->It()->Id, 'Name' => $manufacturer->It()->Name, 'Description' => $manufacturer->It()->Description];
        echo json_encode($arr);
        exit;
    }

    /**
     * @throws ReflectionException
     */
    public function Manufacturer(): void
    {
        $languages = $this->languageController->Get();
        $apps = $this->config["apps"];
        $components = $this->config["components"];
        $component = $this->getFlashMessage('component', 'NewManufacturer');
        $ft = [
            'app' => $this->urlGenerator->application($_SERVER['REQUEST_URI']),
            'ctrl' => $this->urlGenerator->controller($_SERVER['REQUEST_URI']),
            'action' => $this->urlGenerator->action($_SERVER['REQUEST_URI'])
        ];
        $this->view('Manufacturer', ['languages' => $languages, 'apps' => $apps, 'components' => $components, 'component' => $component, 'ft' => $ft]);
    }

    /**
     * @throws ReflectionException
     */
    public function NewManufacturer(): void
    {
        $manufacturers = $this->manufacturerController->Get();
        $components = $this->config["components"];
        $this->viewComponent('NewManufacturer', ['manufacturers' => $manufacturers, 'components' => $components]);
    }

    /**
     * @throws ReflectionException
     */
    public function ModifyManufacturer(): void
    {
        $manufacturers = $this->manufacturerController->Get();
        $components = $this->config["components"];
        $this->viewComponent('ModifyManufacturer', ['manufacturers' => $manufacturers, 'components' => $components]);
    }

    /**
     * @throws ReflectionException
     */
    public function DeleteManufacturer(): void
    {
        $manufacturers = $this->manufacturerController->Get();
        $components = $this->config["components"];
        $this->viewComponent('DeleteManufacturer', ['manufacturers' => $manufacturers, 'components' => $components]);
    }

    /**
     * @throws Exception
     */
    public function AddManufacturer(ManufacturerModel $model): void
    {
        $this->manufacturerController->Set($model);
        $this->redirectToAction('Manufacturer');
    }

    /**
     * @throws Exception
     */
    public function UpdateManufacturer(ManufacturerModel $model): void
    {
        $this->manufacturerController->Put($model);
        $this->setFlashMessage('component', 'ModifyManufacturer');
        $this->redirectToAction('Manufacturer');
    }

    public function RemoveManufacturer(string $manufacturerId): void
    {
        $this->manufacturerController->Delete($manufacturerId);
    }

    // Load Units

    /**
     * @throws Exception
     */
    public function LoadUnit(string $_unitId): void
    {
        $unit = $this->unitController->GetById($_unitId);
        $relations = $unit->LanguageRelations();
        $languages = $this->languageController->Get();
        $arr = ['Id' => $unit->It()->Id, 'Name' => $unit->It()->Name, 'Label' => $unit->It()->Label, 'Description' => $unit->It()->Description];
        foreach ($relations as $relation) {
            $langId = $relation->LangId;
            $label = $languages->FirstOrDefault(fn($n) => $n->It()->Id == $langId)->It()->Label;
            $arr[$label] = $relation->Label;
        }
        echo json_encode($arr);
        exit;
    }

    /**
     * @throws ReflectionException
     */
    public function Unit(): void
    {
        $languages = $this->languageController->Get();
        $apps = $this->config["apps"];
        $components = $this->config["components"];
        $component = $this->getFlashMessage('component', 'NewUnit');
        $ft = [
            'app' => $this->urlGenerator->application($_SERVER['REQUEST_URI']),
            'ctrl' => $this->urlGenerator->controller($_SERVER['REQUEST_URI']),
            'action' => $this->urlGenerator->action($_SERVER['REQUEST_URI'])
        ];
        $this->view('Unit', ['languages' => $languages, 'apps' => $apps, 'components' => $components, 'component' => $component, 'ft' => $ft]);
    }

    /**
     * @throws ReflectionException
     */
    public function NewUnit(): void
    {
        $units = $this->unitController->Get();
        $components = $this->config["components"];
        $languages = $this->languageController->Get();
        $this->viewComponent('NewUnit', ['units' => $units, 'languages' => $languages, 'components' => $components]);
    }

    /**
     * @throws ReflectionException
     */
    public function ModifyUnit(): void
    {
        $units = $this->unitController->Get();
        $components = $this->config["components"];
        $languages = $this->languageController->Get();
        $this->viewComponent('ModifyUnit', ['units' => $units, 'languages' => $languages, 'components' => $components]);
    }

    /**
     * @throws ReflectionException
     */
    public function DeleteUnit(): void
    {
        $units = $this->unitController->Get();
        $components = $this->config["components"];
        $languages = $this->languageController->Get();
        $this->viewComponent('DeleteUnit', ['units' => $units, 'languages' => $languages, 'components' => $components]);
    }

    /**
     * @throws Exception
     */
    public function AddUnit(UnitModel $model): void
    {
        $this->unitController->Set($model);
        $this->redirectToAction('Unit');
    }

    /**
     * @throws Exception
     */
    public function UpdateUnit(UnitModel $model): void
    {
        $this->unitController->Put($model);
        $this->setFlashMessage('component', 'ModifyUnit');
        $this->redirectToAction('Unit');
    }

    public function RemoveUnit(string $unitId): void
    {
        $this->unitController->Delete($unitId);
    }

    // Load Packagings

    /**
     * @throws Exception
     */
    public function LoadPackaging(string $_packagingId): void
    {
        $packaging = $this->packagingController->GetById($_packagingId);
        $relations = $packaging->LanguageRelations();
        $languages = $this->languageController->Get();
        $arr = ['Id' => $packaging->It()->Id, 'Name' => $packaging->It()->Name, 'Description' => $packaging->It()->Description];
        foreach ($relations as $relation) {
            $langId = $relation->LangId;
            $label = $languages->FirstOrDefault(fn($n) => $n->It()->Id == $langId)->It()->Label;
            $arr[$label] = $relation->Label;
        }
        echo json_encode($arr);
        exit;
    }

    /**
     * @throws ReflectionException
     */
    public function Packaging(): void
    {
        $languages = $this->languageController->Get();
        $apps = $this->config["apps"];
        $components = $this->config["components"];
        $component = $this->getFlashMessage('component', 'NewPackaging');
        $ft = [
            'app' => $this->urlGenerator->application($_SERVER['REQUEST_URI']),
            'ctrl' => $this->urlGenerator->controller($_SERVER['REQUEST_URI']),
            'action' => $this->urlGenerator->action($_SERVER['REQUEST_URI'])
        ];
        $this->view('Packaging', ['languages' => $languages, 'apps' => $apps, 'components' => $components, 'component' => $component, 'ft' => $ft]);
    }

    /**
     * @throws ReflectionException
     */
    public function NewPackaging(): void
    {
        $packagings = $this->packagingController->Get();
        $components = $this->config["components"];
        $languages = $this->languageController->Get();
        $this->viewComponent('NewPackaging', ['packagings' => $packagings, 'languages' => $languages, 'components' => $components]);
    }

    /**
     * @throws ReflectionException
     */
    public function ModifyPackaging(): void
    {
        $packagings = $this->packagingController->Get();
        $components = $this->config["components"];
        $languages = $this->languageController->Get();
        $this->viewComponent('ModifyPackaging', ['packagings' => $packagings, 'languages' => $languages, 'components' => $components]);
    }

    /**
     * @throws ReflectionException
     */
    public function DeletePackaging(): void
    {
        $packagings = $this->packagingController->Get();
        $components = $this->config["components"];
        $languages = $this->languageController->Get();
        $this->viewComponent('DeletePackaging', ['packagings' => $packagings, 'languages' => $languages, 'components' => $components]);
    }

    /**
     * @throws Exception
     */
    public function AddPackaging(PackagingModel $model): void
    {
        $this->packagingController->Set($model);
        $this->redirectToAction('Packaging');
    }

    /**
     * @throws Exception
     */
    public function UpdatePackaging(PackagingModel $model): void
    {
        $this->packagingController->Put($model);
        $this->setFlashMessage('component', 'ModifyPackaging');
        $this->redirectToAction('Packaging');
    }

    public function RemovePackaging(string $packagingId): void
    {
        $this->packagingController->Delete($packagingId);
    }
}
<?php
/*
 * This website is powered by Ternary Data Systems.
 * Ternary Data Systems is the technology department of the Jéoline company
 * This website is based on the Hypergates framework.
 */

declare(strict_types=1);

namespace TS_Controller\Classes;

header('Content-Type: text/html; charset=utf-8');

use DateTime;
use Exception;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionProperty;
use TS_Configuration\Classes\AbstractCls;
use TS_Exception\Classes\UtilsException;
use TS_Utility\Classes\UrlGenerator;

/*
 * BaseController class
 */

abstract class BaseController extends AbstractCls
{
    private array $baseTypes = ['string', 'array', 'int', 'float', 'bool'];
    private UrlGenerator $urlGenerator;

    public function __construct(UrlGenerator $_urlGenerator) {
        // Initialize the URL generator with the configuration file
        $this->urlGenerator = $_urlGenerator;
    }

    /* Inherited protected methods */

    // Method to set the exception property
    protected function setException(): void
    {
        $this->exception = new UtilsException();
    }

    // Method to get the exception property
    public function getException(): void
    {
        throw $this->exception;
    }

    /* Protected methods */
    /**
     * @throws ReflectionException
     */
    protected function view(string $viewName, array $data = []): void
    {
        global $ViewData;
        $ViewData = array_merge($ViewData ?? [], $data);
        $calledClass = get_called_class();
        $appDir = dirname((new ReflectionClass($calledClass))->getFileName(), 2);
        $viewPath = $appDir . '\Views\Templates\\' . $viewName . '.php';
        require $viewPath;
    }

    /**
     * @throws ReflectionException
     */
    protected function viewComponent(string $viewName, array $data = []): void
    {
        global $ViewData;
        $ViewData = array_merge($ViewData ?? [], $data);
        $calledClass = get_called_class();
        $appDir = dirname((new ReflectionClass($calledClass))->getFileName(), 2);
        $viewPath = $appDir . '\Views\ViewComponents\\' . $viewName . '.php';
        require $viewPath;
    }

    protected function setFlashMessage(string $key, mixed $value): void
    {
        $_SESSION['_flash'][$key] = $value;
    }

    protected function getFlashMessage(string $key, mixed $default = null): mixed
    {
        $value = $_SESSION['_flash'][$key] ?? $default;
        unset($_SESSION['_flash'][$key]);
        return $value;
    }

    /**
     * @throws Exception
     */
    protected function redirectToAction(string $action, ?string $controller = null, ?string $app = null): void
    {
        // Get the current URL and extract the language (if not provided)
        $currentUrl = $_SERVER['REQUEST_URI'];
        $language = $this->urlGenerator->language($currentUrl); // Default to 'en' if language is not found
        $application = $app ?? $this->urlGenerator->application($currentUrl);

        // Use current class and action as defaults if not provided
        $controller = $controller ?? (new ReflectionClass($this))->getShortName();
        $controller = str_replace('Controller', '', $controller); // Remove "Controller" suffix

        // Prepare the parameters for URL generation
        $params = [
            'language' => $language,
            'application' => $application,
            'controller' => $controller,
            'action' => $action
        ];

        // Generate the URL using the URL generator
        $url = $this->urlGenerator->generate($params);

        // Perform the redirect
        header("Location: $url");
        exit;
    }

    /**
     * @throws Exception
     */
    protected function beforeAction(string $action): mixed
    {
        if (!method_exists($this, $action))
            throw new Exception('Action not found');

        $reflector = new ReflectionMethod($this, $action);
        $parameters = $reflector->getParameters();

        if (count($parameters) == 0)
            return null;
        else{
            $dependency = $parameters[0]->getType()->getName();
            return in_array($dependency, $this->baseTypes) ? $parameters[0]->getName() : new $dependency();
        }
    }

    /**
     * @throws Exception
     */
    protected function Mapping(object $model, array $data): ?object
    {
        if (!class_exists($model::class))
            throw new Exception('Not a valid class: ' . $model::class);

        $class = new ReflectionClass($model);

        foreach ($data as $key => $value) {
            if (property_exists($model, $key))
                $model->{$key} = $this->TypeCasting($class->getProperty($key), $value);
        }

        return $model;
    }

    /**
     * @throws Exception
     */
    protected function TypeCasting(ReflectionProperty $property, mixed $value): mixed
    {
        $propertyType = $property->getType();
        $typeName = $propertyType?->getName();

        return is_null($value) ? null : match ($typeName){
            'string' => (string)$value,
            'int' => (int)$value,
            'bool' => (bool)$value,
            'float', 'double' => (float)$value,
            'DateTime' => is_string($value) ? new DateTime($value) : null,
            default => $value
        };
    }

    /**
     * @throws Exception
     */
    protected function bindModel(object $model): ?object
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
            return $this->Mapping($model, $_POST);

        return null;
    }

    protected function bindData(mixed $data): mixed
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
            return $_POST[$data];

        return null;
    }

    /**
     * @throws Exception
     */
    public function callAction(string $action): void
    {
        $dependency = $this->beforeAction($action);

        if ($dependency === null)
            $this->$action();
        else{
            $model = is_object($dependency) ? $this->bindModel($dependency) : $this->bindData($dependency);
            $this->$action($model);
        }
    }
}
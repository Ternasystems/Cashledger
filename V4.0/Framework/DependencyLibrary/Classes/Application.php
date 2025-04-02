<?php

namespace TS_DependencyInjection\Classes;

use Exception;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionException;
use TS_Controller\Classes\BaseController;
use TS_Database\Classes\DBContext;

class Application
{
    private array $bindings = [];
    private array $scopedInstances = [];
    private array $singletonInstances = [];
    private array $configurations = [];
    private array $baseTypes = [];
    private array $configMap = [];

    public function __construct(array $services, array $configs)
    {
        $this->bindings = $services;
        $this->configurations = $configs;
        $this->baseTypes = ['string', 'array', 'int', 'float', 'bool'];
        $this->configMap = [
          'Context' => 'ConnectionString'
        ];
    }

    /**
     * @throws ReflectionException
     * @throws Exception
     */
    protected function createInstance(string $Implementation): object{
        $reflector = new ReflectionClass($Implementation);

        if (!$reflector->isInstantiable())
            throw new Exception("Implementation $Implementation is not instantiable");

        $constructor = $reflector->getConstructor();
        if (is_null($constructor))
            return new $Implementation();

        $parameters = $constructor->getParameters();
        $dependencies = [];
        foreach ($parameters as $parameter) {
            $dependency = $parameter->getType()->getName();
            if (in_array($dependency, $this->baseTypes)) {
                $mapKey = array_filter(array_keys($this->configMap), fn($n) => str_contains($reflector->getName(), $n))[0];
                $configKey = $this->configMap[$mapKey];
                $dependencies[] = $this->configurations[$configKey];
            }else
                $dependencies[] = $this->resolve($dependency);
        }
        return $reflector->newInstanceArgs($dependencies);
    }

    /**
     * @throws Exception
     */
    protected function resolve(string $IService): object
    {
        if (!isset($this->bindings[$IService]))
            throw new Exception('Service not registered: ' . $IService);

        $service = $this->bindings[$IService];

        if ($service['Lifetime'] === 'singleton'){
            if (!isset($this->singletonInstances[$IService]))
                $this->singletonInstances[$IService] = $this->createInstance($service['Implementation']);

            return $this->singletonInstances[$IService];
        }

        if ($service['Lifetime'] === 'scoped'){
            $requestId = session_id();
            if (!$requestId)
                throw new Exception('Session not set');
            if (!isset($this->scopedInstances[$requestId][$IService]))
                $this->scopedInstances[$requestId][$IService] = $this->createInstance($service['Implementation']);

            return $this->scopedInstances[$requestId][$IService];
        }

        return $this->createInstance($service['Implementation']);
    }

    /**
     * @throws Exception
     */
    public function GetDBContext(string $IService): object
    {
        if (!is_subclass_of($IService, DBContext::class))
            throw new InvalidArgumentException('The IService must be of type DBContext');

        return $this->resolve($IService);
    }

    /**
     * @throws Exception
     */
    public function GetService(string $IService): object
    {
        if (is_subclass_of($IService, DBContext::class) || is_subclass_of($IService, BaseController::class))
            throw new InvalidArgumentException('The IService is not a valid service');

        return $this->resolve($IService);
    }

    /**
     * @throws Exception
     */
    public function GetController(string $IService): object
    {
        if (!is_subclass_of($IService, BaseController::class))
            throw new InvalidArgumentException('The IService must be of type BaseController');

        return $this->resolve($IService);
    }
}
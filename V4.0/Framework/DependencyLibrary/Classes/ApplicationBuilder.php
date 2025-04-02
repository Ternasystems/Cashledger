<?php

namespace TS_DependencyInjection\Classes;

use TS_DependencyInjection\Interfaces\IServiceCollection;

class ApplicationBuilder implements IServiceCollection
{
    private array $services = [];
    private array $configurations = [];

    protected function bind(string $IService, string $Implementation, string $Lifetime): void
    {
        $this->services[$IService] = [
            'Implementation' => $Implementation,
            'Lifetime' => $Lifetime
        ];
    }

    public function AddConfigurations(array $configurations): void
    {
        $this->configurations = $configurations;
    }

    public function AddConfiguration(string $name, mixed $value): void
    {
        $this->configurations[$name] = $value;
    }

    public function AddTransient(string $IService, string $Implementation): void
    {
        $this->bind($IService, $Implementation, 'transient');
    }

    public function AddScoped(string $IService, string $Implementation): void
    {
        $this->bind($IService, $Implementation, 'scoped');
    }

    public function AddSingleton(string $IService, string $Implementation): void
    {
        $this->bind($IService, $Implementation, 'singleton');
    }

    public function AddDBContext(string $IService, string $Implementation): void
    {
        $this->AddSingleton($IService, $Implementation);
    }

    public function Build(): Application
    {
        return new Application($this->services, $this->configurations);
    }
}
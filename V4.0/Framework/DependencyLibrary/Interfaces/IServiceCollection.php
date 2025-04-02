<?php

namespace TS_DependencyInjection\Interfaces;

interface IServiceCollection
{
    public function AddTransient(string $IService, string $Implementation): void;
    public function AddScoped(string $IService, string $Implementation): void;
    public function AddSingleton(string $IService, string $Implementation): void;
    public function AddDBContext(string $IService, string $Implementation): void;
}
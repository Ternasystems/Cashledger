<?php

namespace APP_Inventory_Model;

class ProductModel
{
    public ?string $productid;
    public string $productname;
    public string $categoryid;
    public string $unitid;
    public float $minstock;
    public float $maxstock;
    public array $attributes;
    public ?string $productdesc;
    public array $productlocales;
}
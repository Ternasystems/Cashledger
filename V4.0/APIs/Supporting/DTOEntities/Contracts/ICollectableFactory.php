<?php

namespace API_DTOEntities_Contract;

use API_DTOEntities_Collection\EntityCollectable;
use API_DTORepositories_Contract\IRepository;
use TS_Utility\Classes\AbstractCollectable;

interface ICollectableFactory
{
    public function Create(): void;
    public function ToArray(): ?array;
    public function Collectable(): EntityCollectable|AbstractCollectable|null;
    public function Repository(): IRepository;
    public function Reset(): void;
}
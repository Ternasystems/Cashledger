<?php

namespace API_Administration_Controller;

use API_Administration_Contract\IAppService;
use API_DTOEntities_Collection\AppCategories;
use API_DTOEntities_Collection\Apps;
use API_DTOEntities_Model\App;
use API_DTOEntities_Model\AppCategory;
use TS_Controller\Classes\BaseController;

class AppController extends BaseController
{
    protected IAppService $service;

    public function __construct(IAppService $_service)
    {
        $this->service  = $_service;
    }

    public function Get(): ?Apps
    {
        return $this->service->GetApps();
    }

    public function GetById(string $id): ?App
    {
        return $this->service->GetApps(fn($n) => $n->It()->Id == $id);
    }

    public function GetByCategory(string $categoryId): ?Apps
    {
        return $this->service->GetApps(fn($n) => $n->AppRelations()->Where(fn($t) => $t->CategoryId == $categoryId));
    }

    public function GetCategories(): ?AppCategories
    {
        return $this->service->GetCategories();
    }

    public function GetCategoryById(string $id): ?AppCategory
    {
        return $this->service->GetCategories(fn($n) => $n->It()->Id == $id);
    }
}
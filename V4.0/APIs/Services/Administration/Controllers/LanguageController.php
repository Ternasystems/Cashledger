<?php

namespace API_Administration_Controller;

use API_Administration_Contract\ILanguageService;
use API_DTOEntities_Collection\Languages;
use API_DTOEntities_Model\Language;
use TS_Controller\Classes\BaseController;

class LanguageController extends BaseController
{
    private ILanguageService $service;

    public function __construct(ILanguageService $_service)
    {
        $this->service = $_service;
    }

    public function Get(): ?Languages
    {
        return $this->service->GetLanguages();
    }

    public function GetById(int $Id): ?Language
    {
        return $this->service->GetLanguages(fn($n) => $n->It()->Id == $Id);
    }

    public function GetByLabel(string $label): ?Language
    {
        return $this->service->GetLanguages(fn($n) => $n->It()->Label == strtoupper($label));
    }
}
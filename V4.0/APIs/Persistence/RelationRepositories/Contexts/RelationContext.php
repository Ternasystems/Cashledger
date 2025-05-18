<?php

namespace API_RelationRepositories_Context;

use API_DTORepositories_Context\TContext;
use API_RelationRepositories_Collection\AppRelations;
use API_RelationRepositories_Collection\AttributeRelations;
use API_RelationRepositories_Collection\CivilityRelations;
use API_RelationRepositories_Collection\ContactRelations;
use API_RelationRepositories_Collection\DeliveryRelations;
use API_RelationRepositories_Collection\DispatchRelations;
use API_RelationRepositories_Collection\GenderRelations;
use API_RelationRepositories_Collection\InventoryRelations;
use API_RelationRepositories_Collection\InventRelations;
use API_RelationRepositories_Collection\OccupationRelations;
use API_RelationRepositories_Collection\ReturnRelations;
use API_RelationRepositories_Collection\RoleRelations;
use API_RelationRepositories_Collection\StatusRelations;
use API_RelationRepositories_Collection\StockRelations;
use API_RelationRepositories_Collection\TitleRelations;
use API_RelationRepositories_Collection\WasteRelations;
use API_RelationRepositories_Model\AppRelation;
use API_RelationRepositories_Model\AttributeRelation;
use API_RelationRepositories_Model\CivilityRelation;
use API_RelationRepositories_Model\ContactRelation;
use API_RelationRepositories_Model\DeliveryRelation;
use API_RelationRepositories_Model\DispatchRelation;
use API_RelationRepositories_Model\GenderRelation;
use API_RelationRepositories_Model\InventoryRelation;
use API_RelationRepositories_Model\InventRelation;
use API_RelationRepositories_Model\OccupationRelation;
use API_RelationRepositories_Model\ReturnRelation;
use API_RelationRepositories_Model\RoleRelation;
use API_RelationRepositories_Model\StatusRelation;
use API_RelationRepositories_Model\StockRelation;
use API_RelationRepositories_Model\TitleRelation;
use API_RelationRepositories_Model\WasteRelation;
use PDO;
use API_RelationRepositories_Collection\LanguageRelations;
use API_RelationRepositories_Model\LanguageRelation;
use TS_Database\Classes\DBContext;

class RelationContext extends DBContext
{
    protected PDO $pdo;
    private string $apprelation = 'cl_AppRelations';
    private string $attributerelation = 'cl_AttributeRelations';
    private string $civilityrelation = 'cl_CivilityRelations';
    private string $contactrelation = 'cl_ContactRelations';
    private string $genderrelation = 'cl_GenderRelations';
    private string $inventoryrelation = 'cl_InventoryRelations';
    private string $languagerelation = 'cl_LanguageRelations';
    private string $occupationrelation = 'cl_OccupationRelations';
    private string $rolerelation = 'cl_RoleRelations';
    private string $statusrelation = 'cl_StatusRelations';
    private string $stockrelation = 'cl_StockRelations';
    private string $titlerelation = 'cl_TitleRelations';
    private string $deliveryrelation = 'cl_DeliveryRelations';
    private string $dispatchrelation = 'cl_DispatchRelations';
    private string $returnrelation = 'cl_ReturnRelations';
    private string $wasterelation = 'cl_WasteRelations';
    private string $inventrelation = 'cl_InventRelations';

    public function __construct(array $_connectionString){
        $this->pdo = DBContext::GetConnection($_connectionString);
        $this->SetEntityMap();
        $this->SetPropertyMap();
    }

    use TContext;

    private function SetEntityMap(): void
    {
        $this->entityMap = [
            'apprelation' => AppRelation::class,
            'attributerelation' => Attributerelation::class,
            'civilityrelation' => CivilityRelation::class,
            'contactrelation' => ContactRelation::class,
            'genderrelation' => GenderRelation::class,
            'inventoryrelation' => InventoryRelation::class,
            'languagerelation' => LanguageRelation::class,
            'occupationrelation' => OccupationRelation::class,
            'rolerelation' => RoleRelation::class,
            'statusrelation' => StatusRelation::class,
            'stockrelation' => StockRelation::class,
            'titlerelation' => TitleRelation::class,
            'deliveryrelation' => DeliveryRelation::class,
            'dispatchrelation' => DispatchRelation::class,
            'returnrelation' => ReturnRelation::class,
            'wasterelation' => WasteRelation::class,
            'inventrelation' => InventRelation::class,
            'apprelationcollection' => AppRelations::class,
            'attributerelationcollection' => AttributeRelations::class,
            'civilityrelationcollection' => CivilityRelations::class,
            'contactrelationcollection' => ContactRelations::class,
            'genderrelationcollection' => GenderRelations::class,
            'inventoryrelationcollection' => InventoryRelations::class,
            'languagerelationcollection' => LanguageRelations::class,
            'occupationrelationcollection' => OccupationRelations::class,
            'rolerelationcollection' => RoleRelations::class,
            'statusrelationcollection' => StatusRelations::class,
            'stockrelationcollection' => StockRelations::class,
            'titlerelationcollection' => TitleRelations::class,
            'deliveryrelationcollection' => DeliveryRelations::class,
            'dispatchrelationcollection' => DispatchRelations::class,
            'returnrelationcollection' => ReturnRelations::class,
            'wasterelationcollection' => WasteRelations::class,
            'inventrelationcollection' => InventRelations::class
        ];
    }

    private function SetPropertyMap(): void
    {
        $this->propertyMap = [
            'ID' => 'Id',
            'CivilityID' => 'CivilityId',
            'ContactID' => 'ContactId',
            'CredentialID' => 'CredentialId',
            'GenderID' => 'GenderId',
            'LangID' => 'LangId',
            'OccupationID' => 'OccupationId',
            'ProfileID' => 'ProfileId',
            'ReferenceID' => 'ReferenceId',
            'RoleID' => 'RoleId',
            'StatusID' => 'StatusId',
            'TitleID' => 'TitleId',
            'AppID' => 'AppId',
            'AppCategoryID' => 'AppCategoryId',
            'AttributeID' => 'AttributeId',
            'StockID' => 'StockId',
            'ProductID' => 'ProductId',
            'InventID' => 'InventId',
            'DeliveryID' => 'DeliveryId',
            'DispatchID' => 'DispatchId',
            'ReturnID' => 'ReturnId',
            'WasteID' => 'WasteId'
        ];
    }
}
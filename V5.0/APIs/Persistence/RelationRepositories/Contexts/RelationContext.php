<?php

namespace API_RelationRepositories_Context;

use API_DTORepositories_Context\TContext;
use API_DTORepositories_Contract\IContext;
use API_RelationRepositories_Collection\AppRelations;
use API_RelationRepositories_Collection\CivilityRelations;
use API_RelationRepositories_Collection\ContactRelations;
use API_RelationRepositories_Collection\GenderRelations;
use API_RelationRepositories_Collection\LanguageRelations;
use API_RelationRepositories_Collection\OccupationRelations;
use API_RelationRepositories_Collection\RoleRelations;
use API_RelationRepositories_Collection\StatusRelations;
use API_RelationRepositories_Collection\TitleRelations;
use API_RelationRepositories_Model\AppRelation;
use API_RelationRepositories_Model\CivilityRelation;
use API_RelationRepositories_Model\ContactRelation;
use API_RelationRepositories_Model\GenderRelation;
use API_RelationRepositories_Model\LanguageRelation;
use API_RelationRepositories_Model\OccupationRelation;
use API_RelationRepositories_Model\RoleRelation;
use API_RelationRepositories_Model\StatusRelation;
use API_RelationRepositories_Model\TitleRelation;
use TS_Database\Classes\DBConnection;
use TS_Database\Classes\DBContext;
use TS_Database\Classes\DBCredentials;
use TS_Exception\Classes\DBException;

class RelationContext implements IContext
{
    // This trait provides all the data access and mapping methods.
    use TContext;

    private DBContext $dbContext;
    protected array $entityMap = [];
    protected array $propertyMap = [];

    // Table name properties specific to this context.

    private string $apprelation = 'cl_AppRelations';
    private string $civilityrelation = 'cl_CivilityRelations';
    private string $contactrelation = 'cl_ContactRelations';
    private string $genderrelation = 'cl_GenderRelations';
    private string $languagerelation = 'cl_LanguageRelations';
    private string $occupationrelation = 'cl_OccupationRelations';
    private string $rolerelation = 'cl_RoleRelations';
    private string $statusrelation = 'cl_StatusRelations';
    private string $titlerelation = 'cl_TitleRelations';

    /**
     * @throws DBException
     */
    public function __construct(DBCredentials $credentials)
    {
        $pdo = DBConnection::create($credentials);
        $this->dbContext = new DBContext($pdo);
        $this->SetEntityMap();
        $this->SetPropertyMap();
    }

    private function SetEntityMap(): void
    {
        $this->entityMap = [
            'apprelation' => AppRelation::class,
            'civilityrelation' => CivilityRelation::class,
            'contactrelation' => ContactRelation::class,
            'genderrelation' => GenderRelation::class,
            'languagerelation' => LanguageRelation::class,
            'occupationrelation' => OccupationRelation::class,
            'rolerelation' => RoleRelation::class,
            'statusrelation' => StatusRelation::class,
            'titlerelation' => TitleRelation::class,
            'apprelationcollection' => AppRelations::class,
            'civilityrelationcollection' => CivilityRelations::class,
            'contactrelationcollection' => ContactRelations::class,
            'genderrelationcollection' => GenderRelations::class,
            'languagerelationcollection' => LanguageRelations::class,
            'occupationrelationcollection' => OccupationRelations::class,
            'rolerelationcollection' => RoleRelations::class,
            'statusrelationcollection' => StatusRelations::class,
            'titlerelationcollection' => TitleRelations::class
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
            'AppCategoryID' => 'AppCategoryId'
        ];
    }
}
<?php

namespace API_RelationRepositories_Context;

use API_DTORepositories_Context\Context;
use API_RelationRepositories_Collection\AppRelations;
use API_RelationRepositories_Collection\CivilityRelations;
use API_RelationRepositories_Collection\ContactRelations;
use API_RelationRepositories_Collection\GenderRelations;
use API_RelationRepositories_Collection\LanguageRelations;
use API_RelationRepositories_Collection\OccupationRelations;
use API_RelationRepositories_Collection\ParameterRelations;
use API_RelationRepositories_Collection\RoleRelations;
use API_RelationRepositories_Collection\StatusRelations;
use API_RelationRepositories_Collection\TitleRelations;
use API_RelationRepositories_Model\AppRelation;
use API_RelationRepositories_Model\CivilityRelation;
use API_RelationRepositories_Model\ContactRelation;
use API_RelationRepositories_Model\GenderRelation;
use API_RelationRepositories_Model\LanguageRelation;
use API_RelationRepositories_Model\OccupationRelation;
use API_RelationRepositories_Model\ParameterRelation;
use API_RelationRepositories_Model\RoleRelation;
use API_RelationRepositories_Model\StatusRelation;
use API_RelationRepositories_Model\TitleRelation;

/**
 * Acts as a Data Mapper for the Relation DTOs.
 * It configures the entity/property maps and uses the TContext trait
 * to handle all database interactions and object hydration.
 */
class RelationContext extends Context
{
    // Table name properties specific to this context.
    private string $apprelation = 'cl_AppRelations';
    private string $civilityrelation = 'cl_CivilityRelations';
    private string $contactrelation = 'cl_ContactRelations';
    private string $genderrelation = 'cl_GenderRelations';
    private string $languagerelation = 'cl_LanguageRelations';
    private string $occupationrelation = 'cl_OccupationRelations';
    private string $parameterrelation = 'cl_ParameterRelations';
    private string $rolerelation = 'cl_RoleRelations';
    private string $statusrelation = 'cl_StatusRelations';
    private string $titlerelation = 'cl_TitleRelations';

    /**
     * @inheritDoc
     */
    protected function setEntityMap(): void
    {
        $this->entityMap = [
            'apprelation' => AppRelation::class,
            'civilityrelation' => CivilityRelation::class,
            'contactrelation' => ContactRelation::class,
            'genderrelation' => GenderRelation::class,
            'languagerelation' => LanguageRelation::class,
            'occupationrelation' => OccupationRelation::class,
            'parameterrelation' => ParameterRelation::class,
            'rolerelation' => RoleRelation::class,
            'statusrelation' => StatusRelation::class,
            'titlerelation' => TitleRelation::class,
            'apprelationcollection' => AppRelations::class,
            'civilityrelationcollection' => CivilityRelations::class,
            'contactrelationcollection' => ContactRelations::class,
            'genderrelationcollection' => GenderRelations::class,
            'languagerelationcollection' => LanguageRelations::class,
            'occupationrelationcollection' => OccupationRelations::class,
            'parameterrelationcollection' => ParameterRelations::class,
            'rolerelationcollection' => RoleRelations::class,
            'statusrelationcollection' => StatusRelations::class,
            'titlerelationcollection' => TitleRelations::class
        ];
    }

    /**
     * @inheritDoc
     */
    protected function setPropertyMap(): void
    {
        $this->propertyMap = [
            'ID' => 'Id',
            'LangID' => 'LangId',
            'ReferenceID' => 'ReferenceId',
            'AppID' => 'AppId',
            'AppCategoryID' => 'AppCategoryId'
        ];
    }
}
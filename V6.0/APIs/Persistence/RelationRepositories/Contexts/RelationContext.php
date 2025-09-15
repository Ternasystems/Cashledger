<?php

namespace API_RelationRepositories_Context;

use API_DTORepositories_Context\Context;
use API_RelationRepositories_Collection\AppRelations;
use API_RelationRepositories_Collection\LanguageRelations;
use API_RelationRepositories_Model\AppRelation;
use API_RelationRepositories_Model\LanguageRelation;

/**
 * Acts as a Data Mapper for the Relation DTOs.
 * It configures the entity/property maps and uses the TContext trait
 * to handle all database interactions and object hydration.
 */
class RelationContext extends Context
{
    // Table name properties specific to this context.
    private string $apprelation = 'cl_AppRelations';
    private string $languagerelation = 'cl_LanguageRelations';

    /**
     * @inheritDoc
     */
    protected function setEntityMap(): void
    {
        $this->entityMap = [
            'apprelation' => AppRelation::class,
            'languagerelation' => LanguageRelation::class,
            'apprelationcollection' => AppRelations::class,
            'languagerelationcollection' => LanguageRelations::class
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
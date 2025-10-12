<?php

namespace API_DTORepositories_Context;

use API_DTORepositories_Contract\IContext;
use TS_Database\Classes\DBContext;
use TS_Database\Classes\DBCredentials;
use TS_Exception\Classes\DBException;

/**
 * The abstract base class for all data contexts in the application.
 * It centralizes the database connection and core data access logic.
 */
abstract class Context implements IContext
{
    // This trait contains the implementation for data access methods like SelectAll, Insert, Mapping, etc.
    use TContext;

    protected DBContext $dbContext;
    protected array $entityMap = [];
    protected array $propertyMap = [];

    /**
     * Initializes the context, creates the database connection, and calls the
     * abstract methods to configure the data maps.
     *
     * @param DBCredentials $credentials
     * @throws DBException
     */
    public function __construct(DBCredentials $credentials)
    {
        $this->dbContext = new DBContext($credentials);
        $this->setEntityMap();
        $this->setPropertyMap();
    }

    /**
     * Concrete context classes must implement this method to define their
     * mapping from entity/collection names to class names.
     */
    protected abstract function setEntityMap(): void;

    /**
     * Concrete context classes must implement this method to define their
     * mapping from database column names to model property names.
     */
    protected abstract function setPropertyMap(): void;
}
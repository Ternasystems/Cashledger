<?php

namespace API_DTORepositories_Model;

use TS_Database\Classes\AbstractModel;

/**
 * The abstract base class for all Data Transfer Object (DTO) models.
 * It directly extends the framework's AbstractModel to provide ORM-like functionality,
 * automatically handling attribute access via magic methods.
 */
abstract class DTOBase extends AbstractModel
{
    // This class is intentionally left empty.
    // All functionality is inherited from AbstractModel, including the magic __get and __set methods.
}
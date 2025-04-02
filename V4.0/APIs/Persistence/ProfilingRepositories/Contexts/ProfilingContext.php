<?php

namespace API_ProfilingRepositories_Context;

use API_DTORepositories_Context\TContext;
use API_ProfilingRepositories_Collection\Civilities;
use API_ProfilingRepositories_Collection\Contacts;
use API_ProfilingRepositories_Collection\ContactTypes;
use API_ProfilingRepositories_Collection\Credentials;
use API_ProfilingRepositories_Collection\Genders;
use API_ProfilingRepositories_Collection\Occupations;
use API_ProfilingRepositories_Collection\Permissions;
use API_ProfilingRepositories_Collection\Profiles;
use API_ProfilingRepositories_Collection\Roles;
use API_ProfilingRepositories_Collection\Statuses;
use API_ProfilingRepositories_Collection\Titles;
use API_ProfilingRepositories_Collection\Trackings;
use API_ProfilingRepositories_Model\Civility;
use API_ProfilingRepositories_Model\Contact;
use API_ProfilingRepositories_Model\ContactType;
use API_ProfilingRepositories_Model\Credential;
use API_ProfilingRepositories_Model\Gender;
use API_ProfilingRepositories_Model\Occupation;
use API_ProfilingRepositories_Model\Permission;
use API_ProfilingRepositories_Model\Profile;
use API_ProfilingRepositories_Model\Role;
use API_ProfilingRepositories_Model\Status;
use API_ProfilingRepositories_Model\Title;
use API_ProfilingRepositories_Model\Tracking;
use PDO;
use TS_Database\Classes\DBContext;

class ProfilingContext extends DBContext
{
    protected PDO $pdo;
    private string $civility = 'cl_Civilities';
    private string $contact = 'cl_Contacts';
    private string $contacttype = 'cl_ContactTypes';
    private string $credential  = 'cl_Credentials';
    private string $gender = 'cl_Genders';
    private string $occupation = 'cl_Occupations';
    private string $permission = 'cl_Permissions';
    private string $profile = 'cl_Profiles';
    private string $role = 'cl_Roles';
    private string $status = 'cl_Statuses';
    private string $title = 'cl_Titles';
    private string $tracking  = 'cl_Trackings';

    public function __construct(array $_connectionString){
        $this->pdo = DBContext::GetConnection($_connectionString);
        $this->SetEntityMap();
        $this->SetPropertyMap();
    }

    use TContext;

    private function SetEntityMap(): void
    {
        $this->entityMap = [
            'civility' => Civility::class,
            'contact' => Contact::class,
            'contacttype' => ContactType::class,
            'credential' => Credential::class,
            'gender' => Gender::class,
            'occupation' => Occupation::class,
            'permission' => Permission::class,
            'profile' => Profile::class,
            'role' => Role::class,
            'status' => Status::class,
            'title' => Title::class,
            'tracking' => Tracking::class,
            'civilitycollection' => Civilities::class,
            'contactcollection' => Contacts::class,
            'contacttypecollection' => ContactTypes::class,
            'credentialcollection' => Credentials::class,
            'gendercollection' => Genders::class,
            'occupationcollection' => Occupations::class,
            'permissioncollection' => Permissions::class,
            'profilecollection' => Profiles::class,
            'rolecollection' => Roles::class,
            'statuscollection' => Statuses::class,
            'titlecollection' => Titles::class,
            'trackingcollection' => Trackings::class
        ];
    }

    private function SetPropertyMap(): void
    {
        $this->propertyMap = [
            'ID' => 'Id',
            'ContactTypeID' => 'ContactTypeId',
            'ProfileID' => 'ProfileId',
            'SessionID' => 'SessionId',
            'IP' => 'Ip'
        ];
    }
}
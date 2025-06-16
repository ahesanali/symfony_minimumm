<?php
namespace App\Utils\Auth;


use App\Utils\CoreDataService;
use App\Utils\Auth\Role;
use App\Utils\DBManager;
use stdClass;

class User extends CoreDataService
{
    public int $id;
    public string $firstName;
    public string $lastName;
    public string $userId;
    public string $password;
    public int $userRoleId;
    public string $apiToken; // <-- Add this
    /**
     * Assign below dynamic properties to class
     *
     * @property int $id [Database record id of user object]
     * @property string $firstName [First Name of user]
     * @property string $lastName [Last Name of user]
     * @property string $userId [User Id of user, example mobile no, or email id]
     * @property string $password [hashed password of user]
     * @property int $userRoleId [Role Id of user]
     *
     * @param array $attributes [assign this array values to above mentioned properties]
     */
    public function __construct(array $attributes,private DBManager $dbmanager)
    {
        parent::__construct($dbmanager);
        foreach ($attributes as $attribute_key => $attribute_value) {
            $this->$attribute_key =   $attribute_value;
        }

    }
    /**
     * check weather user has given permission access or not
     * @param  string  $premission_name
     * @return boolean
     */
    public function hasAccess($premission_name)
    {
        $role = $this->getRole();

        return $role->hasAccess($premission_name);
    }

    /**
     * Get Assigned Role
     * @return Role
     */
    public function getRole()
    {
        $find_role_sql = 'SELECT id, role, permissions, is_super FROM members_view WHERE email=:email';
        $role_obj = $this->executeSQL($find_role_sql,['email' => $this->userId],true);
        if(!is_null($role_obj)){
            $role = new stdClass();
            $role->roleId = $role_obj['id'];
            $role->role = $role_obj['role'];
            $role->permissions = $role_obj['permissions']??'';
            $role->isSuper = ($role_obj['is_super']==1) ? true :  false;
        }

        return  new Role((array)$role, $this->dbmanager);
    }

    /**
     * Check weather user is super or not
     * @return boolean
     */
    public function isSuper()
    {
        $role = $this->getRole();

        return $role->isSuper();
    }

}

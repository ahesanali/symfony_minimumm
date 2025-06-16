<?php
namespace App\Utils\Auth;



use App\Utils\CoreDataService;
use App\Utils\DBManager;

class Role extends CoreDataService
{
    public int $roleId;
    public string $role;
    public string $permissions;
    public bool $isSuper;
	/**
     * Assign below dynamic properties to class
     *
     * @property int $roleId [Role id of role from database]
     * @property string $role [Role Name of role]
     * @property string $permissions [Coma separeted permissions list of role]
     * @property string $isSuper [indicates weather role is super or not]
     *
     * @param array $attributes [assign this array values to above mentioned properties]
     */
	public function __construct(array $attributes,DBManager $dbmanager)
    {
        parent::__construct($dbmanager);
        // assign all attributes to class properties
    
        foreach ($attributes as $attribute_key => $attribute_value) {
            $this->$attribute_key =   $attribute_value;
        }
    }
	/**
     * check weather role has given permission access or not
     * @param  string  $premission_name
     * @return boolean
     */
    public function hasAccess($premission_name)
    {
    	//if user is super user than all permission is granted for user
    	if($this->isSuper)
    		return true;
    	$permission_list = explode(",",$this->permissions);
    	$permission_found = array_search($premission_name, $permission_list);
    	if($permission_found === FALSE)
    		return false;
    	return true;
    }

    /**
     * Get All permission list
     * @return array
     */
    public function getAllPermissions()
    {
    	$permission_list = explode(",",$this->permissions);

    	return $permission_list;
    }
    /**
     * Check weather role is super or not
     * @return boolean
     */
    public function isSuper()
    {
        return $this->isSuper;
    }

}

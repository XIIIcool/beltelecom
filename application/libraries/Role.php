<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');


class Role
{
    protected $permissions;
    private $ci;

    public function __construct() {

        $ci =& get_instance();
        $this->permissions = array();
        $ci->load->model('role_model');
    }

    public static function getRolePerms($role_id) {
        $role = new Role();
        $CI =& get_instance();


        $roles = $CI->role_model->getRolePermsDB($role_id);
        foreach($roles as $rol){
            $role->permissions[$rol['perm_desc']] = true;
        }

        return $role;
    }


    public function hasPerm($permission) {
        return isset($this->permissions[$permission]);
    }
}
<?php

class PrivilegedUser
{
    private $roles;
    private $user_id;

    public function __construct()
    {

        $ci =& get_instance();
        $this->permissions = array();
        $ci->load->model('role_model');
        $ci->load->model('user_model');

    }


    public static function getByUsername($email)
    {
        $ci =& get_instance();

        $result = $ci->user_model->getUserByEmail($email);

        if (!empty($result)) {
            $privUser = new PrivilegedUser();
            $privUser->user_id = $result->id;
            $privUser->username = $result->login;
            $privUser->password = $result->password;
            $privUser->email_addr = $result->email;

            //   print_r($privUser);
            $privUser->initRoles($result->id);
            return $privUser;
        } else {
            return false;
        }
    }

    public static function getByToken($token)
    {
        $ci =& get_instance();

        $result = $ci->user_model->getUserByToken($token);

        if (!empty($result)) {
            $privUser = new PrivilegedUser();
            $privUser->user_id = $result->id;
            $privUser->author = $result->id;
            $privUser->username = $result->login;
            $privUser->password = $result->password;
            $privUser->email_addr = $result->email;

            //   print_r($privUser);
            $privUser->initRoles($result->id);
            return $privUser;
        } else {
            return false;
        }
    }

    public function initRoles($user_id)
    {
        $this->roles = array();
        $CI =& get_instance();
        $CI->load->model('role_model');
        $roles = $CI->role_model->initRole($user_id);


        foreach ($roles as $ro) {
            $this->roles[$ro['role_name']] = Role::getRolePerms($ro['role_id']);
        }


    }

    public function hasPrivilege($perm)
    {
        foreach ($this->roles as $role) {
            if ($role->hasPerm($perm)) {
                return true;
            }
        }
        return false;
    }
}
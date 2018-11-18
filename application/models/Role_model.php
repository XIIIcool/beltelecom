<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Role_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();

        //load database library
        $this->load->database();
    }

    public function getRolePermsDB($id){

        $this->db->select('perm_desc');
        $this->db->from('role_perm');
        $this->db->join('permissions', 'role_perm.perm_id = permissions.perm_id');
        $this->db->where('role_perm.role_id',$id);
        $result = $this->db->get();

        return $result->result_array();
    }

    public function initRole($user_id){

        $this->db->select('user_role.role_id, roles.role_name');
        $this->db->from('user_role');
        $this->db->join('roles', 'user_role.role_id = roles.role_id');
        $this->db->where('user_role.user_id',$user_id);
        $result = $this->db->get();

        return $result->result_array();
    }

    public function addUserInRole($user_id,$role_id = 0){
        $data['user_id'] = $user_id;
        $data['role_id'] = $role_id;
        $insert = $this->db->insert('user_role',$data);
        if($insert){
            return $this->db->insert_id();
        } else return false;
    }
}
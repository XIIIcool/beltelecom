<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class User_model extends CI_Model {

    public function __construct() {
        parent::__construct();

        //load database library
        $this->load->database();
        $this->load->model('role_model');
    }

    /*
     *
     *//*
    function getRows($id = "", $limit = 50, $offset = 0){
        if(!empty($id)){
            $query = $this->db->get_where('posts', array('id' => $id));
            return $query->row_array();
        }else{
            $query = $this->db->get('posts', $limit, $offset);
            return $query->result_array();
        }
    }*/

    /*
     * Добавить пользователя
     */

    public function insert($data = array()) {
       $token = $data['token'];
       $data['password'] = md5($this->config->item( 'solt' ).$data['password']);
       unset($data['token']);
       unset($data['time']);

       if($data['password'] != '' && $data['email'] != '' && $token != ''){

           if(!$this->check_email($data['email'])) return false;

            $insert = $this->db->insert('users', $data);

            if($insert){
                $id = $this->db->insert_id();
                $this->create_token($this->db->insert_id(),$token);
                $this->role_model->addUserInRole($id);
                return $id;
            }else{
                return false;
            }
       } else return false;
    }

    //авторизация пользователя
    public function check_login($data = array()){
        if($data['email'] != '' && $data['password'] != '' ){
            $userData['email'] = $data['email'];
            $userData['password'] = $data['password'];
            $userData['time'] = now();

            $data['password'] = md5($this->config->item( 'solt' ).$data['password']);
            $data['status'] = 1;
            $request = $this->db->get_where('users',$data);
         //   echo $this->db->last_query();
            if($request->num_rows()==1){
               $request = $request->row();

                $token = AUTHORIZATION::generateToken($userData);
                $this->updateTokenUser($request->id,$token);

                //$token = $this->getTokenUserById($request->id);
                if($token){
                    return $token;
                } else return false;

            } else return false;
        }
    }
    // проверить существуем ли емейл
    public function check_email($email){
        $this->db->where('email',$email);
        $request = $this->db->get('users');
        if($request->num_rows()==0){
            return true;
        } else return false;
    }
    // обновить токен
    public function updateTokenUser($id,$token){
        $data['token'] = $token;
        $this->db->where('user_id',$id);
        $this->db->update('keys',$data);
    }
    // получить токен пользователя по id
    public function getTokenUserById($id){
        $this->db->where('user_id',$id);
        $this->db->order_by('date_created', 'DESC');
        $this->db->limit(1);
        $request = $this->db->get('keys');
        if($request->num_rows()==1){
            $request = $request->row();
            return $request->token;
        } else return false;
    }

    // проверка есть ли такой токен
    public function checkTokenUser($token){
        $this->db->where('token',$token);
        $this->db->limit(1);
        $request = $this->db->get('keys');
        if($request->num_rows()==1){

            return true;
        } else return false;
    }
    //добавить токен
    public function create_token($id,$token){
        $this->db->insert('keys',array('token'=>$token,'user_id'=>$id));

    }
    //получить пользователя по емейлу
    public function getUserByEmail($email){
        $this->db->where('email',$email);
        $request = $this->db->get('users');
        return $request->row();
    }
    // получить данные о пользователе по токену
    public function getUserByToken($token){
        $this->db->select('users.id,users.login,users.password,users.email');
        $this->db->from('users');
        $this->db->join('keys', 'keys.user_id = users.id');
        $this->db->where('keys.token',$token);


        $request = $this->db->get();
        return $request->row();
    }

    /*
     * Update post data
     *//*
    public function update($data, $id) {
        if(!empty($data) && !empty($id)){
            if(!array_key_exists('modified', $data)){
                $data['modified'] = date("Y-m-d H:i:s");
            }
            $update = $this->db->update('posts', $data, array('id'=>$id));
            return $update?true:false;
        }else{
            return false;
        }
    }*/

    /*
     * Delete post data
     *//*
    public function delete($id){
        //  $this->db->where('id',$id);
        //  $query = $this->db->get('posts');
        //  if ($query->num_rows() > 0){

        $delete = $this->db->delete('posts',array('id'=>$id));
        return $delete?true:false;
        //  }
        //  else{
        //     return false;
        //  }

    }*/

}
?>
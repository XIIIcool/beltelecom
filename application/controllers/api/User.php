<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

//include Rest Controller library
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';


class User extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();

        //Подключим библиотеки
        $this->load->helper('date');
        $this->load->model('user_model');

    }

    // регистрация пользователя
    public function register_post()
    {

        if ($this->post('password') == '' && $this->post('email') == '')
            $this->response(array('CODE' => 401, 'MESSAGE' => '401 Unauthorized'), REST_Controller::HTTP_UNAUTHORIZED);


        $userData = array();
        $userData['password'] = $this->post('password');
        $userData['email'] = $this->post('email');
        $userData['time'] = now();
        $userData['token'] = AUTHORIZATION::generateToken($userData);

        $request = $this->user_model->insert($userData);

        if ($request) {

            $this->response(array('status' => true, 'token' => $userData['token']
            ), REST_Controller::HTTP_OK);
        } else {
            $this->response(array('status' => false, 'message' => 'Invalid data or data already in use.'), REST_Controller::HTTP_OK);
        }


    }

    // Авторизация пользователя
    public function login_post()
    {

        $userData = array();
        $userData['password'] = $this->post('password');
        $userData['email'] = $this->post('email');
        $request = $this->user_model->check_login($userData);

        if ($request) {

            $this->response(array('status' => true, 'token' => $request
            ), REST_Controller::HTTP_OK);
        } else {
            $this->response(array('status' => false, 'message' => 'Invalid data or data already in use.'), REST_Controller::HTTP_UNAUTHORIZED);
        }

    }


}
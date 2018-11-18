<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {


    public function index()
    {
       $this->load->view('template/header');
       $this->load->view('auth');
       $this->load->view('template/footer');
    }

    public function register(){
        $this->load->view('template/header');
        $this->load->view('register');
        $this->load->view('template/footer');
    }
}

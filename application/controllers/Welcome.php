<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {


	public function index()
	{
		//$this->load->view('welcome_message');

        echo 'Тестовое задание<br>';
        echo '<div style="width: 500px;"><code>Средство разработки: PHP
Framework: Codeignitor или Yii2
База данных: MySQL или MongoDB

    </code></div>';
	}
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('pagination');
        $this->load->library('Restcurl');
        $this->load->helper('cookie');
        if (!get_cookie('token')) {
            redirect(base_url() . 'auth');
        }

    }

    public function index()
    {

        $token = get_cookie('token');

        $pageitem = 5;
        $result = RestCurl::get('http://pic-post.ru/beltelecom/api/post/', array('limit' => $pageitem), $token);

        if($result['status'] == 401 && $result['data']->status == false) {
            delete_cookie('token');
            redirect(base_url() . 'auth');
        }

        //print_r($result);


        $config['base_url'] = base_url() . 'main/page/';
        $config['total_rows'] = $result['data']->meta->total;
        $config['per_page'] = $pageitem;

        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="page-item active"><a class="page-link" href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['next_tag_open'] = '<li class="page-item">';
        $config['next_tagl_close'] = '</a></li>';
        $config['prev_tag_open'] = '<li class="page-item">';
        $config['prev_tagl_close'] = '</li>';
        $config['first_tag_open'] = '<li class="page-item disabled">';
        $config['first_tagl_close'] = '</li>';
        $config['last_tag_open'] = '<li class="page-item">';
        $config['last_tagl_close'] = '</a></li>';
        $config['attributes'] = array('class' => 'page-link');
        $this->pagination->initialize($config);


        $pagination = $this->pagination->create_links();
        $result['pagination'] = $pagination;

        $this->load->view('template/header');
        $this->load->view('template/nav');
        $this->load->view('main', $result);
        $this->load->view('template/footer');


    }

    public function page($a = 0)
    {
        $a = intval($a);
        $token = get_cookie('token');

        $pageitem = 5;
        $result = RestCurl::get('http://pic-post.ru/beltelecom/api/post/', array('limit' => $pageitem, 'offset' => $a), $token);

        if($result['status'] == 401 && $result['data']->status == false) {
            delete_cookie('token');
            redirect(base_url() . 'auth');
        }

        $config['base_url'] = base_url() . 'main/page/';
        $config['total_rows'] = $result['data']->meta->total;
        $config['per_page'] = $pageitem;

        $config['base_url'] = base_url() . 'main/page/';
        $config['total_rows'] = $result['data']->meta->total;
        $config['per_page'] = $pageitem;

        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="page-item active"><a class="page-link" href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['next_tag_open'] = '<li class="page-item">';
        $config['next_tagl_close'] = '</a></li>';
        $config['prev_tag_open'] = '<li class="page-item">';
        $config['prev_tagl_close'] = '</li>';
        $config['first_tag_open'] = '<li class="page-item disabled">';
        $config['first_tagl_close'] = '</li>';
        $config['last_tag_open'] = '<li class="page-item">';
        $config['last_tagl_close'] = '</a></li>';
        $config['attributes'] = array('class' => 'page-link');
        $this->pagination->initialize($config);


        $pagination = $this->pagination->create_links();
        $result['pagination'] = $pagination;

        $this->load->view('template/header');
        $this->load->view('template/nav');
        $this->load->view('main', $result);
        $this->load->view('template/footer');
    }

    public function detail($a)
    {
        if (empty($a)) redirect(base_url() . 'main');

        $a = intval($a);
        $token = get_cookie('token');

        $result = RestCurl::get('http://pic-post.ru/beltelecom/api/post/' . $a, array(), $token);
        if($result['status'] == 401 && $result['data']->status == false) {
            delete_cookie('token');
            redirect(base_url() . 'auth');
        }

        $comments = RestCurl::get('http://pic-post.ru/beltelecom/api/comment/' . $a, array(), $token);

        $result['comments'] = $comments;

        $this->load->view('template/header');
        $this->load->view('template/nav');
        $this->load->view('detail', $result);
        $this->load->view('template/footer');

    }

    public function postnew()
    {
        $this->load->view('template/header');
        $this->load->view('template/nav');
        $this->load->view('postnew');
        $this->load->view('template/footer');
    }

    public function editpost($a)
    {
        if (empty($a)) redirect(base_url() . 'main');

        $a = intval($a);
        $token = get_cookie('token');

        $result = RestCurl::get('http://pic-post.ru/beltelecom/api/post/' . $a, array(), $token);

        if($result['status'] == 401 && $result['data']->status == false) {
            delete_cookie('token');
            redirect(base_url() . 'auth');
        }

        $this->load->view('template/header');
        $this->load->view('template/nav');
        $this->load->view('editpost', $result);
        $this->load->view('template/footer');

    }
}

<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

//include Rest Controller library
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';
require APPPATH . 'libraries/Role.php';
require APPPATH . 'libraries/PrivilegedUser.php';


class Post extends REST_Controller
{

    protected $PU;

    public function __construct()
    {
        parent::__construct();

        $this->load->helper('bearer');
        // Получим токем из запроса
        $bearer = getBearerToken();

        //Подключим модели
        $this->load->model('post_model');
        $this->load->model('user_model');

        // проверка есть ли пользователь с найденным токеном
        if (!$this->user_model->checkTokenUser($bearer))
            $this->response(array('status' => false, 'message' => 'error token'), REST_Controller::HTTP_UNAUTHORIZED);

        // получим превилегии привилегии пользователя
        $this->PU = PrivilegedUser::getByToken($bearer);

        // проверим активен ли ещё токен по времени
        if (!AUTHORIZATION::validateTimestamp($bearer)) {
            $this->response(array('status' => false, 'message' => 'error token'), REST_Controller::HTTP_UNAUTHORIZED);
        }


    }

    // получить список статей
    public function index_get($id = 0, $limit = 50, $offset = 0)
    {

        if (!$this->PU->hasPrivilege("postGet"))
            $this->response(array('status' => false, 'text' => 'you dont have permission'), REST_Controller::HTTP_FORBIDDEN);

        if (isset($_REQUEST['limit'])) {
            $limit = intval($_REQUEST['limit']);
        }
        if (isset($_REQUEST['offset'])) {
            $offset = intval($_REQUEST['offset']);
        }

        $perm = array('postEdit' => $this->PU->hasPrivilege("postEdit"),
            'postEditAll' => $this->PU->hasPrivilege("postEditAll"),
            'postDeleteAll' => $this->PU->hasPrivilege("postDeleteAll"),
            'postDelete' => $this->PU->hasPrivilege("postDelete"),
            'id' => $this->PU->author
        );

        $post = $this->post_model->getRows($id, $limit, $offset, $perm);

        if (!empty($post)) {
            $posts['item'] = $post['result'];
            // $posts['total'] = $post['total'];
            $posts['meta'] = array('count' => ($post['total'] > 1) ? count($post['result']) : $post['total'], 'total' => $post['total']);
            //set the response and exit
            $this->response($posts, REST_Controller::HTTP_OK);
        } else {

            $this->response(array(
                'status' => FALSE,
                'message' => 'No post were found.'
            ), REST_Controller::HTTP_NOT_FOUND);
        }
    }

    // добавить статью
    public function index_post()
    {
        if (!$this->PU->hasPrivilege("postInsert"))
            $this->response(array('status' => 'error', 'text' => 'you dont have permission'), REST_Controller::HTTP_FORBIDDEN);

        $userData = array();
        $userData['title'] = htmlspecialchars($this->post('title'));
        $userData['body'] = htmlspecialchars($this->post('body'));
        $userData['author'] = $this->PU->author;

        if (!empty($userData['title']) && !empty($userData['body'])) {

            $insert = $this->post_model->insert($userData);


            if ($insert) {

                $this->response(array(
                    'status' => TRUE,
                    'message' => 'Post has been added successfully.',
                    'post_id' => $insert
                ), REST_Controller::HTTP_OK);
            } else {

                $this->response("Some problems occurred, please try again.", REST_Controller::HTTP_BAD_REQUEST);
            }
        } else {

            $this->response("Provide complete user information to create.", REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    //Редактирование записи
    public function index_patch($id)
    {
        if (!$this->PU->hasPrivilege("postEdit") && !$this->PU->hasPrivilege("postEditAll"))
            $this->response(array('status' => 'error', 'text' => 'you dont have permission'), REST_Controller::HTTP_FORBIDDEN);

        $data['title'] = htmlspecialchars($this->patch('title'));
        $data['body'] = htmlspecialchars($this->patch('body'));

        if ($id && !empty($data['title']) && !empty($data['body'])) {

            $edit = $this->post_model->update($id, $data, $this->PU->hasPrivilege("postEditAll"), $this->PU->author);

            if ($edit) {

                $this->response(array(
                    'status' => TRUE,
                    'message' => 'Post has been edit successfully.'
                ), REST_Controller::HTTP_OK);
            } else {

                $this->response(array('status' => FALSE, 'status' => false, 'message' => 'Some problems occurred, please try again.'), REST_Controller::HTTP_BAD_REQUEST);
            }
        } else {

            $this->response(array(
                'status' => FALSE,
                'message' => 'No post were found.'
            ), REST_Controller::HTTP_NOT_FOUND);
        }
    }

    // Удаление записи
    public function index_delete($id)
    {

        if (!$this->PU->hasPrivilege("postDelete") && !$this->PU->hasPrivilege("postDeleteAll"))
            $this->response(array('status' => 'error', 'text' => 'you dont have permission'), REST_Controller::HTTP_FORBIDDEN);

        if ($id) {

            $delete = $this->post_model->delete($id, $this->PU->hasPrivilege("postDeleteAll"), $this->PU->author);

            if ($delete) {

                $this->response(array(
                    'status' => TRUE,
                    'message' => 'Post has been removed successfully.'
                ), REST_Controller::HTTP_OK);
            } else {

                $this->response(array('status' => false, 'message' => 'Some problems occurred, please try again.'), REST_Controller::HTTP_BAD_REQUEST);
            }
        } else {

            $this->response(array(
                'status' => FALSE,
                'message' => 'No post were found.'
            ), REST_Controller::HTTP_NOT_FOUND);
        }
    }
}

?>
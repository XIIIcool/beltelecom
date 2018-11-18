<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

//include Rest Controller library
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';
require APPPATH . 'libraries/Role.php';
require APPPATH . 'libraries/PrivilegedUser.php';


class Comment extends REST_Controller
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
        $this->load->model('comment_model');
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

    // получить комментарии
    public function index_get($id = 0, $limit = 50, $offset = 0)
    {

        if (!$this->PU->hasPrivilege("commentGet"))
            $this->response(array('status' => false, 'text' => 'you dont have permission'), REST_Controller::HTTP_FORBIDDEN);

        if (empty($id)) {
            $this->response(array(
                'status' => FALSE,
                'message' => 'No comments were found.'
            ), REST_Controller::HTTP_NOT_FOUND);
        }

        if (isset($_REQUEST['limit'])) {
            $limit = intval($_REQUEST['limit']);
        }
        if (isset($_REQUEST['offset'])) {
            $offset = intval($_REQUEST['offset']);
        }

        $perm = array('commentEdit' => $this->PU->hasPrivilege("commentEdit"),
            'commentEditAll' => $this->PU->hasPrivilege("commentEditAll"),
            'commentDeleteAll' => $this->PU->hasPrivilege("commentDeleteAll"),
            'commentDelete' => $this->PU->hasPrivilege("commentDelete"),
            'id' => $this->PU->author
        );


        $comment = $this->comment_model->getRows($id, $this->PU->hasPrivilege("commentGetAll"), $limit, $offset, $perm);

        //check if the user data exists
        if (!empty($comment)) {

            $comment2 = $comment;
            unset($comment2['total']);
            $comment2['status'] = true;
            $comment2['meta'] = array('count' => count($comment2['items']), 'total' => $comment['total']);

            $this->response($comment2, REST_Controller::HTTP_OK);
        } else {

            $this->response(array(
                'status' => FALSE,
                'message' => 'No comments were found.'
            ), REST_Controller::HTTP_NOT_FOUND);
        }
    }

    // добавить комментарий
    public function index_post()
    {
        if (!$this->PU->hasPrivilege("commentInsert"))
            $this->response(array('status' => 'error', 'text' => 'you dont have permission'), REST_Controller::HTTP_FORBIDDEN);

        $userData = array();
        $userData['post_id'] = $this->post('post_id');
        $userData['body'] = htmlspecialchars($this->post('body'));
        $userData['author'] = $this->PU->author;

        if (!empty($userData['post_id']) && !empty($userData['body'])) {

            $insert = $this->comment_model->insert($userData);


            if ($insert) {

                $this->response(array(
                    'status' => TRUE,
                    'message' => 'Comment has been added successfully.',
                    'id' => $insert
                ), REST_Controller::HTTP_OK);
            } else {

                $this->response("Some problems occurred, please try again.", REST_Controller::HTTP_BAD_REQUEST);
            }
        } else {

            $this->response("Provide complete user information to create.", REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    //Редактирование комментария
    public function index_patch($id)
    {

        if (!$this->PU->hasPrivilege("commentEdit") && !$this->PU->hasPrivilege("commentEditAll"))
            $this->response(array('status' => 'error', 'text' => 'you dont have permission'), REST_Controller::HTTP_FORBIDDEN);

        $data['body'] = htmlspecialchars($this->patch('body'));


        if ($id && !empty($data['body'])) {

            $edit = $this->comment_model->update($id, $data, $this->PU->hasPrivilege("commentEditAll"), $this->PU->author);

            if ($edit) {

                $this->response(array(
                    'status' => TRUE,
                    'message' => 'Comment has been edit successfully.'
                ), REST_Controller::HTTP_OK);
            } else {

                $this->response(array('status' => false, 'message' => 'Some problems occurred, please try again.'), REST_Controller::HTTP_BAD_REQUEST);
            }
        } else {

            $this->response(array(
                'status' => FALSE,
                'message' => 'No comment were found.'
            ), REST_Controller::HTTP_NOT_FOUND);
        }
    }

    //Редактирование комментария, меняем статус
    public function change_patch($id)
    {

        if (!$this->PU->hasPrivilege("commentChange"))
            $this->response(array('status' => 'error', 'text' => 'you dont have permission'), REST_Controller::HTTP_FORBIDDEN);

        if ($id) {

            $edit = $this->comment_model->change($id);

            if ($edit) {
                //set the response and exit
                $this->response(array(
                    'status' => TRUE,
                    'message' => 'Comment has been edit successfully.'
                ), REST_Controller::HTTP_OK);
            } else {

                $this->response(array('status' => false, 'message' => 'Some problems occurred, please try again.'), REST_Controller::HTTP_BAD_REQUEST);
            }
        } else {

            $this->response(array(
                'status' => FALSE,
                'message' => 'No comment were found.'
            ), REST_Controller::HTTP_NOT_FOUND);
        }
    }

    // Удаление комментария
    public function index_delete($id)
    {
        if (!$this->PU->hasPrivilege("commentDelete") && !$this->PU->hasPrivilege("commentDeleteAll"))
            $this->response(array('status' => 'error', 'text' => 'you dont have permission'), REST_Controller::HTTP_FORBIDDEN);

        if ($id) {

            $delete = $this->comment_model->delete($id, $this->PU->hasPrivilege("postDeleteAll"), $this->PU->author);

            if ($delete) {

                $this->response(array(
                    'status' => TRUE,
                    'message' => 'Comment has been removed successfully.'
                ), REST_Controller::HTTP_OK);
            } else {

                $this->response(array('status' => false, 'message' => 'Some problems occurred, please try again.'), REST_Controller::HTTP_BAD_REQUEST);
            }
        } else {

            $this->response(array(
                'status' => FALSE,
                'message' => 'No comment were found.'
            ), REST_Controller::HTTP_NOT_FOUND);
        }
    }
}

?>
<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Post_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();

        //load database library
        $this->load->database();
    }

    /*
     * Получить список постов
     */
    function getRows($id = "", $limit = 50, $offset = 0, $per = array())
    {

        if (!empty($id)) {
            $query = $this->db->get_where('posts', array('id' => $id));
            $queryResult = $query->row_array();

            $queryResult['edit'] = false;
            $queryResult['delete'] = false;

            if ($per['postEditAll'] === true) {
                $queryResult['edit'] = true;
            } elseif ($per['postEdit'] === true) {
                if ($queryResult['author'] == $per['id']) {
                    $queryResult['edit'] = true;
                }
            }
            if ($per['postDeleteAll'] === true) {
                $queryResult['delete'] = true;
            } elseif ($per['postDelete'] === true) {
                if ($queryResult['author'] == $per['id']) {
                    $queryResult['delete'] = true;
                }
            }

            return array('result' => $queryResult, 'total' => 1);
        } else {

            $this->db->order_by('created', 'desc');
            $query = $this->db->get('posts', $limit, $offset);
            $queryResult = $query->result_array();

            foreach ($queryResult as &$item) {

                $item['edit'] = false;
                $item['delete'] = false;

                if ($per['postEditAll'] === true) {
                    $item['edit'] = true;
                } elseif ($per['postEdit'] === true) {
                    if ($item['author'] == $per['id']) {
                        $item['edit'] = true;
                    }
                }
                if ($per['postDeleteAll'] === true) {
                    $item['delete'] = true;
                } elseif ($per['postDelete'] === true) {
                    if ($item['author'] == $per['id']) {
                        $item['delete'] = true;
                    }
                }
            }

            $total = $this->db->get('posts');
            return array('result' => $queryResult, 'total' => count($total->result_array()));
        }
    }

    /*
     * добавить пост
     */
    public function insert($data = array())
    {
        if (!array_key_exists('created', $data)) {
            $data['created'] = date("Y-m-d H:i:s");
        }

        $data['status'] = 1;
        $insert = $this->db->insert('posts', $data);
        if ($insert) {
            return $this->db->insert_id();
        } else {
            return false;
        }
    }

    /*
     * Обновить пост
     */
    public function update($id, $data, $all = false, $author)
    {
        // Если у пользователя есть права на редактирование любого поста
        if ($all === true) {
            //unset($data['author']);
            $data['modified'] = date("Y-m-d H:i:s");

            $this->db->where('id', $id);
            $edit = $this->db->update('posts', $data);
            return ($this->db->affected_rows() == 1) ? true : false;
        } else {
            // Если у пользователя есть права только на редактирование только своих постов
            $data['modified'] = date("Y-m-d H:i:s");

            $this->db->where('id = ' . $id . ' AND author = ' . $author);
            $edit = $this->db->update('posts', $data);

            return ($this->db->affected_rows() == 1) ? true : false;
        }


    }

    /*
     * Удалить пост
     */
    public function delete($id, $all = false, $author)
    {
        // Если у пользователя есть права на удаление любого поста
        if ($all === true) {
            $delete = $this->db->delete('posts', array('id' => $id));
            return ($this->db->affected_rows() == 1) ? true : false;
        } else {
            // Если у пользователя есть права только на удаление своих постов
            $delete = $this->db->delete('posts', array('id' => $id, 'author' => $author));

            return ($this->db->affected_rows() == 1) ? true : false;
        }


    }

}

?>
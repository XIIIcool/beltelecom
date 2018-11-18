<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Comment_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();

        $this->load->database();
    }

    /*
     * Получить список комментариев
     */
    function getRows($id = "", $all = false, $limit = 50, $offset = 0, $per = array())
    {

        if (!empty($id)) {
            $data['post_id'] = $id;
            // Если нет прав на просмотр всех комментов то покажем толкьо проверенные
            if ($all === false) {
                $data['cheked'] = 1;
                $this->db->select('id,post_id,author,body,created,modified');
            } else $this->db->select('id,post_id,author,body,cheked,created,modified');

            $query = $this->db->get_where('comments', $data, $limit, $offset);
            $queryResult = $query->result_array();


            foreach ($queryResult as &$item) {

                $item['edit'] = false;
                $item['delete'] = false;

                if ($per['commentEditAll'] === true) {
                    $item['edit'] = true;
                } elseif ($per['commentEdit'] === true) {
                    if ($item['author'] == $per['id']) {
                        $item['edit'] = true;
                    }
                }
                if ($per['commentDeleteAll'] === true) {
                    $item['delete'] = true;
                } elseif ($per['commentDelete'] === true) {
                    if ($item['author'] == $per['id']) {
                        $item['delete'] = true;
                    }
                }
            }

            $total = $this->db->get_where('comments', $data);

            return array('items' => $queryResult, 'total' => count($total->result_array()));
        }
    }

    // изменить статус комментария

    public function change($id)
    {

        $this->db->where('id', $id);
        $result = $this->db->get('comments');
        $num = $result->row();
        if ($num->cheked == 0) {
            $n = 1;
        } else {
            $n = 0;
        }

        $data['cheked'] = $n;
        $this->db->where('id', $id);
        $this->db->update('comments', $data);

        return ($this->db->affected_rows() == 1) ? true : false;
    }

    // добавить комментарий
    public function insert($data = array())
    {
        if (!array_key_exists('created', $data)) {
            $data['created'] = date("Y-m-d H:i:s");
        }

        $data['cheked'] = 0;
        // проверим есть ли такой пост
        $this->db->get_where('posts', array('id' => $data['post_id']));
        if ($this->db->affected_rows() == 1) {

            $insert = $this->db->insert('comments', $data);
            if ($insert) {
                return $this->db->insert_id();
            } else {
                return false;
            }
        } else return false;
    }

    // обновить комментарий
    public function update($id, $data, $all = false, $author)
    {
        // Если у пользователя есть права на редактирование любого комментария
        if ($all === true) {
            //unset($data['author']);
            $data['modified'] = date("Y-m-d H:i:s");

            $this->db->where('id', $id);
            $edit = $this->db->update('comments', $data);
            return ($this->db->affected_rows() == 1) ? true : false;
        } else {
            // Если у пользователя есть права только на редактирование только своих комментариев
            $data['modified'] = date("Y-m-d H:i:s");

            $this->db->where('id = ' . $id . ' AND author = ' . $author);
            $edit = $this->db->update('comments', $data);
            // echo $this->db->last_query();
            //var_dump($this->db->affected_rows());
            return ($this->db->affected_rows() == 1) ? true : false;
        }


    }


    // Удалить комментарий
    public function delete($id, $all = false, $author)
    {
        // Если у пользователя есть права на удаление любого комментария
        if ($all === true) {
            $delete = $this->db->delete('comments', array('id' => $id));
            return ($this->db->affected_rows() == 1) ? true : false;
        } else {
            // Если у пользователя есть права только на удаление своих комментариев
            $delete = $this->db->delete('comments', array('id' => $id, 'author' => $author));
            // echo $this->db->last_query();
            //var_dump($this->db->affected_rows());
            return ($this->db->affected_rows() == 1) ? true : false;
        }


    }

}

?>
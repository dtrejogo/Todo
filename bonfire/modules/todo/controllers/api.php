<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of api
 *
 * @author dtrejogo
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class api extends Front_Controller {

    //--------------------------------------------------------------------

    public function __construct() {
        parent::__construct();


        $this->load->model('todo_model', null, true);
        $this->lang->load('todo');

        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->form_validation->CI = & $this;

        if (!class_exists('User_model')) {
            $this->load->model('users/User_model', 'user_model');
        }

        $this->load->database();

        $this->load->library('users/auth');

        if (!class_exists('Activity_model')) {
            $this->load->model('activities/Activity_model', 'activity_model', true);
        }

        $this->lang->load('users/users');

        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Content-type: application/json');

        if (!$this->auth->login($this->input->post('login'), $this->input->post('password')) === true) {
            $result->error = "Invalid Email or Password.";
            print json_encode($result);
            die();
        }
    }

    function get_entries() {

        $order = $this->input->post('order') ? $this->input->post('order') : 'date';
        $type = $this->input->post('type') ? $this->input->post('type') : 'asc';

        if ($order == 'date') {
            if ($type == 'asc') {
                $this->todo_model->order_by('todo_date', 'ASC');
            } else {
                $this->todo_model->order_by('todo_date', 'DESC');
            }
        } else {
            if ($type == 'asc') {
                $this->todo_model->order_by('todo_priority', 'ASC');
            } else {
                $this->todo_model->order_by('todo_priority', 'DESC');
            }
        }
        $result->params = $_POST;
        $result->results = $this->todo_model->find_all_by('todo_user_id',$this->auth->user_id());
        print json_encode($result);
        die();
    }

    function add_entry() {

        if ($insert_id = $this->save_todo()) {
            // Log the activity
            $this->activity_model->log_activity($this->auth->user_id(), lang('todo_act_create_record') . ': ' . $insert_id . ' : ' . $this->input->ip_address(), 'todo');

            $result->success = 1;
            $result->entry = $this->todo_model->find($insert_id);
        } else {

            $result->error = 1;
            $result->message = strip_tags(str_replace("\n", ' ', $this->form_validation->error_string()));
        }

        print json_encode($result);
        die();
    }

    public function edit_entry() {


        $id = (int) $this->input->post('id');

        if (empty($id)) {
            $result->error = 1;
            $result->message = lang('todo_invalid_id');
            print json_encode($result);
            die();
        }

        $todo = $this->todo_model->find($id);

        if (!$todo) {
            $result->error = 1;
            $result->message = lang('todo_invalid_id');
            print json_encode($result);
            die();
        }

        if ($todo->todo_user_id != $this->auth->user_id()) {
            $result->error = 1;
            $result->message = lang('todo_invalid_id');
            print json_encode($result);
            die();
        }


        if ($this->save_todo('update', $id)) {
            // Log the activity
            $this->activity_model->log_activity($this->auth->user_id(), lang('todo_act_edit_record') . ': ' . $id . ' : ' . $this->input->ip_address(), 'todo');

            $result->success = 1;
            $result->entry = $this->todo_model->find($id);
        } else {
            $result->error = 1;
            $result->message = strip_tags(str_replace("\n", ' ', $this->form_validation->error_string()));
        }


        print json_encode($result);
        die();
    }

    function complete_entry() {

        $id = (int) $this->input->post('id');

        if (empty($id)) {
            $data['error'] = 1;
            $data['message'] = lang('todo_invalid_id');
            print json_encode($data);
            die();
        }

        $todo = $this->todo_model->find($id);

        if (!$todo) {
            $result->error = 1;
            $result->message = lang('todo_invalid_id');
            print json_encode($result);
            die();
        }

        if ($todo->todo_user_id != $this->auth->user_id()) {
            $result->error = 1;
            $result->message = lang('todo_invalid_id');
            print json_encode($result);
            die();
        }

        $params['todo_completed'] = 1;

        if ($this->todo_model->update($id, $params)) {
            $result->success = 1;
            $result->entry = $this->todo_model->find($id);
        } else {
            $result->error = 1;
            $result->message = lang('todo_done_failure');
        }

        print json_encode($result);
        die();
    }

    public function remove_entry() {

        $id = (int) $this->input->post('id');

        if (empty($id)) {
            $data['error'] = 1;
            $data['message'] = lang('todo_invalid_id');
            print json_encode($data);
            die();
        }

        $todo = $this->todo_model->find($id);

        if (!$todo) {
            $result->error = 1;
            $result->message = lang('todo_invalid_id');
            print json_encode($result);
            die();
        }

        if ($todo->todo_user_id != $this->auth->user_id()) {
            $result->error = 1;
            $result->message = lang('todo_invalid_id');
            print json_encode($result);
            die();
        }

        if ($this->todo_model->delete($id)) {
            // Log the activity
            $this->activity_model->log_activity($this->auth->user_id(), lang('todo_act_delete_record') . ': ' . $id . ' : ' . $this->input->ip_address(), 'todo');

            $result->success = 1;
            $result->message = lang('todo_delete_success');
        } else {
            $result->error = 1;
            $result->message = lang('todo_delete_failure') . $this->todo_model->error;
        }


        print json_encode($result);
        die();
    }

    private function save_todo($type = 'insert', $id = 0) {

        $this->form_validation->set_rules('todo_title', 'Title', 'required|trim|xss_clean|max_length[255]');
        $this->form_validation->set_rules('todo_description', 'Description', 'trim|xss_clean');
        $this->form_validation->set_rules('todo_date', 'date', 'required|trim|xss_clean|valid_datetime');
        $this->form_validation->set_rules('todo_priority', 'Priority', 'required|xss_clean|max_length[1]|numeric|greater_than[0]|less_than[4]');
        $this->form_validation->set_rules('todo_completed', 'Completed', 'xss_clean|max_length[1]|numeric|valid_completed|greater_than[-1]|less_than[2]');
        $this->form_validation->set_rules('todo_user_id', 'User', 'xss_clean|max_length[11]');


        if ($this->form_validation->run() === FALSE) {
            return FALSE;
        }



        $data = array();
        $data['todo_title'] = $this->input->post('todo_title');
        $data['todo_description'] = $this->input->post('todo_description');
        $data['todo_date'] = date('y-m-d H:i:s', strtotime($this->input->post('todo_date')));
        $data['todo_priority'] = $this->input->post('todo_priority');
        $data['todo_completed'] = $this->input->post('todo_completed');
        $data['todo_user_id'] = $this->auth->user_id();

        if ($type == 'insert') {
            $id = $this->todo_model->insert($data);

            if (is_numeric($id)) {
                $return = $id;
            } else {
                $return = FALSE;
            }
        } else if ($type == 'update') {
            $return = $this->todo_model->update($id, $data);
        }

        return $return;
    }

}


<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class content extends Admin_Controller {

    //--------------------------------------------------------------------

    public function __construct() {
        parent::__construct();

        $this->auth->restrict('Todo.Content.View');
        $this->load->model('todo_model', null, true);
        $this->lang->load('todo');


        Assets::add_css('flick/jquery-ui-1.8.13.custom.css');
        Assets::add_css('jquery-ui-timepicker.css');
        Assets::add_js('jquery-ui-timepicker-addon.js');
    }

    //--------------------------------------------------------------------

    /*
      Method: index()

      Displays a list of form data.
     */
    public function index() {
        Assets::add_js($this->load->view('content/js', null, true), 'inline');


        $order = $this->uri->segment(5) ? $this->uri->segment(5) : 'date';
        $type = $this->uri->segment(6) ? $this->uri->segment(6) : 'asc';

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


        Template::set('type', $type);
        Template::set('order', $order);
        Template::set('priorities', $this->todo_model->get_priorities());


        Template::set('records', $this->todo_model->find_all_by('todo_user_id',$this->auth->user_id()));
        Template::set('toolbar_title', "Manage Todo");
        Template::render();
    }

    //--------------------------------------------------------------------

    /*
      Method: create()

      Creates a Todo object.
     */
    public function create() {
        $this->auth->restrict('Todo.Content.Create');

        $params['priorities'] = $this->todo_model->get_priorities();
        $params['toolbar_title'] = lang('todo_create') . ' Todo';

        if ($this->input->post('submit')) {
            if ($insert_id = $this->save_todo()) {
                // Log the activity
                $this->activity_model->log_activity($this->auth->user_id(), lang('todo_act_create_record') . ': ' . $insert_id . ' : ' . $this->input->ip_address(), 'todo');

                Template::set_message(lang("todo_create_success"), 'success');

                $data['success'] = 1;
                $data['redirect'] = site_url('admin/content/todo#index');
            } else {

                $data['error'] = 1;
                $data['view'] = $this->load->view('todo/content/create', $params, true);
            }
            
            $data['message'] = Template::message();
            print json_encode($data);
            die();
        }

        Template::set('data', $params);
        Template::render();
    }

    //--------------------------------------------------------------------

    /*
      Method: edit()

      Allows editing of Todo data.
     */
    public function edit() {
        $this->auth->restrict('Todo.Content.Edit');

        $id = (int) $this->uri->segment(5);

        if (empty($id)) {
            Template::set_message(lang('todo_invalid_id'), 'error');
            print Template::message();
            die();
        }

        $params['priorities'] = $this->todo_model->get_priorities();
        $params['toolbar_title'] = lang('todo_edit') . ' Todo';
        $params['todo'] = $this->todo_model->find($id);
        

        if(!$params['todo']){           
            Template::set_message(lang('todo_invalid_id'), 'error');
            print Template::message();
            die();          
        }
        
        if ($params['todo']->todo_user_id != $this->auth->user_id()){
            Template::set_message(lang('todo_invalid_id'), 'error');
            print Template::message();
            die();
        }
        
        $params['todo']->todo_date = date('m/d/Y h:i:s', strtotime($params['todo']->todo_date));
        
        if ($this->input->post('submit')) {
            if ($this->save_todo('update', $id)) {
                // Log the activity
                $this->activity_model->log_activity($this->auth->user_id(), lang('todo_act_edit_record') . ': ' . $id . ' : ' . $this->input->ip_address(), 'todo');

                Template::set_message(lang('todo_edit_success'), 'success');
                $data['success'] = 1;
            } else {
                $data['error'] = 1;
            }
            $data['message'] = Template::message();
            $data['view'] = $this->load->view('todo/content/edit', $params, true);
            print json_encode($data);
            die();
        }

        Template::set('data', $params);
        Template::render();
    }

    //--------------------------------------------------------------------

    /*
      Method: delete()

      Allows deleting of Todo data.
     */
    public function delete() {
        $this->auth->restrict('Todo.Content.Delete');

        $id = $this->uri->segment(5);
        
        $todo = $this->todo_model->find($id);
        
        if ($todo->todo_user_id != $this->auth->user_id()){
            Template::set_message(lang('todo_invalid_id'), 'error');
            $data['message'] = Template::message();
            $data['redirect'] = site_url(SITE_AREA . '/content/todo/#index');
            print json_encode($data);
            die();
        }

        if (!empty($id)) {
            if ($this->todo_model->delete($id)) {
                // Log the activity
                $this->activity_model->log_activity($this->auth->user_id(), lang('todo_act_delete_record') . ': ' . $id . ' : ' . $this->input->ip_address(), 'todo');

                Template::set_message(lang('todo_delete_success'), 'success');
                $data['success'] = 1;
            } else {
                Template::set_message(lang('todo_delete_failure') . $this->todo_model->error, 'error');
                $data['error'] = 1;
            }

            $data['message'] = Template::message();
            $data['redirect'] = site_url(SITE_AREA . '/content/todo/#index');
            print json_encode($data);
            die();
        }
        //redirect(SITE_AREA . '/content/todo');
    }

    function done() {

        $this->auth->restrict('Todo.Content.Edit');

        $id = (int) $this->uri->segment(5);

        if (empty($id)) {
            Template::set_message(lang('todo_invalid_id'), 'error');
            $data['error'] = 1;
            $data['message'] = Template::message();
            print json_encode($data);
            die();
        }
        
        
        $todo = $this->todo_model->find($id);
        
        if ($todo->todo_user_id != $this->auth->user_id()){
            Template::set_message(lang('todo_invalid_id'), 'error');
            $data['message'] = Template::message();
            $data['redirect'] = site_url(SITE_AREA . '/content/todo/#index');
            print json_encode($data);
            die();
        }
        
        
        $params['todo_completed'] = 1;
        if ($this->todo_model->update($id, $params)) {
            Template::set_message(lang('todo_done_success'), 'success');
            $data['success'] = 1;
        } else {
            Template::set_message(lang('todo_done_failure') . $this->todo_model->error, 'error');
            $data['error'] = 1;
        }

        $data['message'] = Template::message();
        print json_encode($data);
        die();
    }

    //--------------------------------------------------------------------
    //--------------------------------------------------------------------
    // !PRIVATE METHODS
    //--------------------------------------------------------------------

    /*
      Method: save_todo()

      Does the actual validation and saving of form data.

      Parameters:
      $type	- Either "insert" or "update"
      $id		- The ID of the record to update. Not needed for inserts.

      Returns:
      An INT id for successful inserts. If updating, returns TRUE on success.
      Otherwise, returns FALSE.
     */
    private function save_todo($type = 'insert', $id = 0) {

        $this->form_validation->set_rules('todo_title', 'Title', 'required|trim|xss_clean|max_length[255]');
        $this->form_validation->set_rules('todo_description', 'Description', 'trim|xss_clean');
        $this->form_validation->set_rules('todo_date', 'date', 'required|trim|xss_clean|valid_datetime');
        $this->form_validation->set_rules('todo_priority', 'Priority', 'required|xss_clean|max_length[1]numeric|greater_than[0]|less_than[4]');
        $this->form_validation->set_rules('todo_completed', 'Completed', 'xss_clean|max_length[1]|numeric|greater_than[-1]|less_than[2]');
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

    //--------------------------------------------------------------------
}
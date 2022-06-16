<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Admin extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        cek_login();
    }

    public function index()
    {
        $data['title'] = 'Dashboard';
        $data['user'] = $this->db->get_where('user', ['username' => $this->session->userdata('username')])->row_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('admin/index', $data);
        $this->load->view('templates/footer');
    }

    public function user_list()
    {
        $data['title'] = 'User List';
        $data['user'] = $this->db->get_where('user', ['username' => $this->session->userdata('username')])->row_array();
        $userListdata['user'] = $this->db->get('user')->result_array();


        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('admin/user_list', $userListdata);
        $this->load->view('templates/footer');
    }

    public function add_user()
    {
        $data['title'] = 'Add User';
        $this->load->library('form_validation');
        $this->form_validation->set_rules('name', 'Name', 'required|trim');
        $this->form_validation->set_rules('username', 'Username', 'required|trim|is_unique[user.username]', [
            'is_unique' => 'This username has already registered!'
        ]);
        $this->form_validation->set_rules('password1', 'Password', 'required|trim|min_length[6]|matches[password2]', [
            'matches' => 'Password dont match!',
            'min_length' => 'Password too short!'
        ]);
        $this->form_validation->set_rules('password2', 'Password', 'required|trim|matches[password1]');

        $data['user'] = $this->db->get_where('user', ['username' => $this->session->userdata('username')])->row_array();


        if ($this->form_validation->run() == FALSE) {
            $this->load->view('templates/header', $data);
            $this->load->view('templates/sidebar', $data);
            $this->load->view('templates/topbar', $data);
            $this->load->view('admin/add_user');
            $this->load->view('templates/footer');
        } else {
            $dataInsert = [
                'name' => ($this->input->post('name', true)),
                'username' => htmlspecialchars($this->input->post('username', true)),
                'password' => password_hash($this->input->post('password1'), PASSWORD_DEFAULT),
                'image' => 'default.jpg',
                'role_id' => 2,
                'date_created' => time()

            ];

            $this->db->insert('user', $dataInsert);
            $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Congratulation! Account has been created.</div>');

            redirect('admin/user_list');
        }
    }

    public function delete_user($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('user');
        $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Account has been deleted.</div>');
        redirect('admin/user_list');
    }

    public function edit_user($id)
    {
        $data['title'] = 'Edit User';


        $data['user'] = $this->db->get_where('user', ['username' => $this->session->userdata('username')])
            ->row_array();
        $userListdata['user'] = $this->db->get_where('user', ['id' => $id])->result_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('admin/edit_user', $userListdata);
        $this->load->view('templates/footer');
    }

    public function update_user()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('name', 'Name', 'required|trim');
        $this->form_validation->set_rules('username', 'Username', 'required|trim|');

        $dataUpdate = [
            'name' => ($this->input->post('name', true)),
            'username' => htmlspecialchars($this->input->post('username', true)),
            'role_id' => ($this->input->post('role_id', true))
        ];

        $this->db->where('id', $this->input->post('id'));
        $this->db->update('user', $dataUpdate);
        $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Congratulation! Account has been changed.</div>');

        redirect('admin/user_list');
    }
}

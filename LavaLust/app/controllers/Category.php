<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class Category extends Controller {

    public function __construct() {
        parent::__construct();
        $this->call->model('Category_model');
        $this->call->library('session');

        // Middleware check: Force login
        if (!$this->session->userdata('logged_in')
            || strtolower((string) $this->session->userdata('role_name')) !== 'admin') {
            redirect('auth/login');
        }
    }

    // READ ALL
    public function index() {
        $data = [
            'categories' => $this->Category_model->get_all(),
            'success' => $this->session->flashdata('success'),
            'error' => $this->session->flashdata('error')
        ];
        $this->call->view('categories/index', $data);
    }

    // CREATE VIEW
    public function create() {
        $this->call->view('categories/create', [
            'error' => $this->session->flashdata('error')
        ]);
    }

    // STORE DATA
    public function store() {
        $post = $this->request->post();
        $categoryName = isset($post['category_name']) && is_string($post['category_name'])
            ? trim($post['category_name'])
            : '';
        $description = isset($post['description']) && is_string($post['description'])
            ? trim($post['description'])
            : '';

        if (preg_match('/^.{1,100}$/us', $categoryName) !== 1) {
            $this->session->set_flashdata('error', 'Category name is required and must be 100 characters or fewer.');
            redirect('category/create');
        }

        $data = [
            'category_name' => $categoryName,
            'description'   => $description === '' ? null : $description,
            'is_active'     => isset($post['is_active']) && $post['is_active'] === '1' ? 1 : 0
        ];

        $this->Category_model->insert($data);
        $this->session->set_flashdata('success', 'Category added successfully.');
        redirect('category');
    }

    // EDIT VIEW
    public function edit($id) {
        if (!ctype_digit((string) $id) || (int) $id < 1) {
            redirect('category');
        }

        $data['category'] = $this->Category_model->get_by_id($id);
        if (!$data['category']) {
            $this->session->set_flashdata('error', 'Category not found.');
            redirect('category');
        }
        $data['error'] = $this->session->flashdata('error');
        $this->call->view('categories/edit', $data);
    }

    // UPDATE DATA
    public function update($id) {
        if (!ctype_digit((string) $id) || (int) $id < 1) {
            redirect('category');
        }

        $post = $this->request->post();
        $categoryName = isset($post['category_name']) && is_string($post['category_name'])
            ? trim($post['category_name'])
            : '';
        $description = isset($post['description']) && is_string($post['description'])
            ? trim($post['description'])
            : '';

        if (preg_match('/^.{1,100}$/us', $categoryName) !== 1) {
            $this->session->set_flashdata('error', 'Category name is required and must be 100 characters or fewer.');
            redirect('category/edit/' . $id);
        }

        $data = [
            'category_name' => $categoryName,
            'description'   => $description === '' ? null : $description,
            'is_active'     => isset($post['is_active']) && $post['is_active'] === '1' ? 1 : 0
        ];

        if (!$this->Category_model->get_by_id($id)) {
            $this->session->set_flashdata('error', 'Category not found.');
            redirect('category');
        }

        $this->Category_model->update($id, $data);
        $this->session->set_flashdata('success', 'Category updated successfully.');
        redirect('category');
    }

    // DELETE DATA
    public function delete($id) {
        if (!ctype_digit((string) $id) || (int) $id < 1) {
            redirect('category');
        }

        if (!$this->Category_model->get_by_id($id)) {
            $this->session->set_flashdata('error', 'Category not found.');
            redirect('category');
        }

        $this->Category_model->delete($id);
        $this->session->set_flashdata('success', 'Category deleted successfully.');
        redirect('category');
    }
}
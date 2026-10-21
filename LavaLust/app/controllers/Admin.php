<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class Admin extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->call->model('Admin_model');
        $this->call->library('session');

        if (!$this->session->userdata('logged_in')
            || strtolower((string) $this->session->userdata('role_name')) !== 'admin') {
            redirect('auth/login');
        }
    }

    public function index()
    {
        $data = $this->Admin_model->get_dashboard_data();
        $data['tables'] = $this->Admin_model->get_tables();
        $this->render('dashboard', $data);
    }

    public function table($table)
    {
        $info = $this->get_table_info($table);
        if ($info === null) {
            return;
        }

        $searchInput = $this->request->get('q', '');
        $pageInput = $this->request->get('page', '');
        $search = is_string($searchInput) ? trim($searchInput) : '';
        $page = is_string($pageInput) && ctype_digit($pageInput) ? (int) $pageInput : 1;
        $result = $this->Admin_model->get_rows($table, $search, $page);
        $data = [
            'tables' => $this->Admin_model->get_tables(),
            'table' => $table,
            'table_info' => $info,
            'columns' => $this->Admin_model->get_column_names($table),
            'rows' => $result['rows'],
            'total' => $result['total'],
            'search' => $search,
            'search_column' => $result['search_column'],
            'page' => $result['page'],
            'page_size' => $result['page_size'],
            'current_user_id' => $this->session->userdata('user_id'),
            'message' => $this->session->flashdata('success'),
            'error' => $this->session->flashdata('error')
        ];
        $this->render('table', $data);
    }

    public function create($table)
    {
        $info = $this->get_table_info($table);
        if ($info === null) {
            return;
        }
        if (!empty($info['read_only'])) {
            $this->session->set_flashdata('error', 'This table is read-only to preserve its audit records.');
            redirect('admin/tables/' . $table);
        }

        $this->render_form($table, $info, null);
    }

    public function store($table)
    {
        $info = $this->get_table_info($table);
        if ($info === null) {
            return;
        }

        $validation = $this->Admin_model->validate_form_data($table, $this->request->post(), false);
        if ($validation['error'] !== null) {
            $this->session->set_flashdata('error', $validation['error']);
            redirect('admin/tables/' . $table . '/create');
        }

        try {
            $this->Admin_model->insert_record($table, $validation['data']);
        } catch (PDOException $exception) {
            error_log('Admin insert failed for ' . $table . ': ' . $exception->getMessage());
            $this->session->set_flashdata('error', 'Could not save this record. Check that unique values are not already in use and related records exist.');
            redirect('admin/tables/' . $table . '/create');
        }

        $this->session->set_flashdata('success', $info['label'] . ' record created.');
        redirect('admin/tables/' . $table);
    }

    public function edit($table, $id)
    {
        $info = $this->get_table_info($table);
        if ($info === null) {
            return;
        }
        if (!ctype_digit((string) $id) || (int) $id < 1) {
            redirect('admin/tables/' . $table);
        }
        if ($table === 'users' && (string) $id === (string) $this->session->userdata('user_id')) {
            $this->session->set_flashdata('error', 'You cannot edit your own account from this screen.');
            redirect('admin/tables/' . $table);
        }

        $record = $this->Admin_model->get_record($table, $id);
        if (!$record) {
            $this->session->set_flashdata('error', 'Record not found.');
            redirect('admin/tables/' . $table);
        }
        if (!empty($info['read_only'])) {
            $this->session->set_flashdata('error', 'This table is read-only to preserve its audit records.');
            redirect('admin/tables/' . $table);
        }

        $this->render_form($table, $info, $record);
    }

    public function update($table, $id)
    {
        $info = $this->get_table_info($table);
        if ($info === null) {
            return;
        }
        if (!ctype_digit((string) $id) || (int) $id < 1) {
            redirect('admin/tables/' . $table);
        }
        if ($table === 'users' && (string) $id === (string) $this->session->userdata('user_id')) {
            $this->session->set_flashdata('error', 'You cannot edit your own account from this screen.');
            redirect('admin/tables/' . $table);
        }

        $record = $this->Admin_model->get_record($table, $id);
        if (!$record) {
            $this->session->set_flashdata('error', 'Record not found.');
            redirect('admin/tables/' . $table);
        }

        $validation = $this->Admin_model->validate_form_data($table, $this->request->post(), true);
        if ($validation['error'] !== null) {
            $this->session->set_flashdata('error', $validation['error']);
            redirect('admin/tables/' . $table . '/edit/' . $id);
        }

        try {
            $this->Admin_model->update_record($table, $id, $validation['data']);
        } catch (PDOException $exception) {
            error_log('Admin update failed for ' . $table . ': ' . $exception->getMessage());
            $this->session->set_flashdata('error', 'Could not update this record. Check that unique values are not already in use and related records exist.');
            redirect('admin/tables/' . $table . '/edit/' . $id);
        }

        $this->session->set_flashdata('success', $info['label'] . ' record updated.');
        redirect('admin/tables/' . $table);
    }

    public function delete($table, $id)
    {
        $info = $this->get_table_info($table);
        if ($info === null) {
            return;
        }
        if (!empty($info['read_only'])) {
            $this->session->set_flashdata('error', 'This table is read-only to preserve its audit records.');
            redirect('admin/tables/' . $table);
        }
        if (!ctype_digit((string) $id) || (int) $id < 1) {
            redirect('admin/tables/' . $table);
        }
        if ($table === 'users' && (string) $id === (string) $this->session->userdata('user_id')) {
            $this->session->set_flashdata('error', 'You cannot delete your own account.');
            redirect('admin/tables/' . $table);
        }

        try {
            $deleted = $this->Admin_model->delete_record($table, $id);
        } catch (PDOException $exception) {
            error_log('Admin delete failed for ' . $table . ': ' . $exception->getMessage());
            $this->session->set_flashdata('error', 'Could not delete this record. It may be referenced by other records; deactivate it instead where possible.');
            redirect('admin/tables/' . $table);
        }

        $this->session->set_flashdata('success', $deleted ? $info['label'] . ' record deleted.' : 'No record was deleted.');
        redirect('admin/tables/' . $table);
    }

    private function get_table_info($table)
    {
        try {
            return $this->Admin_model->get_table_info($table);
        } catch (InvalidArgumentException $exception) {
            $this->session->set_flashdata('error', 'That data table is not available.');
            redirect('admin');
            return null;
        }
    }

    private function render_form($table, $info, $record)
    {
        $schema = $this->Admin_model->get_schema($table);
        $fields = [];
        foreach ($schema as $field) {
            if (!$field['form']) {
                continue;
            }
            $field['options'] = $this->Admin_model->get_reference_options($table, $field);
            $field['value'] = $record[$field['name']] ?? '';
            $fields[] = $field;
        }

        $this->render('form', [
            'tables' => $this->Admin_model->get_tables(),
            'table' => $table,
            'table_info' => $info,
            'fields' => $fields,
            'record' => $record,
            'error' => $this->session->flashdata('error')
        ]);
    }

    private function render($page, $data)
    {
        $data['view_page'] = $page;
        $data['admin_name'] = $this->session->userdata('full_name') ?: 'Administrator';
        $data['message'] = $data['message'] ?? $this->session->flashdata('success');
        $data['error'] = $data['error'] ?? $this->session->flashdata('error');
        $this->call->view('admin/layout', $data);
    }
}

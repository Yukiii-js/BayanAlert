<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class Category_model extends Model {

    protected $table = 'hazard_categories';
    protected $primary_key = 'category_id';
    protected $fillable = ['category_name', 'description', 'is_active'];

    public function get_all() {
        return $this->all();
    }

    public function get_by_id($id) {
        return $this->find($id);
    }

    public function insert($data) {
        return $this->_insert($data);
    }

    public function update($id, $data) {
        return $this->_update($id, $data);
    }

    public function delete($id) {
        return $this->_delete($id);
    }
}
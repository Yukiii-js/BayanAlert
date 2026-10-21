<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class Auth_model extends Model {

    protected $table = 'users';
    protected $primary_key = 'user_id';

    public function get_user_by_email_or_username($login) {
        return $this->db->table($this->table)
                        ->join('roles', 'roles.role_id = users.role_id')
                        ->select('users.user_id, users.role_id, users.full_name, users.password_hash, users.account_status, roles.role_name')
                        ->where('users.email', $login)
                        ->or_where('users.username', $login)
                        ->get();
    }
}
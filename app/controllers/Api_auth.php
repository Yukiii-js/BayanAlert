<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class Api_auth extends Controller {

    public function __construct() {
        parent::__construct();
        $this->call->model('Auth_model');
    }

    public function login() {
        $input = get_request_input();
        $username = isset($input['username']) && is_string($input['username']) ? trim($input['username']) : '';
        $password = isset($input['password']) && is_string($input['password']) ? $input['password'] : '';

        if (empty($username) || empty($password)) {
            api_error('Username/email and password are required.', 400);
        }

        try {
            $user = $this->Auth_model->get_user_by_email_or_username($username);
        } catch (\Exception $e) {
            api_error('Database error: ' . $e->getMessage(), 500);
        }

        if (!$user) {
            api_error('Invalid credentials.', 401);
        }

        if ($user['account_status'] !== 'active') {
            api_error('Account is deactivated. Please contact an administrator.', 403);
        }

        if (!password_verify($password, $user['password_hash'])) {
            api_error('Invalid credentials.', 401);
        }

        $tokenPayload = [
            'user_id'   => $user['user_id'],
            'full_name' => $user['full_name'],
            'role_id'   => $user['role_id'],
            'role_name' => $user['role_name'],
            'sub'       => $user['user_id']
        ];

        $token = generate_jwt_token($tokenPayload, 86400 * 7); // 7 days

        api_response([
            'token' => $token,
            'user'  => [
                'user_id'   => $user['user_id'],
                'full_name' => $user['full_name'],
                'role_id'   => $user['role_id'],
                'role_name' => $user['role_name']
            ]
        ], 200, 'Login successful.');
    }

    public function me() {
        $user = require_api_auth();
        api_response([
            'user' => [
                'user_id'   => $user['user_id'],
                'full_name' => $user['full_name'],
                'role_id'   => $user['role_id'],
                'role_name' => $user['role_name']
            ]
        ]);
    }

    public function logout() {
        api_response(null, 200, 'Logged out successfully.');
    }
}

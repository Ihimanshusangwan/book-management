<?php

class Auth {
    private $userModel;
    private $session;

    public function __construct(User $userModel, Session $session) {
        $this->userModel = $userModel;
        $this->session = $session;
    }

    public function register($username, $email, $password, $role) {
        return $this->userModel->create($username, $email, $password, $role);
    }

    public function login($username, $password) {
        $user = $this->userModel->findByUsername($username);
        if ($user && password_verify($password, $user['password'])) {
            $this->session->set('user_id', $user['id']);
            $this->session->set('username', $user['username']);
            $this->session->set('role', $user['role']);
            return true;
        }
        return false;
    }

    public function check() {
        return $this->session->get('user_id') !== null;
    }

    public function user() {
        if ($this->check()) {
            return [
                'user_id' => $this->session->get('user_id'),
                'username' => $this->session->get('username'),
                'role' => $this->session->get('role')
            ];
        }
        return null;
    }

    public function logout() {
        $this->session->destroy();
    }

    public function hasRole($role) {
        $userRole = $this->session->get('role');
        return $userRole === $role;
    }

    public function checkRole($role) {
        $userRole = $this->session->get('role');
        return $userRole === $role;
    }
}

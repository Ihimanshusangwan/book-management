<?php
namespace Controllers;

use Models\User;
use Core\Auth;
use Core\Response;
use Core\Session;
use Helpers\Validator;
use Core\Route;


class UserController
{
    private $auth;

    public function __construct()
    {
        $this->auth = new Auth(new User(), new Session());
    }

    #[Route('POST', '/api/user/register')]
    public function register()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        $validationRules = [
            'username' => 'required|alpha|min:3|max:20',
            'email' => 'required|email',
            'password' => 'required|min:6|max:20'
        ];

        $errors = Validator::validate($data, $validationRules);

        if (!empty($errors)) {
            Response::json(['success' => false, 'message' => $errors], 400);
            return;
        }

        if ($this->auth->register($data['username'], $data['email'], $data['password'], 'user')) {
            Response::json(['success' => true,'message' => 'Registration successful!']);
        } else {
            Response::json(['success' => false,'message' => 'Error occurred during registration.'], 400);
        }
    }


    #[Route('POST', '/api/user/login')]
    public function login()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $validationRules = [
            'username' => 'required|alpha|min:3|max:20',
            'password' => 'required|min:6|max:20'
        ];
        $errors = Validator::validate($data, $validationRules);
        if (!empty($errors)) {
            Response::json(['success' => false, 'message' => $errors], 400);
            return;
        }

        if ($this->auth->login($data['username'], $data['password'])) {
            Response::json(['success' => true,'message' => 'Login successful!']);
        } else {
            Response::json(['success' => false, 'message' => 'Invalid credentials.'], 401);
        }
    }

    #[Route('GET', '/api/user/profile', 'user')]
    public function profile()
    {
        $user = $this->auth->user();
        Response::json(['success' => true, 'data' => $user]);
    }

    #[Route('POST', '/api/user/logout', 'user')]
    public function logout()
    {
        $this->auth->logout();
        Response::json(['success' => false, 'message' => 'Logout successful!']);
    }
}

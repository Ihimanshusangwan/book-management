<?php
namespace Core;

class Middleware {
    private $auth;

    public function __construct(Auth $auth) {
        $this->auth = $auth;
    }

    public function handle(callable $callback, ?string $requiredRole = null) {
        if ($requiredRole === null) {
            return $callback();
        }

        if ($this->auth->check()) {
            if (!$this->auth->hasRole($requiredRole)) {
                Response::json(['success' => false, 'message' => 'Access Forbidden'], 403);
                return;
            }
            return $callback();
        } else {
            Response::json(['success' => false, 'message' => 'Unauthorized Access'], 401);
        }
    }
}

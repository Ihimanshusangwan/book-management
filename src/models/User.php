<?php

class User {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($username, $email, $password, $role = 'user') {
        $sql = 'INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)';
        
        try {
            $stmt = $this->db->prepare($sql);
            if (!$stmt) {
                throw new Exception('Prepare statement failed: ' . $this->db->error);
            }
            
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $stmt->bind_param('ssss', $username, $email, $hashedPassword, $role);
            
            $stmt->execute();
            $stmt->close();
            return true;
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() === 1062) {
                $duplicateField = '';
                if (strpos($e->getMessage(), "for key 'users.username'") !== false) {
                    $duplicateField = 'Username';
                } elseif (strpos($e->getMessage(), "for key 'users.email'") !== false) {
                    $duplicateField = 'Email';
                } else {
                    $duplicateField = 'Field';
                }
                
                Response::json(['success' => false, 'message' => "$duplicateField already exists."], 400);
            } else {
                Response::json(['success' => false, 'message' => 'Execute statement failed: ' . $e->getMessage()], 400);
            }
            exit();
        } catch (Exception $e) {
            Response::json(['success' => false, 'message' => $e->getMessage()], 400);
            exit();
        }
    }
    public function findByUsername($username) {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE username = ?');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}

<?php
namespace Models;

use Core\Database;

class Book {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($title, $author, $genre, $publication_date) {
        $stmt = $this->db->prepare('INSERT INTO books (title, author, genre, publication_date) VALUES (?, ?, ?, ?)');
        $stmt->bind_param('ssss', $title, $author, $genre, $publication_date);
        return $stmt->execute();
    }

    public function getAll() {
        $result = $this->db->query('SELECT * FROM books');
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->db->prepare('SELECT * FROM books WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function update($id, $title, $author, $genre, $publication_date) {
        $stmt = $this->db->prepare('UPDATE books SET title = ?, author = ?, genre = ?, publication_date = ? WHERE id = ?');
        $stmt->bind_param('ssssi', $title, $author, $genre, $publication_date, $id);
        return $stmt->execute();
    }

    public function delete($id) {
        $stmt = $this->db->prepare('DELETE FROM books WHERE id = ?');
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }
    public function search($title = null, $author = null, $genre = null) {
        $query = 'SELECT * FROM books WHERE 1=1';
        $params = [];
        $types = '';
    
        if ($title) {
            $query .= ' AND title LIKE ?';
            $params[] = '%' . $title . '%';
            $types .= 's';
        }
        if ($author) {
            $query .= ' AND author LIKE ?';
            $params[] = '%' . $author . '%';
            $types .= 's';
        }
        if ($genre) {
            $query .= ' AND genre LIKE ?';
            $params[] = '%' . $genre . '%';
            $types .= 's';
        }
    
        $stmt = $this->db->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
}

<?php

require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../models/Book.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/Response.php';

class BookController {
    private $auth;
    private $bookModel;

    public function __construct() {
        $this->auth = new Auth(new User(), new Session());
        $this->bookModel = new Book();
    }

    #[Route('POST', '/api/book/create', 'user')]
    public function create() {
        $data = json_decode(file_get_contents('php://input'), true);
    
        $validationRules = [
            'title' => 'required|min:3|max:255',
            'author' => 'required|min:3|max:255',
            'genre' => 'required|min:3|max:255',
            'publication_date' => 'required'
        ];
        $errors = Validator::validate($data, $validationRules);
    
        if (!empty($errors)) {
            Response::json(['success' => false, 'message' => $errors], 400);
            return;
        }
        if ($this->bookModel->create($data['title'], $data['author'], $data['genre'], $data['publication_date'])) {
            Response::json(['success' => true, 'message' => 'Book added successfully!']);
        } else {
            Response::json(['success' => false, 'message' => 'Error occurred while adding the book.'], 400);
        }
    }
    

    #[Route('GET', '/api/books', 'user')]
    public function getAll() {
        $books = $this->bookModel->getAll();
        Response::json(['success' => true, 'data' => $books]);
    }

    #[Route('GET', '/api/book/{id}', 'user')]
    public function getById($id) {
        $book = $this->bookModel->getById($id);
        if ($book) {
            Response::json(['success' => true, 'data' => $book]);
        } else {
            Response::json([ 'success' => true, 'message' => 'Book not found.'], 404);
        }
    }

    #[Route('PUT', '/api/book/{id}', 'user')]
    public function update($id) {
        $data = json_decode(file_get_contents('php://input'), true);
        $validationRules = [
            'title' => 'required|min:3|max:255',
            'author' => 'required|min:3|max:255',
            'genre' => 'required|min:3|max:255',
            'publication_date' => 'required'
        ];
    
        $errors = Validator::validate($data, $validationRules);
    
        if (!empty($errors)) {
            Response::json(['success' => false, 'message' => $errors], 400);
            return;
        }
    
        if ($this->bookModel->update($id, $data['title'], $data['author'], $data['genre'], $data['publication_date'])) {
            Response::json(['success' => true, 'message' => 'Book updated successfully!']);
        } else {
            Response::json([ 'success' => false, 'message' => 'Error occurred while updating the book.'], 400);
        }
    }
    

    #[Route('DELETE', '/api/book/{id}', 'user')]
    public function delete($id) {
        if ($this->bookModel->delete($id)) {
            Response::json(['success' => true, 'message' => 'Book deleted successfully!']);
        } else {
            Response::json(['success' => false,'message' => 'Error occurred while deleting the book.'], 400);
        }
    }

    #[Route('GET', '/api/books/search', 'user')]
    public function search() {
        $queryParams = $_GET;
        $title = isset($queryParams['title']) ? trim($queryParams['title']) : null;
        $author = isset($queryParams['author']) ? trim($queryParams['author']) : null;
        $genre = isset($queryParams['genre']) ? trim($queryParams['genre']) : null;
    
        $validationRules = [
            'title' => 'min:3|max:255',
            'author' => 'min:3|max:255',
            'genre' => 'min:3|max:255',
        ];
    
        $data = [
            'title' => $title,
            'author' => $author,
            'genre' => $genre
        ];
    
        $errors = Validator::validate($data, $validationRules);
    
        if (!empty($errors)) {
            Response::json(['success' => false, 'message' => $errors], 400);
            return;
        }
    
        try {
            $books = $this->bookModel->search($title, $author, $genre);
            Response::json(['success' => true, 'books' => $books]);
        } catch (Exception $e) {
            Response::json(['success' => false, 'message' => 'Error occurred while searching for books.'], 500);
        }
    }
    
}

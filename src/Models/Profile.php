<?php
class User {
    public $id;
    public $username;
    public $password;
    public $full_name;
    public $email;
    public $role;
    public $created_at;

    public function __construct($data = []) {
        // Đồng bộ các thuộc tính với cột trong bảng users
        $this->id         = $data['id'] ?? null;
        $this->username   = $data['username'] ?? null;
        $this->password   = $data['password'] ?? null;
        $this->full_name  = $data['full_name'] ?? null;
        $this->email      = $data['email'] ?? null;
        $this->role       = $data['role'] ?? 'member';
        $this->created_at = $data['created_at'] ?? null;
    }
}
?>
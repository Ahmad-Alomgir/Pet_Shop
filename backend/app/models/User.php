<?php

class User extends Model
{
    protected $table = 'users';

    public function __construct()
    {
        parent::__construct();
        $this->ensureTableExists();
    }

    private function ensureTableExists()
    {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(120) NOT NULL,
                email VARCHAR(150) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                phone VARCHAR(50),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
        $this->db->execute();
    }

    public function findByEmail($email)
    {
        $this->db->query("SELECT * FROM {$this->table} WHERE email = :email LIMIT 1");
        $this->db->bind(':email', $email);
        return $this->db->single();
    }

    public function create($data)
    {
        $this->db->query("
            INSERT INTO {$this->table} (name, email, password, phone)
            VALUES (:name, :email, :password, :phone)
        ");
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':password', $data['password']);
        $this->db->bind(':phone', $data['phone']);
        return $this->db->execute();
    }

    public function updateProfile($id, $name, $phone)
    {
        $this->db->query("
            UPDATE {$this->table}
            SET name = :name, phone = :phone
            WHERE id = :id
        ");
        $this->db->bind(':id', $id);
        $this->db->bind(':name', $name);
        $this->db->bind(':phone', $phone);
        return $this->db->execute();
    }
}

<?php

class Admin extends Model
{
    protected $table = 'admins';

    /*
    |--------------------------------------------------------------------------
    | Find Admin by Email
    |--------------------------------------------------------------------------
    */
    public function findByEmail($email)
    {
        $this->db->query("SELECT * FROM {$this->table} WHERE email = :email LIMIT 1");
        $this->db->bind(':email', $email);
        return $this->db->single();
    }

    /*
    |--------------------------------------------------------------------------
    | Create Admin (optional, for setup)
    |--------------------------------------------------------------------------
    */
    public function create($data)
    {
        $this->db->query("
            INSERT INTO {$this->table} (name, email, password)
            VALUES (:name, :email, :password)
        ");

        $this->db->bind(':name', $data['name']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':password', $data['password']);

        return $this->db->execute();
    }
}
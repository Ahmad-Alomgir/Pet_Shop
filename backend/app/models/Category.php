<?php

class Category extends Model
{
    protected $table = 'categories';

    /*
    |--------------------------------------------------------------------------
    | Create Category
    |--------------------------------------------------------------------------
    */
    public function create($data)
    {
        $this->db->query("INSERT INTO {$this->table} (name, image) VALUES (:name, :image)");
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':image', $data['image']);
        return $this->db->execute();
    }

    /*
    |--------------------------------------------------------------------------
    | Update Category
    |--------------------------------------------------------------------------
    */
    public function update($id, $data)
    {
        $this->db->query("UPDATE {$this->table} SET name = :name, image = :image WHERE id = :id");
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':image', $data['image']);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
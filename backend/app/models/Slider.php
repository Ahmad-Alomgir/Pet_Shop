<?php

class Slider extends Model
{
    protected $table = 'sliders';

    /*
    |--------------------------------------------------------------------------
    | Get All Sliders
    |--------------------------------------------------------------------------
    */
    public function getAll()
    {
        $this->db->query("SELECT * FROM {$this->table} ORDER BY id DESC");
        return $this->db->resultSet();
    }

    /*
    |--------------------------------------------------------------------------
    | Create Slider
    |--------------------------------------------------------------------------
    */
    public function create($data)
    {
        $this->db->query("
            INSERT INTO {$this->table} (title, image) 
            VALUES (:title, :image)
        ");

        $this->db->bind(':title', $data['title']);
        $this->db->bind(':image', $data['image']);

        return $this->db->execute();
    }

    /*
    |--------------------------------------------------------------------------
    | Update Slider
    |--------------------------------------------------------------------------
    */
    public function update($id, $data)
    {
        $this->db->query("
            UPDATE {$this->table}
            SET title = :title, image = :image
            WHERE id = :id
        ");

        $this->db->bind(':title', $data['title']);
        $this->db->bind(':image', $data['image']);
        $this->db->bind(':id', $id);

        return $this->db->execute();
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Slider
    |--------------------------------------------------------------------------
    */
    public function delete($id)
    {
        $this->db->query("DELETE FROM {$this->table} WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
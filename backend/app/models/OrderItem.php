<?php

class OrderItem extends Model
{
    protected $table = 'order_items';

    /*
    |--------------------------------------------------------------------------
    | Create Order Item
    |--------------------------------------------------------------------------
    */
    public function create($data)
    {
        $this->db->query("
            INSERT INTO {$this->table} 
            (order_id, product_id, quantity, price) 
            VALUES (:order_id, :product_id, :quantity, :price)
        ");

        $this->db->bind(':order_id', $data['order_id']);
        $this->db->bind(':product_id', $data['product_id']);
        $this->db->bind(':quantity', $data['quantity']);
        $this->db->bind(':price', $data['price']);

        return $this->db->execute();
    }

    /*
    |--------------------------------------------------------------------------
    | Get Items by Order
    |--------------------------------------------------------------------------
    */
    public function getByOrder($orderId)
    {
        $this->db->query("
            SELECT oi.*, p.name, p.image 
            FROM {$this->table} oi
            LEFT JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = :order_id
        ");

        $this->db->bind(':order_id', $orderId);
        return $this->db->resultSet();
    }
}
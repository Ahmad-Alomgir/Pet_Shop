<?php

class Product extends Model
{
    protected $table = 'products';

    public function __construct()
    {
        parent::__construct();
        $this->ensureInventoryColumns();
    }

    private function ensureInventoryColumns()
    {
        $this->db->query("SHOW COLUMNS FROM {$this->table} LIKE 'stock_quantity'");
        $stockColumn = $this->db->single();
        if (!$stockColumn) {
            $this->db->query("ALTER TABLE {$this->table} ADD COLUMN stock_quantity INT NOT NULL DEFAULT 0 AFTER price");
            $this->db->execute();
        }

        $this->db->query("SHOW COLUMNS FROM {$this->table} LIKE 'low_stock_threshold'");
        $thresholdColumn = $this->db->single();
        if (!$thresholdColumn) {
            $this->db->query("ALTER TABLE {$this->table} ADD COLUMN low_stock_threshold INT NOT NULL DEFAULT 5 AFTER stock_quantity");
            $this->db->execute();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Get Products with Category
    |--------------------------------------------------------------------------
    */
    public function getAllWithCategory()
    {
        $this->db->query("
            SELECT p.*, c.name as category_name 
            FROM {$this->table} p
            LEFT JOIN categories c ON p.category_id = c.id
            ORDER BY p.id DESC
        ");
        return $this->db->resultSet();
    }

    /*
    |--------------------------------------------------------------------------
    | Find Product with Category
    |--------------------------------------------------------------------------
    */
    public function findWithCategory($id)
    {
        $this->db->query("
            SELECT p.*, c.name as category_name 
            FROM {$this->table} p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.id = :id
        ");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /*
    |--------------------------------------------------------------------------
    | Create Product
    |--------------------------------------------------------------------------
    */
    public function create($data)
    {
        $this->db->query("
            INSERT INTO {$this->table} 
            (name, description, price, stock_quantity, low_stock_threshold, image, category_id) 
            VALUES (:name, :description, :price, :stock_quantity, :low_stock_threshold, :image, :category_id)
        ");

        $this->db->bind(':name', $data['name']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':price', $data['price']);
        $this->db->bind(':stock_quantity', (int) ($data['stock_quantity'] ?? 0));
        $this->db->bind(':low_stock_threshold', (int) ($data['low_stock_threshold'] ?? 5));
        $this->db->bind(':image', $data['image']);
        $this->db->bind(':category_id', $data['category_id']);

        return $this->db->execute();
    }

    /*
    |--------------------------------------------------------------------------
    | Update Product
    |--------------------------------------------------------------------------
    */
    public function update($id, $data)
    {
        $this->db->query("
            UPDATE {$this->table} 
            SET
                name = :name,
                description = :description,
                price = :price,
                stock_quantity = :stock_quantity,
                low_stock_threshold = :low_stock_threshold,
                image = :image,
                category_id = :category_id
            WHERE id = :id
        ");

        $this->db->bind(':name', $data['name']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':price', $data['price']);
        $this->db->bind(':stock_quantity', (int) ($data['stock_quantity'] ?? 0));
        $this->db->bind(':low_stock_threshold', (int) ($data['low_stock_threshold'] ?? 5));
        $this->db->bind(':image', $data['image']);
        $this->db->bind(':category_id', $data['category_id']);
        $this->db->bind(':id', $id);

        return $this->db->execute();
    }

    public function adjustStock($id, $delta)
    {
        $this->db->query("
            UPDATE {$this->table}
            SET stock_quantity = GREATEST(0, stock_quantity + :delta)
            WHERE id = :id
        ");
        $this->db->bind(':delta', (int) $delta);
        $this->db->bind(':id', (int) $id);
        return $this->db->execute();
    }

    public function lowStock($limit = 20)
    {
        $this->db->query("
            SELECT p.*, c.name as category_name
            FROM {$this->table} p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.stock_quantity <= p.low_stock_threshold
            ORDER BY p.stock_quantity ASC, p.id DESC
            LIMIT :limit
        ");
        $this->db->bind(':limit', (int) $limit);
        return $this->db->resultSet();
    }

    /*
    |--------------------------------------------------------------------------
    | Search Products (AJAX)
    |--------------------------------------------------------------------------
    */
    public function search($keyword)
    {
        $this->db->query("
            SELECT * FROM {$this->table}
            WHERE name LIKE :keyword
            ORDER BY id DESC
        ");

        $this->db->bind(':keyword', '%' . $keyword . '%');
        return $this->db->resultSet();
    }
}
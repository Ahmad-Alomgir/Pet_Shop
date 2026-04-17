<?php

class PaymentMethod extends Model
{
    protected $table = 'payment_methods';

    public function __construct()
    {
        parent::__construct();
        $this->ensureTable();
        $this->seedDefaults();
    }

    private function ensureTable()
    {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS {$this->table} (
                id INT AUTO_INCREMENT PRIMARY KEY,
                code VARCHAR(50) UNIQUE NOT NULL,
                name VARCHAR(120) NOT NULL,
                description VARCHAR(255) NULL,
                is_active TINYINT(1) NOT NULL DEFAULT 1,
                sort_order INT NOT NULL DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
        $this->db->execute();
    }

    private function seedDefaults()
    {
        $this->db->query("SELECT COUNT(*) AS total FROM {$this->table}");
        $total = $this->db->single();
        if (!empty($total) && (int) $total['total'] > 0) {
            return;
        }

        $defaults = [
            ['cash_on_delivery', 'Cash on Delivery', 'Pay in cash when order arrives', 1, 1],
            ['card_on_delivery', 'Card on Delivery', 'Pay by card machine at delivery', 1, 2],
            ['bank_transfer', 'Bank Transfer', 'Manual transfer before shipping', 1, 3]
        ];

        foreach ($defaults as $row) {
            $this->db->query("
                INSERT INTO {$this->table} (code, name, description, is_active, sort_order)
                VALUES (:code, :name, :description, :is_active, :sort_order)
            ");
            $this->db->bind(':code', $row[0]);
            $this->db->bind(':name', $row[1]);
            $this->db->bind(':description', $row[2]);
            $this->db->bind(':is_active', $row[3]);
            $this->db->bind(':sort_order', $row[4]);
            $this->db->execute();
        }
    }

    public function getActive()
    {
        $this->db->query("
            SELECT *
            FROM {$this->table}
            WHERE is_active = 1
            ORDER BY sort_order ASC, id ASC
        ");
        return $this->db->resultSet();
    }

    public function updateMethod($id, $data)
    {
        $this->db->query("
            UPDATE {$this->table}
            SET
                name = :name,
                description = :description,
                is_active = :is_active,
                sort_order = :sort_order
            WHERE id = :id
        ");
        $this->db->bind(':name', trim($data['name'] ?? ''));
        $this->db->bind(':description', trim($data['description'] ?? ''));
        $this->db->bind(':is_active', !empty($data['is_active']) ? 1 : 0);
        $this->db->bind(':sort_order', (int) ($data['sort_order'] ?? 0));
        $this->db->bind(':id', (int) $id);
        return $this->db->execute();
    }
}

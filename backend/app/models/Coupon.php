<?php

class Coupon extends Model
{
    protected $table = 'coupons';

    public function __construct()
    {
        parent::__construct();
        $this->ensureTable();
    }

    private function ensureTable()
    {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS {$this->table} (
                id INT AUTO_INCREMENT PRIMARY KEY,
                code VARCHAR(80) UNIQUE NOT NULL,
                type VARCHAR(20) NOT NULL DEFAULT 'percent',
                value DECIMAL(10,2) NOT NULL DEFAULT 0,
                min_order_amount DECIMAL(10,2) NOT NULL DEFAULT 0,
                usage_limit INT NOT NULL DEFAULT 0,
                used_count INT NOT NULL DEFAULT 0,
                starts_at DATETIME NULL,
                ends_at DATETIME NULL,
                is_active TINYINT(1) NOT NULL DEFAULT 1,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
        $this->db->execute();
    }

    public function create($data)
    {
        $this->db->query("
            INSERT INTO {$this->table}
            (code, type, value, min_order_amount, usage_limit, starts_at, ends_at, is_active)
            VALUES (:code, :type, :value, :min_order_amount, :usage_limit, :starts_at, :ends_at, :is_active)
        ");
        $this->db->bind(':code', strtoupper(trim($data['code'])));
        $this->db->bind(':type', $data['type'] ?? 'percent');
        $this->db->bind(':value', $data['value'] ?? 0);
        $this->db->bind(':min_order_amount', $data['min_order_amount'] ?? 0);
        $this->db->bind(':usage_limit', (int) ($data['usage_limit'] ?? 0));
        $this->db->bind(':starts_at', $data['starts_at'] ?: null);
        $this->db->bind(':ends_at', $data['ends_at'] ?: null);
        $this->db->bind(':is_active', !empty($data['is_active']) ? 1 : 0);
        return $this->db->execute();
    }

    public function updateCoupon($id, $data)
    {
        $this->db->query("
            UPDATE {$this->table}
            SET
                code = :code,
                type = :type,
                value = :value,
                min_order_amount = :min_order_amount,
                usage_limit = :usage_limit,
                starts_at = :starts_at,
                ends_at = :ends_at,
                is_active = :is_active
            WHERE id = :id
        ");
        $this->db->bind(':code', strtoupper(trim($data['code'])));
        $this->db->bind(':type', $data['type'] ?? 'percent');
        $this->db->bind(':value', $data['value'] ?? 0);
        $this->db->bind(':min_order_amount', $data['min_order_amount'] ?? 0);
        $this->db->bind(':usage_limit', (int) ($data['usage_limit'] ?? 0));
        $this->db->bind(':starts_at', $data['starts_at'] ?: null);
        $this->db->bind(':ends_at', $data['ends_at'] ?: null);
        $this->db->bind(':is_active', !empty($data['is_active']) ? 1 : 0);
        $this->db->bind(':id', (int) $id);
        return $this->db->execute();
    }
}

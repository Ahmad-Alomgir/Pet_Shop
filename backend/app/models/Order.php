<?php

class Order extends Model
{
    protected $table = 'orders';

    public function __construct()
    {
        parent::__construct();
        $this->ensureColumns();
    }

    private function ensureColumns()
    {
        $this->db->query("SHOW COLUMNS FROM {$this->table} LIKE 'user_id'");
        $userIdColumn = $this->db->single();
        if (!$userIdColumn) {
            $this->db->query("ALTER TABLE {$this->table} ADD COLUMN user_id INT NULL AFTER id");
            $this->db->execute();
        }

        $this->db->query("SHOW COLUMNS FROM {$this->table} LIKE 'customer_email'");
        $emailColumn = $this->db->single();
        if (!$emailColumn) {
            $this->db->query("ALTER TABLE {$this->table} ADD COLUMN customer_email VARCHAR(150) NULL AFTER customer_name");
            $this->db->execute();
        }

        $this->db->query("SHOW COLUMNS FROM {$this->table} LIKE 'payment_method'");
        $paymentMethodColumn = $this->db->single();
        if (!$paymentMethodColumn) {
            $this->db->query("ALTER TABLE {$this->table} ADD COLUMN payment_method VARCHAR(50) NOT NULL DEFAULT 'cash_on_delivery' AFTER total_amount");
            $this->db->execute();
        }

        $this->db->query("SHOW COLUMNS FROM {$this->table} LIKE 'payment_status'");
        $paymentStatusColumn = $this->db->single();
        if (!$paymentStatusColumn) {
            $this->db->query("ALTER TABLE {$this->table} ADD COLUMN payment_status VARCHAR(50) NOT NULL DEFAULT 'pending' AFTER payment_method");
            $this->db->execute();
        }

        $this->db->query("SHOW COLUMNS FROM {$this->table} LIKE 'admin_note'");
        $adminNoteColumn = $this->db->single();
        if (!$adminNoteColumn) {
            $this->db->query("ALTER TABLE {$this->table} ADD COLUMN admin_note TEXT NULL AFTER status");
            $this->db->execute();
        }

        $this->db->query("SHOW COLUMNS FROM {$this->table} LIKE 'shipped_at'");
        $shippedAtColumn = $this->db->single();
        if (!$shippedAtColumn) {
            $this->db->query("ALTER TABLE {$this->table} ADD COLUMN shipped_at DATETIME NULL AFTER admin_note");
            $this->db->execute();
        }

        $this->db->query("SHOW COLUMNS FROM {$this->table} LIKE 'completed_at'");
        $completedAtColumn = $this->db->single();
        if (!$completedAtColumn) {
            $this->db->query("ALTER TABLE {$this->table} ADD COLUMN completed_at DATETIME NULL AFTER shipped_at");
            $this->db->execute();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Create Order
    |--------------------------------------------------------------------------
    */
    public function create($data)
    {
        $this->db->query("
            INSERT INTO {$this->table} 
            (user_id, customer_name, customer_email, customer_phone, customer_address, total_amount, payment_method, payment_status, status) 
            VALUES (:user_id, :name, :email, :phone, :address, :total, :payment_method, :payment_status, :status)
        ");

        $this->db->bind(':user_id', $data['user_id'] ?? null);
        $this->db->bind(':name', $data['customer_name']);
        $this->db->bind(':email', $data['customer_email'] ?? null);
        $this->db->bind(':phone', $data['customer_phone']);
        $this->db->bind(':address', $data['customer_address']);
        $this->db->bind(':total', $data['total_amount']);
        $this->db->bind(':payment_method', $data['payment_method'] ?? 'cash_on_delivery');
        $this->db->bind(':payment_status', $data['payment_status'] ?? 'pending');
        $this->db->bind(':status', 'pending');

        $this->db->execute();

        return $this->db->lastInsertId();
    }

    /*
    |--------------------------------------------------------------------------
    | Get All Orders
    |--------------------------------------------------------------------------
    */
    public function getAll()
    {
        $this->db->query("SELECT * FROM {$this->table} ORDER BY id DESC");
        return $this->db->resultSet();
    }

    /*
    |--------------------------------------------------------------------------
    | Update Order Status
    |--------------------------------------------------------------------------
    */
    public function updateStatus($id, $status)
    {
        $status = strtolower($status);
        $allowed = ['pending', 'processing', 'shipped', 'completed', 'cancelled'];
        if (!in_array($status, $allowed, true)) {
            $status = 'pending';
        }

        $timelineSql = '';
        if ($status === 'shipped') {
            $timelineSql = ", shipped_at = NOW()";
        } elseif ($status === 'completed') {
            $timelineSql = ", completed_at = NOW()";
        }

        $this->db->query("
            UPDATE {$this->table} 
            SET status = :status {$timelineSql}
            WHERE id = :id
        ");

        $this->db->bind(':status', $status);
        $this->db->bind(':id', $id);

        return $this->db->execute();
    }

    public function updatePayment($id, $paymentMethod, $paymentStatus)
    {
        $this->db->query("
            UPDATE {$this->table}
            SET payment_method = :payment_method, payment_status = :payment_status
            WHERE id = :id
        ");
        $this->db->bind(':payment_method', $paymentMethod);
        $this->db->bind(':payment_status', $paymentStatus);
        $this->db->bind(':id', (int) $id);
        return $this->db->execute();
    }

    public function updateAdminNote($id, $adminNote)
    {
        $this->db->query("
            UPDATE {$this->table}
            SET admin_note = :admin_note
            WHERE id = :id
        ");
        $this->db->bind(':admin_note', $adminNote);
        $this->db->bind(':id', (int) $id);
        return $this->db->execute();
    }

    public function getById($id)
    {
        $this->db->query("SELECT * FROM {$this->table} WHERE id = :id");
        $this->db->bind(':id', (int) $id);
        return $this->db->single();
    }

    public function getByUser($userId)
    {
        $this->db->query("SELECT * FROM {$this->table} WHERE user_id = :user_id ORDER BY id DESC");
        $this->db->bind(':user_id', $userId);
        return $this->db->resultSet();
    }

    public function getStatusCounts()
    {
        $this->db->query("
            SELECT status, COUNT(*) AS total
            FROM {$this->table}
            GROUP BY status
        ");
        return $this->db->resultSet();
    }

    public function getMonthlySales()
    {
        $this->db->query("
            SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, SUM(total_amount) AS total_amount, COUNT(*) AS orders_count
            FROM {$this->table}
            GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ORDER BY month ASC
        ");
        return $this->db->resultSet();
    }

    public function getTopCustomers($limit = 10)
    {
        $this->db->query("
            SELECT
                user_id,
                customer_name,
                customer_email,
                customer_phone,
                customer_address,
                COUNT(*) AS orders_count,
                SUM(total_amount) AS total_spent,
                MAX(created_at) AS latest_order_at
            FROM {$this->table}
            GROUP BY user_id, customer_name, customer_email, customer_phone, customer_address
            ORDER BY total_spent DESC
            LIMIT :limit
        ");
        $this->db->bind(':limit', (int) $limit);
        return $this->db->resultSet();
    }

    public function getRangeSummary($fromDate = null, $toDate = null)
    {
        $where = "WHERE 1=1";
        if ($fromDate) {
            $where .= " AND DATE(created_at) >= :from_date";
        }
        if ($toDate) {
            $where .= " AND DATE(created_at) <= :to_date";
        }

        $this->db->query("
            SELECT
                COUNT(*) AS orders_count,
                COALESCE(SUM(total_amount), 0) AS revenue,
                COALESCE(AVG(total_amount), 0) AS avg_order_value
            FROM {$this->table}
            {$where}
        ");

        if ($fromDate) {
            $this->db->bind(':from_date', $fromDate);
        }
        if ($toDate) {
            $this->db->bind(':to_date', $toDate);
        }

        return $this->db->single();
    }
}
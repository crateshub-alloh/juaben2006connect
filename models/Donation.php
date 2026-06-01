<?php
class Donation
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO donations (user_id, campaign_id, amount, currency, reference,
                                    gateway, status, donor_name, donor_email, note)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['user_id']     ?? null,
            $data['campaign_id'] ?? null,
            $data['amount'],
            $data['currency']    ?? 'GHS',
            $data['reference'],
            $data['gateway']     ?? 'manual',
            'pending',
            $data['donor_name']  ?? null,
            $data['donor_email'] ?? null,
            $data['note']        ?? null,
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function findByReference(string $ref): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM donations WHERE reference = ?');
        $stmt->execute([$ref]);
        return $stmt->fetch() ?: null;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM donations WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function markCompleted(int $id, string $gatewayRef, int $approvedBy): void
    {
        $this->db->prepare(
            'UPDATE donations SET status = "completed", gateway_ref = ?,
             approved_by = ?, approved_at = NOW(), updated_at = NOW()
             WHERE id = ?'
        )->execute([$gatewayRef, $approvedBy, $id]);
    }

    public function markCancelled(int $id): void
    {
        $this->db->prepare(
            'UPDATE donations SET status = "cancelled", updated_at = NOW() WHERE id = ?'
        )->execute([$id]);
    }

    public function all(int $page = 1, int $perPage = PAGE_SIZE, array $filters = []): array
    {
        $where  = ['1=1'];
        $params = [];

        if (!empty($filters['status'])) {
            $where[]  = 'd.status = ?';
            $params[] = $filters['status'];
        }
        if (!empty($filters['user_id'])) {
            $where[]  = 'd.user_id = ?';
            $params[] = $filters['user_id'];
        }

        $whereStr = implode(' AND ', $where);
        $offset   = ($page - 1) * $perPage;

        $stmt = $this->db->prepare(
            "SELECT d.*, u.first_name, u.last_name, dc.title AS campaign_title
             FROM donations d
             LEFT JOIN users u ON u.id = d.user_id
             LEFT JOIN donation_campaigns dc ON dc.id = d.campaign_id
             WHERE {$whereStr}
             ORDER BY d.created_at DESC
             LIMIT ? OFFSET ?"
        );
        $stmt->execute(array_merge($params, [$perPage, $offset]));
        $rows = $stmt->fetchAll();

        $countStmt = $this->db->prepare("SELECT COUNT(*) FROM donations d WHERE {$whereStr}");
        $countStmt->execute($params);

        return [
            'data'     => $rows,
            'total'    => (int)$countStmt->fetchColumn(),
            'page'     => $page,
            'per_page' => $perPage,
        ];
    }

    public function byUser(int $userId): array
    {
        $stmt = $this->db->prepare(
            'SELECT d.*, dc.title AS campaign_title
             FROM donations d LEFT JOIN donation_campaigns dc ON dc.id = d.campaign_id
             WHERE d.user_id = ? ORDER BY d.created_at DESC'
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function totalRaised(): float
    {
        return (float)$this->db->query(
            "SELECT COALESCE(SUM(amount),0) FROM donations WHERE status = 'completed'"
        )->fetchColumn();
    }

    public function pendingCount(): int
    {
        return (int)$this->db->query(
            "SELECT COUNT(*) FROM donations WHERE status = 'pending'"
        )->fetchColumn();
    }

    public function monthlyRevenue(): array
    {
        $stmt = $this->db->query(
            "SELECT DATE_FORMAT(created_at,'%Y-%m') AS month, SUM(amount) AS total
             FROM donations WHERE status = 'completed'
             AND created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
             GROUP BY month ORDER BY month"
        );
        return $stmt->fetchAll();
    }

    // Campaigns
    public function activeCampaigns(): array
    {
        $stmt = $this->db->query(
            "SELECT dc.*,
                    COALESCE(SUM(CASE WHEN d.status='completed' THEN d.amount ELSE 0 END), 0) AS raised
             FROM donation_campaigns dc
             LEFT JOIN donations d ON d.campaign_id = dc.id
             WHERE dc.is_active = 1
             GROUP BY dc.id ORDER BY dc.created_at DESC"
        );
        return $stmt->fetchAll();
    }
}

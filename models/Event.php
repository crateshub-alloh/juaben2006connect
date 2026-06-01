<?php
class Event
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function all(int $page = 1, int $perPage = PAGE_SIZE, array $filters = []): array
    {
        $where  = ['1=1'];
        $params = [];

        if (!empty($filters['status'])) {
            $where[]  = 'e.status = ?';
            $params[] = $filters['status'];
        }
        if (!empty($filters['search'])) {
            $where[]  = 'e.title LIKE ?';
            $params[] = '%' . $filters['search'] . '%';
        }
        if (isset($filters['is_public'])) {
            $where[]  = 'e.is_public = ?';
            $params[] = (int)$filters['is_public'];
        }

        $whereStr = implode(' AND ', $where);
        $offset   = ($page - 1) * $perPage;

        $stmt = $this->db->prepare(
            "SELECT e.*, u.first_name, u.last_name,
                    (SELECT COUNT(*) FROM event_registrations er WHERE er.event_id = e.id) AS registrations
             FROM events e
             JOIN users u ON u.id = e.created_by
             WHERE {$whereStr}
             ORDER BY e.starts_at DESC
             LIMIT ? OFFSET ?"
        );
        $stmt->execute(array_merge($params, [$perPage, $offset]));
        $rows = $stmt->fetchAll();

        $countStmt = $this->db->prepare("SELECT COUNT(*) FROM events e WHERE {$whereStr}");
        $countStmt->execute($params);

        return [
            'data'     => $rows,
            'total'    => (int)$countStmt->fetchColumn(),
            'page'     => $page,
            'per_page' => $perPage,
        ];
    }

    public function upcoming(int $limit = 3): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM events
             WHERE status IN ('upcoming','ongoing') AND is_public = 1 AND starts_at >= NOW()
             ORDER BY starts_at ASC LIMIT ?"
        );
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    public function findBySlug(string $slug): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT e.*, u.first_name, u.last_name,
                    (SELECT COUNT(*) FROM event_registrations er WHERE er.event_id = e.id) AS registrations
             FROM events e JOIN users u ON u.id = e.created_by
             WHERE e.slug = ?'
        );
        $stmt->execute([$slug]);
        return $stmt->fetch() ?: null;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM events WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO events (created_by, title, slug, description, image, location, venue,
                                  starts_at, ends_at, capacity, ticket_price, status, is_public)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['created_by'],
            $data['title'],
            make_slug($data['title']),
            $data['description']  ?? null,
            $data['image']        ?? null,
            $data['location']     ?? null,
            $data['venue']        ?? null,
            $data['starts_at'],
            $data['ends_at'],
            $data['capacity']     ?? null,
            $data['ticket_price'] ?? 0,
            $data['status']       ?? 'upcoming',
            $data['is_public']    ?? 1,
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $this->db->prepare(
            'UPDATE events SET title=?, description=?, image=?, location=?, venue=?,
             starts_at=?, ends_at=?, capacity=?, ticket_price=?, status=?, is_public=?, updated_at=NOW()
             WHERE id=?'
        )->execute([
            $data['title'],
            $data['description']  ?? null,
            $data['image']        ?? null,
            $data['location']     ?? null,
            $data['venue']        ?? null,
            $data['starts_at'],
            $data['ends_at'],
            $data['capacity']     ?? null,
            $data['ticket_price'] ?? 0,
            $data['status'],
            $data['is_public']    ?? 1,
            $id,
        ]);
    }

    public function delete(int $id): void
    {
        $this->db->prepare('DELETE FROM events WHERE id = ?')->execute([$id]);
    }

    public function register(int $eventId, int $userId): bool
    {
        try {
            $this->db->prepare(
                'INSERT INTO event_registrations (event_id, user_id) VALUES (?, ?)'
            )->execute([$eventId, $userId]);
            return true;
        } catch (PDOException) {
            return false; // duplicate key = already registered
        }
    }

    public function isRegistered(int $eventId, int $userId): bool
    {
        $stmt = $this->db->prepare(
            'SELECT 1 FROM event_registrations WHERE event_id = ? AND user_id = ?'
        );
        $stmt->execute([$eventId, $userId]);
        return (bool)$stmt->fetchColumn();
    }

    public function attendees(int $eventId): array
    {
        $stmt = $this->db->prepare(
            'SELECT u.first_name, u.last_name, u.email, er.status, er.registered_at
             FROM event_registrations er JOIN users u ON u.id = er.user_id
             WHERE er.event_id = ? ORDER BY er.registered_at'
        );
        $stmt->execute([$eventId]);
        return $stmt->fetchAll();
    }

    public function totalCount(): int
    {
        return (int)$this->db->query('SELECT COUNT(*) FROM events')->fetchColumn();
    }

    public function monthlyAttendance(): array
    {
        $stmt = $this->db->query(
            "SELECT DATE_FORMAT(e.starts_at,'%Y-%m') AS month,
                    COUNT(DISTINCT er.user_id) AS attendees
             FROM events e LEFT JOIN event_registrations er ON er.event_id = e.id
             WHERE e.starts_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
             GROUP BY month ORDER BY month"
        );
        return $stmt->fetchAll();
    }
}

<?php
class Project
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
            $where[]  = 'p.status = ?';
            $params[] = $filters['status'];
        }

        $whereStr = implode(' AND ', $where);
        $offset   = ($page - 1) * $perPage;

        $stmt = $this->db->prepare(
            "SELECT p.*, u.first_name, u.last_name,
                    (SELECT COUNT(*) FROM project_updates pu WHERE pu.project_id = p.id) AS update_count
             FROM projects p JOIN users u ON u.id = p.created_by
             WHERE {$whereStr}
             ORDER BY p.created_at DESC LIMIT ? OFFSET ?"
        );
        $stmt->execute(array_merge($params, [$perPage, $offset]));
        $rows = $stmt->fetchAll();

        $countStmt = $this->db->prepare("SELECT COUNT(*) FROM projects p WHERE {$whereStr}");
        $countStmt->execute($params);

        return [
            'data'     => $rows,
            'total'    => (int)$countStmt->fetchColumn(),
            'page'     => $page,
            'per_page' => $perPage,
        ];
    }

    public function featured(int $limit = 3): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM projects WHERE is_featured = 1 AND status != "completed"
             ORDER BY created_at DESC LIMIT ?'
        );
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    public function findBySlug(string $slug): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM projects WHERE slug = ?');
        $stmt->execute([$slug]);
        return $stmt->fetch() ?: null;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM projects WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO projects (created_by, title, slug, description, image,
                                   goal_amount, start_date, end_date, status, is_featured)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['created_by'],
            $data['title'],
            make_slug($data['title']),
            $data['description']  ?? null,
            $data['image']        ?? null,
            $data['goal_amount']  ?? 0,
            $data['start_date']   ?? null,
            $data['end_date']     ?? null,
            $data['status']       ?? 'planned',
            $data['is_featured']  ?? 0,
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $this->db->prepare(
            'UPDATE projects SET title=?, description=?, image=?, goal_amount=?,
             start_date=?, end_date=?, status=?, is_featured=?, updated_at=NOW()
             WHERE id=?'
        )->execute([
            $data['title'],
            $data['description'] ?? null,
            $data['image']       ?? null,
            $data['goal_amount'] ?? 0,
            $data['start_date']  ?? null,
            $data['end_date']    ?? null,
            $data['status'],
            $data['is_featured'] ?? 0,
            $id,
        ]);
    }

    public function delete(int $id): void
    {
        $this->db->prepare('DELETE FROM projects WHERE id = ?')->execute([$id]);
    }

    public function addUpdate(int $projectId, int $authorId, string $title, string $body): void
    {
        $this->db->prepare(
            'INSERT INTO project_updates (project_id, author_id, title, body) VALUES (?, ?, ?, ?)'
        )->execute([$projectId, $authorId, $title, $body]);
    }

    public function getUpdates(int $projectId): array
    {
        $stmt = $this->db->prepare(
            'SELECT pu.*, u.first_name, u.last_name
             FROM project_updates pu JOIN users u ON u.id = pu.author_id
             WHERE pu.project_id = ? ORDER BY pu.created_at DESC'
        );
        $stmt->execute([$projectId]);
        return $stmt->fetchAll();
    }

    public function totalCount(): int
    {
        return (int)$this->db->query('SELECT COUNT(*) FROM projects')->fetchColumn();
    }

    public function completedCount(): int
    {
        return (int)$this->db->query(
            "SELECT COUNT(*) FROM projects WHERE status = 'completed'"
        )->fetchColumn();
    }
}

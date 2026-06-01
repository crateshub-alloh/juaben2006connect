<?php
class Testimonial
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function featured(int $limit = 6): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM testimonials WHERE is_featured = 1
             ORDER BY sort_order ASC, created_at DESC LIMIT ?'
        );
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    public function all(): array
    {
        $stmt = $this->db->query(
            'SELECT * FROM testimonials ORDER BY sort_order ASC, created_at DESC'
        );
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM testimonials WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO testimonials (name, nickname, graduation_year, class_name, message, is_featured, sort_order)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['name'],
            $data['nickname'] ?: null,
            $data['graduation_year'] ?: null,
            $data['class_name'] ?: null,
            $data['message'],
            (int)($data['is_featured'] ?? 0),
            (int)($data['sort_order'] ?? 0),
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $stmt = $this->db->prepare(
            'UPDATE testimonials
             SET name = ?, nickname = ?, graduation_year = ?, class_name = ?, message = ?, is_featured = ?, sort_order = ?
             WHERE id = ?'
        );
        $stmt->execute([
            $data['name'],
            $data['nickname'] ?: null,
            $data['graduation_year'] ?: null,
            $data['class_name'] ?: null,
            $data['message'],
            (int)($data['is_featured'] ?? 0),
            (int)($data['sort_order'] ?? 0),
            $id,
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM testimonials WHERE id = ?');
        $stmt->execute([$id]);
    }
}

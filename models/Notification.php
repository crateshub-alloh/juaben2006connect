<?php
class Notification
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function forUser(int $userId, int $limit = 20): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM notifications WHERE user_id = ?
             ORDER BY created_at DESC LIMIT ?'
        );
        $stmt->execute([$userId, $limit]);
        return $stmt->fetchAll();
    }

    public function unreadCount(int $userId): int
    {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM notifications WHERE user_id = ? AND read_at IS NULL'
        );
        $stmt->execute([$userId]);
        return (int)$stmt->fetchColumn();
    }

    public function create(int $userId, string $type, string $title, ?string $body = null, ?string $url = null): void
    {
        $this->db->prepare(
            'INSERT INTO notifications (user_id, type, title, body, url) VALUES (?, ?, ?, ?, ?)'
        )->execute([$userId, $type, $title, $body, $url]);
    }

    public function markAllRead(int $userId): void
    {
        $this->db->prepare(
            'UPDATE notifications SET read_at = NOW() WHERE user_id = ? AND read_at IS NULL'
        )->execute([$userId]);
    }

    public function markRead(int $id, int $userId): void
    {
        $this->db->prepare(
            'UPDATE notifications SET read_at = NOW() WHERE id = ? AND user_id = ?'
        )->execute([$id, $userId]);
    }
}

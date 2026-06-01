<?php
class Message
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function inbox(int $userId): array
    {
        $stmt = $this->db->prepare(
            "SELECT m.*, u.first_name, u.last_name, u.email AS sender_email
             FROM messages m JOIN users u ON u.id = m.sender_id
             WHERE (m.recipient_id = ? OR m.is_broadcast = 1)
               AND m.is_deleted = 0
             ORDER BY m.created_at DESC"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function sent(int $userId): array
    {
        $stmt = $this->db->prepare(
            "SELECT m.*, u.first_name AS r_first, u.last_name AS r_last
             FROM messages m LEFT JOIN users u ON u.id = m.recipient_id
             WHERE m.sender_id = ? AND m.is_deleted = 0
             ORDER BY m.created_at DESC"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT m.*, u.first_name, u.last_name
             FROM messages m JOIN users u ON u.id = m.sender_id
             WHERE m.id = ?'
        );
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function send(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO messages (sender_id, recipient_id, is_broadcast, subject, body)
             VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['sender_id'],
            $data['recipient_id'] ?? null,
            $data['is_broadcast'] ?? 0,
            $data['subject'],
            $data['body'],
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function markRead(int $messageId): void
    {
        $this->db->prepare('UPDATE messages SET read_at = NOW() WHERE id = ? AND read_at IS NULL')
                 ->execute([$messageId]);
    }

    public function delete(int $messageId, int $userId): void
    {
        $this->db->prepare(
            'UPDATE messages SET is_deleted = 1 WHERE id = ? AND (recipient_id = ? OR sender_id = ?)'
        )->execute([$messageId, $userId, $userId]);
    }

    public function unreadCount(int $userId): int
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM messages
             WHERE (recipient_id = ? OR is_broadcast = 1)
               AND read_at IS NULL AND is_deleted = 0"
        );
        $stmt->execute([$userId]);
        return (int)$stmt->fetchColumn();
    }
}

<?php
class Gallery
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function albums(bool $publicOnly = true): array
    {
        $sql = 'SELECT ga.*, u.first_name, u.last_name,
                       (SELECT COUNT(*) FROM gallery_images gi WHERE gi.album_id = ga.id) AS image_count
                FROM gallery_albums ga JOIN users u ON u.id = ga.created_by';
        if ($publicOnly) $sql .= ' WHERE ga.is_public = 1';
        $sql .= ' ORDER BY ga.created_at DESC';
        return $this->db->query($sql)->fetchAll();
    }

    public function albumImages(int $albumId): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM gallery_images WHERE album_id = ? ORDER BY sort_order, created_at'
        );
        $stmt->execute([$albumId]);
        return $stmt->fetchAll();
    }

    public function findAlbum(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM gallery_albums WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function createAlbum(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO gallery_albums (created_by, title, slug, description, is_public)
             VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['created_by'],
            $data['title'],
            make_slug($data['title']),
            $data['description'] ?? null,
            $data['is_public']   ?? 1,
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function addImage(int $albumId, int $uploaderId, string $filename, ?string $caption): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO gallery_images (album_id, uploaded_by, filename, caption) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$albumId, $uploaderId, $filename, $caption]);
        return (int)$this->db->lastInsertId();
    }

    public function deleteImage(int $imageId): ?string
    {
        $stmt = $this->db->prepare('SELECT filename FROM gallery_images WHERE id = ?');
        $stmt->execute([$imageId]);
        $row = $stmt->fetch();
        if (!$row) return null;
        $this->db->prepare('DELETE FROM gallery_images WHERE id = ?')->execute([$imageId]);
        return $row['filename'];
    }

    public function setCover(int $albumId, string $filename): void
    {
        $this->db->prepare('UPDATE gallery_albums SET cover_image = ? WHERE id = ?')
                 ->execute([$filename, $albumId]);
    }

    public function recentImages(int $limit = 12): array
    {
        $stmt = $this->db->prepare(
            'SELECT gi.*, ga.title AS album_title
             FROM gallery_images gi JOIN gallery_albums ga ON ga.id = gi.album_id
             WHERE ga.is_public = 1
             ORDER BY gi.created_at DESC LIMIT ?'
        );
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
}

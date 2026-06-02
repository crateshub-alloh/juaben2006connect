<?php
class User
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT u.*, r.name AS role_name, r.label AS role_label,
                    p.avatar, p.phone,
                    p.bio, p.nickname, p.occupation, p.employer, p.city, p.state, p.country, p.house,
                    p.linkedin_url, p.twitter_url, p.facebook_url,
                    p.dues_paid_until
             FROM users u
             JOIN roles   r ON r.id = u.role_id
             LEFT JOIN profiles p ON p.user_id = u.id
             WHERE u.id = ?'
        );
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT u.*, r.name AS role_name, r.label AS role_label
             FROM users u JOIN roles r ON r.id = u.role_id
             WHERE u.email = ?'
        );
        $stmt->execute([strtolower(trim($email))]);
        return $stmt->fetch() ?: null;
    }

    public function findByVerifyToken(string $token): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT u.*, r.name AS role_name, r.label AS role_label,
                    p.avatar, p.phone,
                    p.bio, p.occupation, p.employer, p.city, p.state, p.country,
                    p.linkedin_url, p.twitter_url, p.facebook_url, p.house
             FROM users u
             JOIN roles r ON r.id = u.role_id
             LEFT JOIN profiles p ON p.user_id = u.id
             WHERE u.email_verify_token = ? AND u.email_verified_at IS NULL'
        );
        $stmt->execute([$token]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO users (role_id, email, password_hash, first_name, last_name,
                                email_verify_token, is_active)
             VALUES (?, ?, ?, ?, ?, ?, 0)'
        );
        $stmt->execute([
            $data['role_id']       ?? ROLE_MEMBER,
            strtolower(trim($data['email'])),
            password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]),
            trim($data['first_name']),
            trim($data['last_name']),
            $data['verify_token'],
        ]);
        $userId = (int)$this->db->lastInsertId();

        // Create empty profile row
        $this->db->prepare('INSERT INTO profiles (user_id) VALUES (?)')->execute([$userId]);

        return $userId;
    }

    public function verifyEmail(string $token): ?array
    {
        $user = $this->findByVerifyToken($token);
        if (!$user) {
            return null;
        }

        $stmt = $this->db->prepare(
            'UPDATE users SET is_active = 1, email_verified_at = NOW(), email_verify_token = NULL
             WHERE id = ? AND email_verified_at IS NULL'
        );
        $stmt->execute([$user['id']]);

        return $stmt->rowCount() > 0 ? $user : null;
    }

    public function setPasswordResetToken(string $email, string $token): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE users SET password_reset_token = ?, password_reset_expires = DATE_ADD(NOW(), INTERVAL 1 HOUR)
             WHERE email = ?'
        );
        $stmt->execute([$token, strtolower($email)]);
        return $stmt->rowCount() > 0;
    }

    public function resetPassword(string $token, string $newPassword): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE users
             SET password_hash = ?, password_reset_token = NULL, password_reset_expires = NULL
             WHERE password_reset_token = ? AND password_reset_expires > NOW()'
        );
        $stmt->execute([
            password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]),
            $token,
        ]);
        return $stmt->rowCount() > 0;
    }

    public function updateLastLogin(int $id): void
    {
        $this->db->prepare('UPDATE users SET last_login_at = NOW() WHERE id = ?')->execute([$id]);
    }

    public function updateProfile(int $userId, array $data): void
    {
        // Update users table fields
        if (!empty($data['first_name']) || !empty($data['last_name'])) {
            $this->db->prepare(
                'UPDATE users SET first_name = ?, last_name = ?, is_profile_complete = 1 WHERE id = ?'
            )->execute([trim($data['first_name']), trim($data['last_name']), $userId]);
        }

        // Upsert profile
        $fields = ['phone','bio','nickname','occupation',
                   'employer','city','state','country','house','linkedin_url','twitter_url',
                   'facebook_url'];
        $setClauses = implode(', ', array_map(fn($f) => "$f = ?", $fields));
        $values     = array_map(fn($f) => $data[$f] ?? null, $fields);
        $values[]   = $userId;

        $this->db->prepare(
            "UPDATE profiles SET {$setClauses} WHERE user_id = ?"
        )->execute($values);
    }

    public function updateAvatar(int $userId, string $filename): void
    {
        $this->db->prepare('UPDATE profiles SET avatar = ? WHERE user_id = ?')
                 ->execute([$filename, $userId]);
    }

    // Listing with pagination
    public function all(int $page = 1, int $perPage = PAGE_SIZE, array $filters = []): array
    {
        $where  = ['1=1'];
        $params = [];

        if (!empty($filters['search'])) {
            $where[]  = '(u.first_name LIKE ? OR u.last_name LIKE ? OR u.email LIKE ?)';
            $term     = '%' . $filters['search'] . '%';
            $params   = array_merge($params, [$term, $term, $term]);
        }
        if (!empty($filters['role_id'])) {
            $where[]  = 'u.role_id = ?';
            $params[] = $filters['role_id'];
        }
        if (isset($filters['is_active'])) {
            $where[]  = 'u.is_active = ?';
            $params[] = (int)$filters['is_active'];
        }

        $whereStr = implode(' AND ', $where);
        $offset   = ($page - 1) * $perPage;

        $stmt = $this->db->prepare(
            "SELECT u.id, u.email, u.first_name, u.last_name, u.is_active,
                    u.is_profile_complete, u.last_login_at, u.created_at,
                    r.label AS role_label, p.avatar
             FROM users u
             JOIN roles r ON r.id = u.role_id
             LEFT JOIN profiles p ON p.user_id = u.id
             WHERE {$whereStr}
             ORDER BY u.created_at DESC
             LIMIT ? OFFSET ?"
        );
        $stmt->execute(array_merge($params, [$perPage, $offset]));
        $rows = $stmt->fetchAll();

        $countStmt = $this->db->prepare(
            "SELECT COUNT(*) FROM users u WHERE {$whereStr}"
        );
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();

        return ['data' => $rows, 'total' => $total, 'page' => $page, 'per_page' => $perPage];
    }

    public function updateRole(int $userId, int $roleId): void
    {
        $this->db->prepare('UPDATE users SET role_id = ? WHERE id = ?')
                 ->execute([$roleId, $userId]);
    }

    public function toggleActive(int $userId, bool $active): void
    {
        $this->db->prepare('UPDATE users SET is_active = ? WHERE id = ?')
                 ->execute([(int)$active, $userId]);
    }

    public function delete(int $userId): void
    {
        $this->db->prepare('DELETE FROM users WHERE id = ?')->execute([$userId]);
    }

    public function activateUser(int $userId): void
    {
        $this->db->prepare(
            'UPDATE users SET is_active = 1, email_verified_at = NOW(), email_verify_token = NULL WHERE id = ?'
        )->execute([$userId]);
    }

    public function totalCount(): int
    {
        return (int)$this->db->query('SELECT COUNT(*) FROM users')->fetchColumn();
    }

    public function activeCount(): int
    {
        return (int)$this->db->query('SELECT COUNT(*) FROM users WHERE is_active = 1')->fetchColumn();
    }

    public function monthlyGrowth(): array
    {
        $stmt = $this->db->query(
            'SELECT DATE_FORMAT(created_at, "%Y-%m") AS month, COUNT(*) AS count
             FROM users
             WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
             GROUP BY month ORDER BY month'
        );
        return $stmt->fetchAll();
    }
}

<?php
/**
 * API – Notifications (JSON)
 */
require_once dirname(__DIR__) . '/config/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');

if (!is_logged_in()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthenticated']);
    exit;
}

$model = new Notification();
$user  = auth();
$userId = (int)$user['id'];

$action = sanitize_input($_GET['action'] ?? 'list');

switch ($action) {
    case 'list':
        echo json_encode([
            'notifications' => $model->forUser($userId, 15),
            'unread'        => $model->unreadCount($userId),
        ]);
        break;

    case 'mark_all_read':
        $model->markAllRead($userId);
        echo json_encode(['ok' => true]);
        break;

    case 'mark_read':
        $id = (int)($_GET['id'] ?? 0);
        if ($id) $model->markRead($id, $userId);
        echo json_encode(['ok' => true]);
        break;

    default:
        http_response_code(400);
        echo json_encode(['error' => 'Unknown action']);
}

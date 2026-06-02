<?php
/**
 * NJOSA Alumni Portal – Front Controller
 */
require_once __DIR__ . '/config/bootstrap.php';

$uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Strip base path (for subdirectory installs like /alumni/)
$base = parse_url(APP_URL, PHP_URL_PATH) ?: '';
if ($base && strpos($uri, $base) === 0) {
    $uri = substr($uri, strlen($base));
}
$uri = '/' . ltrim($uri, '/');
if ($uri !== '/' && str_ends_with($uri, '/')) {
    $uri = rtrim($uri, '/');
}

// ── Route table ──────────────────────────────────────────────
$routes = [
    // Public
    'GET'  => [
        '/'                    => fn() => include VIEWS_PATH . '/public/home.php',
        '/about'               => fn() => include VIEWS_PATH . '/public/about.php',
        '/oldstudents'         => fn() => include VIEWS_PATH . '/public/oldstudents.php',
        '/events'              => fn() => include VIEWS_PATH . '/public/events.php',
        '/projects'            => fn() => include VIEWS_PATH . '/public/projects.php',
        '/gallery'             => fn() => include VIEWS_PATH . '/public/gallery.php',
        '/donate'              => fn() => include VIEWS_PATH . '/public/donate.php',
        '/contact'             => fn() => include VIEWS_PATH . '/public/contact.php',

        // Auth
        '/login'               => fn() => (new AuthController)->showLogin(),
        '/register'            => fn() => (new AuthController)->showRegister(),
        '/registration-success'=> fn() => include VIEWS_PATH . '/auth/registration-success.php',
        '/logout'              => fn() => (new AuthController)->logout(),
        '/verify-email'        => fn() => (new AuthController)->verifyEmail(),
        '/forgot-password'     => fn() => (new AuthController)->showForgotPassword(),
        '/reset-password'      => fn() => (new AuthController)->showResetPassword(),

        // Member
        '/member/dashboard'    => fn() => include VIEWS_PATH . '/member/dashboard.php',
        '/member/profile'      => fn() => include VIEWS_PATH . '/member/profile.php',
        '/member/events'       => fn() => include VIEWS_PATH . '/member/events.php',
        '/member/payments'     => fn() => include VIEWS_PATH . '/member/payments.php',
        '/member/donations'    => fn() => include VIEWS_PATH . '/member/donations.php',
        '/member/messages'     => fn() => include VIEWS_PATH . '/member/messages.php',
        '/member/settings'     => fn() => include VIEWS_PATH . '/member/settings.php',

        // Executive
        '/executive/dashboard' => fn() => include VIEWS_PATH . '/executive/dashboard.php',
        '/executive/members'   => fn() => include VIEWS_PATH . '/executive/members.php',
        '/executive/members/create' => fn() => include VIEWS_PATH . '/executive/member-create.php',
        '/executive/members/{id}' => fn() => include VIEWS_PATH . '/executive/member.php',
        '/executive/events'    => fn() => include VIEWS_PATH . '/executive/events.php',
        '/executive/events/create' => fn() => include VIEWS_PATH . '/executive/event-form.php',
        '/executive/projects'  => fn() => include VIEWS_PATH . '/executive/projects.php',
        '/executive/projects/create' => fn() => include VIEWS_PATH . '/executive/project-form.php',
        '/executive/gallery'   => fn() => include VIEWS_PATH . '/executive/gallery.php',
        '/executive/broadcast' => fn() => include VIEWS_PATH . '/executive/broadcast.php',

        // Financial
        '/financial/dashboard' => fn() => include VIEWS_PATH . '/financial/dashboard.php',
        '/financial/donations' => fn() => include VIEWS_PATH . '/financial/donations.php',
        '/financial/payments'  => fn() => include VIEWS_PATH . '/financial/payments.php',
        '/financial/reports'   => fn() => include VIEWS_PATH . '/financial/reports.php',

        // Admin
        '/admin/dashboard'     => fn() => include VIEWS_PATH . '/admin/dashboard.php',
        '/admin/settings'      => fn() => include VIEWS_PATH . '/admin/settings.php',
        '/admin/audit'         => fn() => include VIEWS_PATH . '/admin/audit.php',
        '/admin/testimonials'  => fn() => include VIEWS_PATH . '/admin/testimonials.php',

        // API
        '/api/notifications'   => fn() => include ROOT_PATH . '/api/notifications.php',
    ],
    'POST' => [
        '/login'               => fn() => (new AuthController)->login(),
        '/register'            => fn() => (new AuthController)->register(),
        '/forgot-password'     => fn() => (new AuthController)->forgotPassword(),
        '/reset-password'      => fn() => (new AuthController)->resetPassword(),
        '/contact'             => fn() => include VIEWS_PATH . '/public/contact.php',

        '/member/profile/update' => fn() => profileUpdate(),
        '/member/profile/avatar' => fn() => avatarUpdate(),

        '/donate/submit'       => fn() => donationSubmit(),

        '/executive/events/store'    => fn() => eventStore(),
        '/executive/events/update'   => fn() => eventUpdate(),
        '/executive/projects/store'  => fn() => projectStore(),
        '/executive/broadcast/send'  => fn() => broadcastSend(),
        '/executive/gallery/upload'  => fn() => galleryUpload(),

        '/financial/donations/{id}/approve' => fn() => donationApprove(),
        '/financial/donations/{id}/cancel'  => fn() => donationCancel(),
        '/financial/reports/export'         => fn() => reportsExport(),

        '/executive/members/{id}/role'   => fn() => memberRoleUpdate(),
        '/executive/members/{id}/toggle' => fn() => memberToggleActive(),
        '/executive/members/{id}/delete' => fn() => memberDelete(),
        '/executive/members/store' => fn() => memberStore(),

        '/admin/testimonials/store'  => fn() => testimonialStore(),
        '/admin/testimonials/update' => fn() => testimonialUpdate(),
        '/admin/testimonials/delete' => fn() => testimonialDelete(),
    ],
];

// ── Dynamic route matching ───────────────────────────────────
function match_route(array $routeSet, string $uri): ?callable
{
    if (isset($routeSet[$uri])) return $routeSet[$uri];

    foreach ($routeSet as $pattern => $handler) {
        $regex = preg_replace('/\{[^}]+\}/', '([^/]+)', $pattern);
        $regex = '#^' . $regex . '$#';
        if (preg_match($regex, $uri, $matches)) {
            array_shift($matches);
            // Pass captured segments via $_GET
            preg_match_all('/\{([^}]+)\}/', $pattern, $keys);
            foreach ($keys[1] as $i => $key) {
                $_GET[$key] = $matches[$i] ?? '';
            }
            return $handler;
        }
    }
    return null;
}

// ── Dispatch ─────────────────────────────────────────────────
$handler = match_route($routes[$method] ?? [], $uri);

if ($handler) {
    $handler();
} else {
    http_response_code(404);
    include VIEWS_PATH . '/errors/404.php';
}

// ── Inline action handlers (keep controllers thin here) ──────
function profileUpdate(): void
{
    require_login();
    verify_csrf();
    $userModel = new User();
    $userModel->updateProfile((int)auth()['id'], $_POST);
    flash('success', 'Profile updated successfully.', 'success');
    redirect('/member/profile');
}

function avatarUpdate(): void
{
    require_login();
    verify_csrf();
    if (empty($_FILES['avatar']['tmp_name'])) {
        redirect('/member/profile');
    }
    try {
        $uploader = new UploadController('avatars');
        $filename = $uploader->uploadImage($_FILES['avatar']);
        $userModel = new User();
        $userModel->updateAvatar((int)auth()['id'], $filename);
        flash('success', 'Profile photo updated.', 'success');
    } catch (RuntimeException $e) {
        flash('error', $e->getMessage(), 'error');
    }
    redirect('/member/profile');
}

function donationSubmit(): void
{
    verify_csrf();
    $user  = auth();
    $amount = (float)($_POST['amount'] ?? 0);
    if ($amount < 100) {
        flash('error', 'Minimum donation is ₵100.', 'error');
        redirect('/donate');
    }
    $ref   = 'DNT-' . strtoupper(substr(uniqid(), -8));
    $model = new Donation();
    $model->create([
        'user_id'      => $user ? $user['id'] : null,
        'campaign_id'  => $_POST['campaign_id'] ?: null,
        'amount'       => $amount,
        'reference'    => $ref,
        'gateway'      => 'manual',
        'donor_name'   => $_POST['donor_name'] ?? ($user ? $user['first_name'] . ' ' . $user['last_name'] : 'Anonymous'),
        'donor_email'  => $_POST['donor_email'] ?? ($user['email'] ?? ''),
        'note'         => sanitize_input($_POST['note'] ?? ''),
    ]);
    flash('success', "Thank you! Your donation reference is {$ref}. Please complete payment to confirm.", 'success');
    redirect('/donate');
}

function eventStore(): void
{
    require_role(ROLE_EXECUTIVE, ROLE_ADMIN);
    verify_csrf();
    $data = $_POST;
    $data['created_by'] = auth()['id'];
    if (!empty($_FILES['image']['tmp_name'])) {
        try {
            $uploader = new UploadController('events');
            $data['image'] = $uploader->uploadImage($_FILES['image']);
        } catch (RuntimeException) { /* non-fatal */ }
    }
    $model = new Event();
    $model->create($data);
    flash('success', 'Event created successfully.', 'success');
    redirect('/executive/events');
}

function eventUpdate(): void
{
    require_role(ROLE_EXECUTIVE, ROLE_ADMIN);
    verify_csrf();
    $id   = (int)($_POST['id'] ?? 0);
    $data = $_POST;
    if (!empty($_FILES['image']['tmp_name'])) {
        try {
            $uploader = new UploadController('events');
            $data['image'] = $uploader->uploadImage($_FILES['image']);
        } catch (RuntimeException) { /* non-fatal */ }
    }
    (new Event)->update($id, $data);
    flash('success', 'Event updated.', 'success');
    redirect('/executive/events');
}

function projectStore(): void
{
    require_role(ROLE_EXECUTIVE, ROLE_ADMIN);
    verify_csrf();
    $data = $_POST;
    $data['created_by'] = auth()['id'];
    if (!empty($_FILES['image']['tmp_name'])) {
        try {
            $uploader = new UploadController('projects');
            $data['image'] = $uploader->uploadImage($_FILES['image']);
        } catch (RuntimeException) { /* non-fatal */ }
    }
    (new Project)->create($data);
    flash('success', 'Project created.', 'success');
    redirect('/executive/projects');
}

function broadcastSend(): void
{
    require_role(ROLE_EXECUTIVE, ROLE_ADMIN);
    verify_csrf();
    $model = new Message();
    $model->send([
        'sender_id'    => auth()['id'],
        'recipient_id' => null,
        'is_broadcast' => 1,
        'subject'      => sanitize_input($_POST['subject'] ?? ''),
        'body'         => sanitize_input($_POST['body']    ?? ''),
    ]);
    flash('success', 'Broadcast message sent to all members.', 'success');
    redirect('/executive/broadcast');
}

function galleryUpload(): void
{
    require_role(ROLE_EXECUTIVE, ROLE_ADMIN);
    verify_csrf();
    $albumId  = (int)($_POST['album_id'] ?? 0);
    $caption  = sanitize_input($_POST['caption'] ?? '');
    $model    = new Gallery();

    if (empty($albumId)) {
        $albumId = $model->createAlbum([
            'created_by'  => auth()['id'],
            'title'       => sanitize_input($_POST['album_title'] ?? 'Untitled Album'),
            'description' => '',
            'is_public'   => 1,
        ]);
    }

    foreach ($_FILES['images']['tmp_name'] as $i => $tmpName) {
        if (!$tmpName) continue;
        $file = [
            'tmp_name' => $tmpName,
            'name'     => $_FILES['images']['name'][$i],
            'size'     => $_FILES['images']['size'][$i],
            'error'    => $_FILES['images']['error'][$i],
            'type'     => $_FILES['images']['type'][$i],
        ];
        try {
            $uploader = new UploadController('gallery');
            $filename = $uploader->uploadImage($file);
            $imageId  = $model->addImage($albumId, (int)auth()['id'], $filename, $caption);
            if ($i === 0) $model->setCover($albumId, $filename);
        } catch (RuntimeException $e) {
            // Log and continue with next file
            error_log('Gallery upload error: ' . $e->getMessage());
        }
    }

    flash('success', 'Images uploaded successfully.', 'success');
    redirect('/executive/gallery');
}

function donationApprove(): void
{
    require_role(ROLE_FINANCIAL, ROLE_ADMIN);
    verify_csrf();
    $id = (int)($_GET['id'] ?? 0);
    (new Donation)->markCompleted($id, 'manual-' . uniqid(), (int)auth()['id']);
    flash('success', 'Donation approved.', 'success');
    redirect('/financial/donations');
}

function donationCancel(): void
{
    require_role(ROLE_FINANCIAL, ROLE_ADMIN);
    verify_csrf();
    $id = (int)($_GET['id'] ?? 0);
    (new Donation)->markCancelled($id);
    flash('success', 'Donation cancelled.', 'success');
    redirect('/financial/donations');
}

function reportsExport(): void
{
    require_role(ROLE_FINANCIAL, ROLE_ADMIN);
    $model     = new Donation();
    $donations = $model->all(1, 9999)['data'];

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="donations-' . date('Y-m-d') . '.csv"');

    $out = fopen('php://output', 'w');
    fputcsv($out, ['ID','Donor','Email','Campaign','Amount','Currency','Status','Reference','Date']);
    foreach ($donations as $d) {
        fputcsv($out, [
            $d['id'],
            $d['donor_name'] ?? ($d['first_name'] . ' ' . $d['last_name']),
            $d['donor_email'] ?? '',
            $d['campaign_title'] ?? 'General',
            $d['amount'],
            $d['currency'],
            $d['status'],
            $d['reference'],
            $d['created_at'],
        ]);
    }
    fclose($out);
    exit;
}

function testimonialStore(): void
{
    require_login(); require_role(ROLE_ADMIN); verify_csrf();
    $m = new Testimonial();
    $m->create([
        'name'            => sanitize_input($_POST['name'] ?? ''),
        'nickname'        => sanitize_input($_POST['nickname'] ?? ''),
        'graduation_year' => null,
        'class_name'      => sanitize_input($_POST['class_name'] ?? ''),
        'message'         => sanitize_input($_POST['message'] ?? ''),
        'is_featured'     => isset($_POST['is_featured']) ? 1 : 0,
        'sort_order'      => (int)($_POST['sort_order'] ?? 0),
    ]);
    flash('success', 'Testimonial added.', 'success');
    redirect('/admin/testimonials');
}

function testimonialUpdate(): void
{
    require_login(); require_role(ROLE_ADMIN); verify_csrf();
    $id = (int)($_POST['id'] ?? 0);
    if (!$id) redirect('/admin/testimonials');
    $m = new Testimonial();
    $m->update($id, [
        'name'            => sanitize_input($_POST['name'] ?? ''),
        'nickname'        => sanitize_input($_POST['nickname'] ?? ''),
        'graduation_year' => null,
        'class_name'      => sanitize_input($_POST['class_name'] ?? ''),
        'message'         => sanitize_input($_POST['message'] ?? ''),
        'is_featured'     => isset($_POST['is_featured']) ? 1 : 0,
        'sort_order'      => (int)($_POST['sort_order'] ?? 0),
    ]);
    flash('success', 'Testimonial updated.', 'success');
    redirect('/admin/testimonials');
}

function testimonialDelete(): void
{
    require_login(); require_role(ROLE_ADMIN); verify_csrf();
    $id = (int)($_POST['id'] ?? 0);
    if ($id) (new Testimonial())->delete($id);
    flash('success', 'Testimonial deleted.', 'success');
    redirect('/admin/testimonials');
}

function memberStore(): void
{
    require_login(); require_role(ROLE_ADMIN); verify_csrf();

    $errors = [];
    $email     = sanitize_input($_POST['email'] ?? '');
    $password  = $_POST['password'] ?? '';
    $confirm   = $_POST['password_confirm'] ?? '';
    $firstName = sanitize_input($_POST['first_name'] ?? '');
    $lastName  = sanitize_input($_POST['last_name'] ?? '');
    $roleId    = (int)($_POST['role_id'] ?? ROLE_MEMBER);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Valid email is required.';
    }
    if (strlen($password) < PASSWORD_MIN_LEN) {
        $errors[] = 'Password must be at least ' . PASSWORD_MIN_LEN . ' characters.';
    }
    if ($password !== $confirm) {
        $errors[] = 'Passwords do not match.';
    }
    if (empty($firstName)) {
        $errors[] = 'First name is required.';
    }
    if (empty($lastName)) {
        $errors[] = 'Last name is required.';
    }
    if ((new User())->findByEmail($email)) {
        $errors[] = 'Email address is already registered.';
    }

    if ($errors) {
        $_SESSION['admin_member_errors'] = $errors;
        $_SESSION['admin_member_old'] = array_filter([
            'email' => $email,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'role_id' => $roleId,
            'occupation' => sanitize_input($_POST['occupation'] ?? ''),
            'employer' => sanitize_input($_POST['employer'] ?? ''),
            'city' => sanitize_input($_POST['city'] ?? ''),
            'country' => sanitize_input($_POST['country'] ?? ''),
        ]);
        redirect('/executive/members/create');
    }

    $userId = (new User())->create([
        'email'        => $email,
        'password'     => $password,
        'first_name'   => $firstName,
        'last_name'    => $lastName,
        'role_id'      => $roleId,
        'verify_token' => bin2hex(random_bytes(32)),
    ]);
    (new User())->activateUser($userId);
    (new User())->updateProfile($userId, [
        'occupation' => sanitize_input($_POST['occupation'] ?? ''),
        'employer' => sanitize_input($_POST['employer'] ?? ''),
        'city' => sanitize_input($_POST['city'] ?? ''),
        'country' => sanitize_input($_POST['country'] ?? ''),
    ]);

    flash('success', 'Member created successfully.', 'success');
    redirect('/executive/members');
}

function memberRoleUpdate(): void
{
    require_login(); require_role(ROLE_ADMIN); verify_csrf();
    $id = (int)($_GET['id'] ?? 0);
    $roleId = (int)($_POST['role_id'] ?? 0);
    if ($id && $roleId) {
        (new User())->updateRole($id, $roleId);
        flash('success', 'Member role updated.', 'success');
    }
    redirect('/executive/members');
}

function memberToggleActive(): void
{
    require_login(); require_role(ROLE_ADMIN); verify_csrf();
    $id = (int)($_GET['id'] ?? 0);
    $active = isset($_POST['active']) ? (bool)$_POST['active'] : false;
    if ($id) {
        (new User())->toggleActive($id, $active);
        flash('success', 'Member status updated.', 'success');
    }
    redirect('/executive/members');
}

function memberDelete(): void
{
    require_login(); require_role(ROLE_ADMIN); verify_csrf();
    $id = (int)($_GET['id'] ?? 0);
    if ($id && $id !== auth()['id']) {
        (new User())->delete($id);
        flash('success', 'Member deleted successfully.', 'success');
    } else {
        flash('error', 'Unable to delete this user.', 'error');
    }
    redirect('/executive/members');
}

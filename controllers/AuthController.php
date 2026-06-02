<?php
class AuthController
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function showLogin(): void
    {
        if (is_logged_in()) redirect($this->dashboardPath());
        include VIEWS_PATH . '/auth/login.php';
    }

    public function login(): void
    {
        verify_csrf();

        $email    = sanitize_input($_POST['email']    ?? '');
        $password = $_POST['password'] ?? '';

        $user = $this->userModel->findByEmail($email);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            flash('error', 'Invalid email or password.', 'error');
            redirect('/login');
        }

        if (!$user['is_active']) {
            flash('error', 'Account not verified. Please check your email.', 'warning');
            redirect('/login');
        }

        regenerate_session();
        $_SESSION['user'] = [
            'id'        => $user['id'],
            'email'     => $user['email'],
            'first_name'=> $user['first_name'],
            'last_name' => $user['last_name'],
            'role_id'   => $user['role_id'],
            'role_name' => $user['role_name'],
            'avatar'    => $user['avatar'] ?? null,
        ];

        $this->userModel->updateLastLogin((int)$user['id']);
        $this->logAudit('login', 'user', $user['id']);

        $redirect = $_SESSION['redirect_after_login'] ?? null;
        unset($_SESSION['redirect_after_login']);

        redirect($redirect ?: $this->dashboardPath((int)$user['role_id']));
    }

    public function showRegister(): void
    {
        if (is_logged_in()) redirect($this->dashboardPath());
        include VIEWS_PATH . '/auth/register.php';
    }

    public function register(): void
    {
        verify_csrf();

        $errors = [];
        $email     = sanitize_input($_POST['email']      ?? '');
        $password  = $_POST['password']                  ?? '';
        $confirm   = $_POST['password_confirm']          ?? '';
        $firstName = sanitize_input($_POST['first_name'] ?? '');
        $lastName  = sanitize_input($_POST['last_name']  ?? '');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL))    $errors[] = 'Invalid email address.';
        if (strlen($password) < PASSWORD_MIN_LEN)          $errors[] = 'Password must be at least ' . PASSWORD_MIN_LEN . ' characters.';
        if ($password !== $confirm)                        $errors[] = 'Passwords do not match.';
        if (empty($firstName))                             $errors[] = 'First name is required.';
        if (empty($lastName))                              $errors[] = 'Last name is required.';
        if ($this->userModel->findByEmail($email))         $errors[] = 'Email address already registered.';

        if ($errors) {
            $_SESSION['register_errors'] = $errors;
            $_SESSION['register_old']    = [
                'email' => $email,
                'firstName' => $firstName,
                'lastName' => $lastName,
                'phone' => sanitize_input($_POST['phone'] ?? ''),
                'graduation_year' => sanitize_input($_POST['graduation_year'] ?? ''),
                'degree' => sanitize_input($_POST['degree'] ?? ''),
                'department' => sanitize_input($_POST['department'] ?? ''),
                'occupation' => sanitize_input($_POST['occupation'] ?? ''),
                'employer' => sanitize_input($_POST['employer'] ?? ''),
                'city' => sanitize_input($_POST['city'] ?? ''),
                'country' => sanitize_input($_POST['country'] ?? ''),
                'membership_year' => sanitize_input($_POST['membership_year'] ?? ''),
            ];
            redirect('/register');
        }

        $token = bin2hex(random_bytes(32));
        $userId = $this->userModel->create([
            'email'        => $email,
            'password'     => $password,
            'first_name'   => $firstName,
            'last_name'    => $lastName,
            'verify_token' => $token,
        ]);

        // Save optional profile fields submitted at registration
        $profileFields = [
            'phone' => sanitize_input($_POST['phone'] ?? ''),
            'graduation_year' => sanitize_input($_POST['graduation_year'] ?? ''),
            'degree' => sanitize_input($_POST['degree'] ?? ''),
            'department' => sanitize_input($_POST['department'] ?? ''),
            'occupation' => sanitize_input($_POST['occupation'] ?? ''),
            'employer' => sanitize_input($_POST['employer'] ?? ''),
            'city' => sanitize_input($_POST['city'] ?? ''),
            'state' => sanitize_input($_POST['state'] ?? ''),
            'country' => sanitize_input($_POST['country'] ?? ''),
            'membership_year' => sanitize_input($_POST['membership_year'] ?? ''),
        ];

        // Update profile row
        try {
            $this->userModel->updateProfile((int)$userId, $profileFields);
        } catch (Throwable $e) {
            // non-fatal
        }

        // Handle avatar upload if provided
        if (!empty($_FILES['avatar']['tmp_name'])) {
            try {
                $uploader = new UploadController('avatars');
                $filename = $uploader->uploadImage($_FILES['avatar']);
                $this->userModel->updateAvatar((int)$userId, $filename);
            } catch (RuntimeException $e) {
                // Non-fatal - continue without avatar
            }
        }

        $link = APP_URL . '/verify-email?token=' . $token;
        $html = "
            <h2>Welcome to NJOSA Alumni Portal</h2>
            <p>Hi {$firstName}, please verify your email by clicking the link below:</p>
            <p><a href='{$link}'>Verify Email Address</a></p>
            <p>If you did not create an account, ignore this email.</p>
        ";
        send_mail($email, 'Verify your NJOSA Alumni Portal account', $html);

        flash('success', 'Account created! Please check your email to verify.', 'success');
        redirect('/login');
    }

    public function verifyEmail(): void
    {
        $token = sanitize_input($_GET['token'] ?? '');
        if ($this->userModel->verifyEmail($token)) {
            flash('success', 'Email verified! You can now log in.', 'success');
        } else {
            flash('error', 'Invalid or expired verification link.', 'error');
        }
        redirect('/login');
    }

    public function showForgotPassword(): void
    {
        include VIEWS_PATH . '/auth/forgot-password.php';
    }

    public function forgotPassword(): void
    {
        verify_csrf();

        $email = sanitize_input($_POST['email'] ?? '');
        $token = bin2hex(random_bytes(32));

        // Always show success to prevent email enumeration
        if ($this->userModel->setPasswordResetToken($email, $token)) {
            $link = APP_URL . '/reset-password?token=' . $token;
            $html = "
                <h2>NJOSA Alumni Portal – Password Reset</h2>
                <p>Click the link below to reset your password (valid for 1 hour):</p>
                <p><a href='{$link}'>Reset Password</a></p>
            ";
            send_mail($email, 'Reset your NJOSA Alumni Portal password', $html);
        }

        flash('success', 'If that email exists, a reset link has been sent.', 'success');
        redirect('/forgot-password');
    }

    public function showResetPassword(): void
    {
        $token = sanitize_input($_GET['token'] ?? '');
        include VIEWS_PATH . '/auth/reset-password.php';
    }

    public function resetPassword(): void
    {
        verify_csrf();

        $token    = sanitize_input($_POST['token'] ?? '');
        $password = $_POST['password']             ?? '';
        $confirm  = $_POST['password_confirm']     ?? '';

        if (strlen($password) < PASSWORD_MIN_LEN || $password !== $confirm) {
            flash('error', 'Passwords do not match or too short.', 'error');
            redirect('/reset-password?token=' . urlencode($token));
        }

        if ($this->userModel->resetPassword($token, $password)) {
            flash('success', 'Password reset successful. Please log in.', 'success');
            redirect('/login');
        } else {
            flash('error', 'Invalid or expired reset link.', 'error');
            redirect('/forgot-password');
        }
    }

    public function logout(): void
    {
        $this->logAudit('logout', 'user', (int)($_SESSION['user']['id'] ?? 0));
        session_destroy();
        redirect('/login');
    }

    // ----------------------------------------------------------------
    private function dashboardPath(?int $roleId = null): string
    {
        $roleId = $roleId ?? (int)($_SESSION['user']['role_id'] ?? 1);
        return match ($roleId) {
            ROLE_ADMIN     => '/admin/dashboard',
            ROLE_EXECUTIVE => '/executive/dashboard',
            ROLE_FINANCIAL => '/financial/dashboard',
            default        => '/member/dashboard',
        };
    }

    private function logAudit(string $action, string $entity, int $entityId): void
    {
        try {
            $db = Database::getInstance();
            $db->prepare(
                'INSERT INTO audit_logs (user_id, action, entity_type, entity_id, ip_address, user_agent)
                 VALUES (?, ?, ?, ?, ?, ?)'
            )->execute([
                $_SESSION['user']['id'] ?? null,
                $action, $entity, $entityId,
                $_SERVER['REMOTE_ADDR'] ?? '',
                substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255),
            ]);
        } catch (PDOException) {
            // Non-fatal audit failure
        }
    }
}

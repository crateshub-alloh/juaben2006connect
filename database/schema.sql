-- ============================================================
-- NJOSA Alumni Portal – Database Schema
-- MySQL 8.0+ | UTF8MB4 | InnoDB
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS njosa_alumni
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE njosa_alumni;

-- ------------------------------------------------------------
-- ROLES
-- ------------------------------------------------------------
CREATE TABLE roles (
  id          TINYINT UNSIGNED  NOT NULL AUTO_INCREMENT,
  name        VARCHAR(50)       NOT NULL UNIQUE,
  label       VARCHAR(80)       NOT NULL,
  created_at  TIMESTAMP         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB;

INSERT INTO roles (name, label) VALUES
  ('member',            'Member'),
  ('executive',         'Executive'),
  ('financial_officer', 'Financial Officer'),
  ('super_admin',       'Super Administrator');

-- ------------------------------------------------------------
-- USERS
-- ------------------------------------------------------------
CREATE TABLE users (
  id                  INT UNSIGNED      NOT NULL AUTO_INCREMENT,
  role_id             TINYINT UNSIGNED  NOT NULL DEFAULT 1,
  email               VARCHAR(180)      NOT NULL UNIQUE,
  password_hash       VARCHAR(255)      NOT NULL,
  first_name          VARCHAR(80)       NOT NULL,
  last_name           VARCHAR(80)       NOT NULL,
  is_active           TINYINT(1)        NOT NULL DEFAULT 0,
  is_profile_complete TINYINT(1)        NOT NULL DEFAULT 0,
  email_verified_at   TIMESTAMP         NULL,
  email_verify_token  VARCHAR(100)      NULL,
  password_reset_token VARCHAR(100)     NULL,
  password_reset_expires TIMESTAMP      NULL,
  last_login_at       TIMESTAMP         NULL,
  created_at          TIMESTAMP         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at          TIMESTAMP         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_users_role (role_id),
  KEY idx_users_email_verified (email_verified_at),
  CONSTRAINT fk_users_role FOREIGN KEY (role_id) REFERENCES roles (id)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- PROFILES
-- ------------------------------------------------------------
CREATE TABLE profiles (
  user_id         INT UNSIGNED  NOT NULL,
  avatar          VARCHAR(255)  NULL,
  phone           VARCHAR(30)   NULL,
  graduation_year YEAR          NULL,
  degree          VARCHAR(120)  NULL,
  department      VARCHAR(120)  NULL,
  bio             TEXT          NULL,
  occupation      VARCHAR(120)  NULL,
  employer        VARCHAR(120)  NULL,
  city            VARCHAR(80)   NULL,
  state           VARCHAR(80)   NULL,
  country         VARCHAR(80)   NULL DEFAULT 'Ghana',
  house           VARCHAR(50)   NULL,
  linkedin_url    VARCHAR(255)  NULL,
  twitter_url     VARCHAR(255)  NULL,
  facebook_url    VARCHAR(255)  NULL,
  membership_year YEAR          NULL,
  dues_paid_until DATE          NULL,
  updated_at      TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id),
  CONSTRAINT fk_profiles_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- EVENTS
-- ------------------------------------------------------------
CREATE TABLE events (
  id              INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  created_by      INT UNSIGNED  NOT NULL,
  title           VARCHAR(200)  NOT NULL,
  slug            VARCHAR(220)  NOT NULL UNIQUE,
  description     TEXT          NULL,
  image           VARCHAR(255)  NULL,
  location        VARCHAR(255)  NULL,
  venue           VARCHAR(255)  NULL,
  starts_at       DATETIME      NOT NULL,
  ends_at         DATETIME      NOT NULL,
  capacity        SMALLINT UNSIGNED NULL,
  ticket_price    DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  status          ENUM('draft','upcoming','ongoing','completed','cancelled')
                                NOT NULL DEFAULT 'draft',
  is_public       TINYINT(1)    NOT NULL DEFAULT 1,
  created_at      TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_events_status (status),
  KEY idx_events_starts_at (starts_at),
  CONSTRAINT fk_events_creator FOREIGN KEY (created_by) REFERENCES users (id)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- EVENT REGISTRATIONS
-- ------------------------------------------------------------
CREATE TABLE event_registrations (
  id            INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  event_id      INT UNSIGNED  NOT NULL,
  user_id       INT UNSIGNED  NOT NULL,
  status        ENUM('registered','attended','cancelled') NOT NULL DEFAULT 'registered',
  registered_at TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_event_user (event_id, user_id),
  KEY idx_er_user (user_id),
  CONSTRAINT fk_er_event FOREIGN KEY (event_id) REFERENCES events (id) ON DELETE CASCADE,
  CONSTRAINT fk_er_user  FOREIGN KEY (user_id)  REFERENCES users  (id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- DONATIONS
-- ------------------------------------------------------------
CREATE TABLE donations (
  id              INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  user_id         INT UNSIGNED    NULL,
  campaign_id     INT UNSIGNED    NULL,
  amount          DECIMAL(12,2)   NOT NULL,
  currency        CHAR(3)         NOT NULL DEFAULT 'GHS',
  reference       VARCHAR(100)    NOT NULL UNIQUE,
  gateway         VARCHAR(60)     NOT NULL DEFAULT 'manual',
  gateway_ref     VARCHAR(200)    NULL,
  status          ENUM('pending','completed','cancelled','refunded')
                                  NOT NULL DEFAULT 'pending',
  donor_name      VARCHAR(160)    NULL,
  donor_email     VARCHAR(180)    NULL,
  note            TEXT            NULL,
  approved_by     INT UNSIGNED    NULL,
  approved_at     TIMESTAMP       NULL,
  receipt_sent    TINYINT(1)      NOT NULL DEFAULT 0,
  created_at      TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_donations_user   (user_id),
  KEY idx_donations_status (status),
  KEY idx_donations_date   (created_at),
  CONSTRAINT fk_donations_user     FOREIGN KEY (user_id)     REFERENCES users (id) ON DELETE SET NULL,
  CONSTRAINT fk_donations_approver FOREIGN KEY (approved_by) REFERENCES users (id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- DONATION CAMPAIGNS
-- ------------------------------------------------------------
CREATE TABLE donation_campaigns (
  id          INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  created_by  INT UNSIGNED    NOT NULL,
  title       VARCHAR(200)    NOT NULL,
  slug        VARCHAR(220)    NOT NULL UNIQUE,
  description TEXT            NULL,
  image       VARCHAR(255)    NULL,
  goal_amount DECIMAL(14,2)   NOT NULL DEFAULT 0.00,
  start_date  DATE            NULL,
  end_date    DATE            NULL,
  is_active   TINYINT(1)      NOT NULL DEFAULT 1,
  created_at  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  CONSTRAINT fk_dc_creator FOREIGN KEY (created_by) REFERENCES users (id)
) ENGINE=InnoDB;

ALTER TABLE donations
  ADD CONSTRAINT fk_donations_campaign
  FOREIGN KEY (campaign_id) REFERENCES donation_campaigns (id) ON DELETE SET NULL;

-- ------------------------------------------------------------
-- PAYMENTS (Dues / Membership)
-- ------------------------------------------------------------
CREATE TABLE payments (
  id          INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  user_id     INT UNSIGNED    NOT NULL,
  type        ENUM('dues','event_ticket','other') NOT NULL DEFAULT 'dues',
  amount      DECIMAL(12,2)   NOT NULL,
  currency    CHAR(3)         NOT NULL DEFAULT 'GHS',
  reference   VARCHAR(100)    NOT NULL UNIQUE,
  gateway     VARCHAR(60)     NOT NULL DEFAULT 'manual',
  gateway_ref VARCHAR(200)    NULL,
  status      ENUM('pending','completed','failed','refunded')
                              NOT NULL DEFAULT 'pending',
  period_year YEAR            NULL,
  note        TEXT            NULL,
  created_at  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_payments_user   (user_id),
  KEY idx_payments_status (status),
  CONSTRAINT fk_payments_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- PROJECTS
-- ------------------------------------------------------------
CREATE TABLE projects (
  id              INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  created_by      INT UNSIGNED    NOT NULL,
  title           VARCHAR(200)    NOT NULL,
  slug            VARCHAR(220)    NOT NULL UNIQUE,
  description     TEXT            NULL,
  image           VARCHAR(255)    NULL,
  goal_amount     DECIMAL(14,2)   NOT NULL DEFAULT 0.00,
  raised_amount   DECIMAL(14,2)   NOT NULL DEFAULT 0.00,
  start_date      DATE            NULL,
  end_date        DATE            NULL,
  status          ENUM('planned','active','completed','on_hold')
                                  NOT NULL DEFAULT 'planned',
  is_featured     TINYINT(1)      NOT NULL DEFAULT 0,
  created_at      TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_projects_status (status),
  CONSTRAINT fk_projects_creator FOREIGN KEY (created_by) REFERENCES users (id)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- PROJECT UPDATES
-- ------------------------------------------------------------
CREATE TABLE project_updates (
  id          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  project_id  INT UNSIGNED  NOT NULL,
  author_id   INT UNSIGNED  NOT NULL,
  title       VARCHAR(200)  NOT NULL,
  body        TEXT          NOT NULL,
  created_at  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_pu_project (project_id),
  CONSTRAINT fk_pu_project FOREIGN KEY (project_id) REFERENCES projects  (id) ON DELETE CASCADE,
  CONSTRAINT fk_pu_author  FOREIGN KEY (author_id)  REFERENCES users     (id)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- GALLERY
-- ------------------------------------------------------------
CREATE TABLE gallery_albums (
  id          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  created_by  INT UNSIGNED  NOT NULL,
  title       VARCHAR(200)  NOT NULL,
  slug        VARCHAR(220)  NOT NULL UNIQUE,
  description TEXT          NULL,
  cover_image VARCHAR(255)  NULL,
  is_public   TINYINT(1)    NOT NULL DEFAULT 1,
  created_at  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  CONSTRAINT fk_ga_creator FOREIGN KEY (created_by) REFERENCES users (id)
) ENGINE=InnoDB;

CREATE TABLE gallery_images (
  id          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  album_id    INT UNSIGNED  NOT NULL,
  uploaded_by INT UNSIGNED  NOT NULL,
  filename    VARCHAR(255)  NOT NULL,
  caption     VARCHAR(255)  NULL,
  sort_order  SMALLINT      NOT NULL DEFAULT 0,
  created_at  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_gi_album (album_id),
  CONSTRAINT fk_gi_album    FOREIGN KEY (album_id)    REFERENCES gallery_albums (id) ON DELETE CASCADE,
  CONSTRAINT fk_gi_uploader FOREIGN KEY (uploaded_by) REFERENCES users         (id)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- MESSAGES
-- ------------------------------------------------------------
CREATE TABLE messages (
  id          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  sender_id   INT UNSIGNED  NOT NULL,
  recipient_id INT UNSIGNED NULL,
  is_broadcast TINYINT(1)   NOT NULL DEFAULT 0,
  subject     VARCHAR(255)  NOT NULL,
  body        TEXT          NOT NULL,
  read_at     TIMESTAMP     NULL,
  is_deleted  TINYINT(1)    NOT NULL DEFAULT 0,
  created_at  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_msg_recipient (recipient_id),
  KEY idx_msg_sender    (sender_id),
  CONSTRAINT fk_msg_sender    FOREIGN KEY (sender_id)    REFERENCES users (id),
  CONSTRAINT fk_msg_recipient FOREIGN KEY (recipient_id) REFERENCES users (id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- NOTIFICATIONS
-- ------------------------------------------------------------
CREATE TABLE notifications (
  id          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  user_id     INT UNSIGNED  NOT NULL,
  type        VARCHAR(80)   NOT NULL,
  title       VARCHAR(200)  NOT NULL,
  body        TEXT          NULL,
  url         VARCHAR(255)  NULL,
  read_at     TIMESTAMP     NULL,
  created_at  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_notif_user (user_id),
  KEY idx_notif_read (read_at),
  CONSTRAINT fk_notif_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- AUDIT LOGS
-- ------------------------------------------------------------
CREATE TABLE audit_logs (
  id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id     INT UNSIGNED    NULL,
  action      VARCHAR(120)    NOT NULL,
  entity_type VARCHAR(80)     NULL,
  entity_id   INT UNSIGNED    NULL,
  old_values  JSON            NULL,
  new_values  JSON            NULL,
  ip_address  VARCHAR(45)     NULL,
  user_agent  VARCHAR(255)    NULL,
  created_at  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_audit_user   (user_id),
  KEY idx_audit_action (action),
  KEY idx_audit_date   (created_at),
  CONSTRAINT fk_audit_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- TESTIMONIALS
-- ------------------------------------------------------------
CREATE TABLE testimonials (
  id              INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  user_id         INT UNSIGNED  NULL,
  name            VARCHAR(160)  NOT NULL,
  graduation_year YEAR          NULL,
  message         TEXT          NOT NULL,
  avatar          VARCHAR(255)  NULL,
  is_featured     TINYINT(1)    NOT NULL DEFAULT 0,
  sort_order      SMALLINT      NOT NULL DEFAULT 0,
  created_at      TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  CONSTRAINT fk_testimonials_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- SITE SETTINGS
-- ------------------------------------------------------------
CREATE TABLE site_settings (
  `key`   VARCHAR(100)  NOT NULL,
  `value` TEXT          NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB;

INSERT INTO site_settings (`key`, `value`) VALUES
  ('site_name',        'Juaben2006 Connect'),
  ('site_email',       'info@juaben2006connect.com'),
  ('site_phone',       '+233 000 000 000'),
  ('site_address',     'Koforidua, Ghana'),
  ('members_count',    '0'),
  ('events_count',     '0'),
  ('funds_raised',     '0'),
  ('projects_count',   '0'),
  ('paystack_key',     ''),
  ('smtp_host',        ''),
  ('smtp_port',        '587'),
  ('smtp_user',        ''),
  ('smtp_pass',        '');

-- Seed default super admin (password: Admin@1234 — CHANGE IMMEDIATELY)
INSERT INTO users (role_id, email, password_hash, first_name, last_name, is_active, email_verified_at)
VALUES (4, 'admin@juaben2006connect.com',
        '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uHpFAsGXa',
        'Super', 'Admin', 1, NOW());

INSERT INTO profiles (user_id, graduation_year, membership_year)
VALUES (LAST_INSERT_ID(), 2020, 2020);

SET FOREIGN_KEY_CHECKS = 1;

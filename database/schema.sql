CREATE TABLE IF NOT EXISTS admins (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 email VARCHAR(190) NOT NULL UNIQUE,
 password_hash VARCHAR(255) NOT NULL,
 created_at DATETIME NOT NULL,
 updated_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS youtube_channels (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 channel_id VARCHAR(64) NOT NULL UNIQUE,
 channel_name VARCHAR(255) NOT NULL,
 token_ciphertext LONGTEXT NOT NULL,
 token_expires_at DATETIME NULL,
 status ENUM('connected','error','revoked','disabled') NOT NULL DEFAULT 'connected',
 default_title_prefix VARCHAR(255) NULL,
 default_title_suffix VARCHAR(255) NULL,
 default_description MEDIUMTEXT NULL,
 default_tags_json TEXT NULL,
 default_hashtags_json TEXT NULL,
 default_category_id VARCHAR(20) NOT NULL DEFAULT '22',
 default_privacy_status ENUM('private','public','unlisted') NOT NULL DEFAULT 'private',
 default_made_for_kids TINYINT(1) NOT NULL DEFAULT 0,
 default_timezone VARCHAR(64) NOT NULL DEFAULT 'UTC',
 last_error TEXT NULL,
 created_at DATETIME NOT NULL,
 updated_at DATETIME NOT NULL,
 INDEX idx_youtube_status(status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS google_drive_connections (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 account_email VARCHAR(190) NULL,
 token_ciphertext LONGTEXT NOT NULL,
 token_expires_at DATETIME NULL,
 status ENUM('connected','error','revoked','disabled') NOT NULL DEFAULT 'connected',
 last_error TEXT NULL,
 created_at DATETIME NOT NULL,
 updated_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS drive_folders (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 drive_connection_id BIGINT UNSIGNED NOT NULL,
 youtube_channel_id BIGINT UNSIGNED NOT NULL,
 folder_id VARCHAR(255) NOT NULL,
 folder_name VARCHAR(255) NOT NULL,
 enabled TINYINT(1) NOT NULL DEFAULT 1,
 last_scanned_at DATETIME NULL,
 created_at DATETIME NOT NULL,
 updated_at DATETIME NOT NULL,
 UNIQUE KEY uq_drive_folder_channel (drive_connection_id, youtube_channel_id, folder_id),
 INDEX idx_folder_channel (youtube_channel_id),
 CONSTRAINT fk_folder_drive FOREIGN KEY (drive_connection_id) REFERENCES google_drive_connections(id) ON DELETE CASCADE,
 CONSTRAINT fk_folder_channel FOREIGN KEY (youtube_channel_id) REFERENCES youtube_channels(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS upload_schedules (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 youtube_channel_id BIGINT UNSIGNED NOT NULL,
 day_of_week TINYINT UNSIGNED NOT NULL,
 upload_time TIME NOT NULL,
 timezone VARCHAR(64) NOT NULL,
 enabled TINYINT(1) NOT NULL DEFAULT 1,
 created_at DATETIME NOT NULL,
 updated_at DATETIME NOT NULL,
 UNIQUE KEY uq_schedule_slot (youtube_channel_id, day_of_week, upload_time, timezone),
 INDEX idx_schedule_channel (youtube_channel_id, enabled, day_of_week, upload_time),
 CONSTRAINT fk_schedule_channel FOREIGN KEY (youtube_channel_id) REFERENCES youtube_channels(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS videos (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 youtube_channel_id BIGINT UNSIGNED NOT NULL,
 drive_folder_id BIGINT UNSIGNED NULL,
 drive_file_id VARCHAR(255) NOT NULL UNIQUE,
 drive_file_name VARCHAR(500) NOT NULL,
 mime_type VARCHAR(120) NULL,
 file_size BIGINT UNSIGNED NULL,
 title VARCHAR(500) NOT NULL,
 description MEDIUMTEXT NULL,
 tags_json TEXT NULL,
 hashtags_json TEXT NULL,
 category_id VARCHAR(20) NOT NULL DEFAULT '22',
 privacy_status ENUM('private','public','unlisted') NOT NULL DEFAULT 'private',
 made_for_kids TINYINT(1) NOT NULL DEFAULT 0,
 scheduled_at DATETIME NULL,
 scheduled_timezone VARCHAR(64) NULL,
 manual_schedule TINYINT(1) NOT NULL DEFAULT 0,
 youtube_video_id VARCHAR(64) NULL,
 status ENUM('Pending','Queued','Scheduled','Uploading','Uploaded','Failed','Retry') NOT NULL DEFAULT 'Pending',
 retry_count INT UNSIGNED NOT NULL DEFAULT 0,
 next_retry_at DATETIME NULL,
 last_error TEXT NULL,
 uploaded_at DATETIME NULL,
 created_at DATETIME NOT NULL,
 updated_at DATETIME NOT NULL,
 INDEX idx_video_channel_status (youtube_channel_id, status),
 INDEX idx_video_schedule (youtube_channel_id, scheduled_at, status),
 INDEX idx_video_retry (status, next_retry_at),
 INDEX idx_video_youtube (youtube_video_id),
 CONSTRAINT fk_video_channel FOREIGN KEY (youtube_channel_id) REFERENCES youtube_channels(id) ON DELETE CASCADE,
 CONSTRAINT fk_video_folder FOREIGN KEY (drive_folder_id) REFERENCES drive_folders(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS upload_attempts (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 video_id BIGINT UNSIGNED NOT NULL,
 attempt_number INT UNSIGNED NOT NULL,
 started_at DATETIME NOT NULL,
 finished_at DATETIME NULL,
 success TINYINT(1) NOT NULL DEFAULT 0,
 http_status INT NULL,
 error_message TEXT NULL,
 response_json MEDIUMTEXT NULL,
 INDEX idx_attempt_video (video_id, attempt_number),
 CONSTRAINT fk_attempt_video FOREIGN KEY (video_id) REFERENCES videos(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS system_logs (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 level ENUM('INFO','WARNING','ERROR','DEBUG') NOT NULL DEFAULT 'INFO',
 event VARCHAR(120) NOT NULL,
 message TEXT NOT NULL,
 context_json MEDIUMTEXT NULL,
 created_at DATETIME NOT NULL,
 INDEX idx_log_created (created_at),
 INDEX idx_log_event (event)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS cron_locks (
 lock_name VARCHAR(100) PRIMARY KEY,
 token VARCHAR(128) NOT NULL,
 acquired_at DATETIME NOT NULL,
 expires_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS settings (
 setting_key VARCHAR(120) PRIMARY KEY,
 setting_value TEXT NULL,
 updated_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

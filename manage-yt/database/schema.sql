SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS cron_logs, analytics_cache, upload_history, upload_jobs, video_metadata, videos,
drive_folders, channel_defaults, youtube_channels, google_accounts, system_settings, users;

CREATE TABLE users (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(120) NOT NULL,
 email VARCHAR(190) NOT NULL UNIQUE,
 password_hash VARCHAR(255) NOT NULL,
 role ENUM('admin','user') NOT NULL DEFAULT 'user',
 status ENUM('active','disabled') NOT NULL DEFAULT 'active',
 last_login_at DATETIME NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE google_accounts (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 user_id BIGINT UNSIGNED NOT NULL,
 google_account_id VARCHAR(191) NOT NULL,
 email VARCHAR(190) NULL,
 access_token_encrypted TEXT NULL,
 refresh_token_encrypted TEXT NULL,
 token_expiry DATETIME NULL,
 scopes JSON NULL,
 status ENUM('connected','needs_reconnect','disconnected') NOT NULL DEFAULT 'connected',
 token_updated_at DATETIME NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 UNIQUE KEY uq_google_user_account (user_id, google_account_id),
 KEY idx_google_user_status (user_id,status),
 CONSTRAINT fk_google_user FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE youtube_channels (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 user_id BIGINT UNSIGNED NOT NULL,
 google_account_id BIGINT UNSIGNED NOT NULL,
 youtube_channel_id VARCHAR(191) NOT NULL,
 title VARCHAR(255) NOT NULL,
 handle VARCHAR(255) NULL,
 custom_url VARCHAR(500) NULL,
 thumbnail_url VARCHAR(1000) NULL,
 subscriber_count BIGINT UNSIGNED NULL,
 video_count BIGINT UNSIGNED NULL,
 view_count BIGINT UNSIGNED NULL,
 status ENUM('active','disabled') NOT NULL DEFAULT 'active',
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 UNIQUE KEY uq_youtube_channel (youtube_channel_id),
 KEY idx_channel_user (user_id),
 CONSTRAINT fk_channel_user FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
 CONSTRAINT fk_channel_google FOREIGN KEY(google_account_id) REFERENCES google_accounts(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE channel_defaults (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 channel_id BIGINT UNSIGNED NOT NULL UNIQUE,
 title_template VARCHAR(500) NULL,
 default_description TEXT NULL,
 default_tags JSON NULL,
 category_id VARCHAR(30) NULL,
 privacy_status ENUM('private','unlisted','public') NOT NULL DEFAULT 'private',
 thumbnail_settings JSON NULL,
 scheduling_settings JSON NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 CONSTRAINT fk_defaults_channel FOREIGN KEY(channel_id) REFERENCES youtube_channels(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE drive_folders (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 channel_id BIGINT UNSIGNED NULL,
 user_id BIGINT UNSIGNED NOT NULL,
 parent_folder_id BIGINT UNSIGNED NULL,
 google_drive_folder_id VARCHAR(191) NOT NULL,
 name VARCHAR(255) NOT NULL,
 folder_type ENUM('root','tiktok_downloads','ready_to_upload','scheduled','uploaded','failed','drafts','custom') NOT NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 UNIQUE KEY uq_drive_folder (google_drive_folder_id),
 KEY idx_drive_channel_type (channel_id,folder_type),
 CONSTRAINT fk_drive_user FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
 CONSTRAINT fk_drive_channel FOREIGN KEY(channel_id) REFERENCES youtube_channels(id) ON DELETE CASCADE,
 CONSTRAINT fk_drive_parent FOREIGN KEY(parent_folder_id) REFERENCES drive_folders(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE videos (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 user_id BIGINT UNSIGNED NOT NULL,
 channel_id BIGINT UNSIGNED NOT NULL,
 drive_folder_id BIGINT UNSIGNED NULL,
 google_drive_file_id VARCHAR(191) NULL,
 drive_filename VARCHAR(500) NULL,
 source_platform VARCHAR(50) NULL,
 source_url VARCHAR(1000) NULL,
 source_video_id VARCHAR(191) NULL,
 source_author VARCHAR(255) NULL,
 source_publish_date DATETIME NULL,
 original_title VARCHAR(500) NOT NULL,
 status ENUM('IMPORTED','PROCESSING','READY','SCHEDULED','UPLOADING','UPLOADED','PUBLISHED','FAILED','CANCELLED','DRAFT') NOT NULL DEFAULT 'IMPORTED',
 youtube_video_id VARCHAR(191) NULL,
 youtube_url VARCHAR(1000) NULL,
 uploaded_at DATETIME NULL,
 published_at DATETIME NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 UNIQUE KEY uq_source_video (source_platform,source_video_id),
 UNIQUE KEY uq_drive_file (google_drive_file_id),
 KEY idx_videos_channel_status (channel_id,status),
 KEY idx_videos_due (status,created_at),
 CONSTRAINT fk_video_user FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
 CONSTRAINT fk_video_channel FOREIGN KEY(channel_id) REFERENCES youtube_channels(id) ON DELETE CASCADE,
 CONSTRAINT fk_video_drive FOREIGN KEY(drive_folder_id) REFERENCES drive_folders(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE video_metadata (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 video_id BIGINT UNSIGNED NOT NULL UNIQUE,
 title VARCHAR(500) NOT NULL,
 description TEXT NULL,
 tags JSON NULL,
 category_id VARCHAR(30) NULL,
 privacy_status ENUM('private','unlisted','public') NOT NULL DEFAULT 'private',
 thumbnail_drive_file_id VARCHAR(191) NULL,
 scheduled_at DATETIME NULL,
 target_channel_id BIGINT UNSIGNED NULL,
 extra JSON NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 CONSTRAINT fk_metadata_video FOREIGN KEY(video_id) REFERENCES videos(id) ON DELETE CASCADE,
 CONSTRAINT fk_metadata_channel FOREIGN KEY(target_channel_id) REFERENCES youtube_channels(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE upload_jobs (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 video_id BIGINT UNSIGNED NOT NULL,
 channel_id BIGINT UNSIGNED NOT NULL,
 idempotency_key CHAR(64) NOT NULL UNIQUE,
 scheduled_at DATETIME NOT NULL,
 status ENUM('PENDING','PROCESSING','UPLOADING','COMPLETED','FAILED','CANCELLED') NOT NULL DEFAULT 'PENDING',
 attempts INT UNSIGNED NOT NULL DEFAULT 0,
 max_attempts INT UNSIGNED NOT NULL DEFAULT 3,
 locked_at DATETIME NULL,
 locked_by VARCHAR(191) NULL,
 last_attempt_at DATETIME NULL,
 completed_at DATETIME NULL,
 error_message TEXT NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 KEY idx_jobs_due (status,scheduled_at),
 CONSTRAINT fk_job_video FOREIGN KEY(video_id) REFERENCES videos(id) ON DELETE CASCADE,
 CONSTRAINT fk_job_channel FOREIGN KEY(channel_id) REFERENCES youtube_channels(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE upload_history (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 video_id BIGINT UNSIGNED NOT NULL,
 channel_id BIGINT UNSIGNED NOT NULL,
 upload_job_id BIGINT UNSIGNED NULL,
 youtube_video_id VARCHAR(191) NULL,
 youtube_url VARCHAR(1000) NULL,
 result_status ENUM('success','failed') NOT NULL,
 error_message TEXT NULL,
 upload_started_at DATETIME NULL,
 upload_completed_at DATETIME NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 KEY idx_history_video (video_id),
 CONSTRAINT fk_history_video FOREIGN KEY(video_id) REFERENCES videos(id) ON DELETE CASCADE,
 CONSTRAINT fk_history_channel FOREIGN KEY(channel_id) REFERENCES youtube_channels(id) ON DELETE CASCADE,
 CONSTRAINT fk_history_job FOREIGN KEY(upload_job_id) REFERENCES upload_jobs(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE analytics_cache (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 channel_id BIGINT UNSIGNED NOT NULL,
 video_id BIGINT UNSIGNED NULL,
 metric_date DATE NULL,
 metric_type ENUM('channel_summary','daily_views','daily_subscribers','video_stats') NOT NULL,
 payload JSON NOT NULL,
 fetched_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 expires_at DATETIME NULL,
 UNIQUE KEY uq_analytics (channel_id,video_id,metric_date,metric_type),
 KEY idx_analytics_lookup (channel_id,metric_type,metric_date),
 CONSTRAINT fk_analytics_channel FOREIGN KEY(channel_id) REFERENCES youtube_channels(id) ON DELETE CASCADE,
 CONSTRAINT fk_analytics_video FOREIGN KEY(video_id) REFERENCES videos(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE system_settings (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 user_id BIGINT UNSIGNED NULL,
 setting_key VARCHAR(190) NOT NULL,
 setting_value LONGTEXT NULL,
 is_secret TINYINT(1) NOT NULL DEFAULT 0,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 UNIQUE KEY uq_setting_scope_key (user_id,setting_key),
 CONSTRAINT fk_setting_user FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE cron_logs (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 job_name VARCHAR(190) NOT NULL,
 run_id CHAR(36) NOT NULL,
 status ENUM('started','success','failed','skipped') NOT NULL,
 started_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 finished_at DATETIME NULL,
 processed_count INT UNSIGNED NOT NULL DEFAULT 0,
 error_message TEXT NULL,
 payload JSON NULL,
 KEY idx_cron_job_time (job_name,started_at),
 KEY idx_cron_run (run_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS=1;

CREATE TABLE IF NOT EXISTS users (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, name VARCHAR(120) NOT NULL, email VARCHAR(190) NOT NULL UNIQUE,
 password_hash VARCHAR(255) NULL, role VARCHAR(40) NOT NULL DEFAULT 'admin', status VARCHAR(30) NOT NULL DEFAULT 'active',
 google_subject VARCHAR(255) NULL UNIQUE, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS google_accounts (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, user_id BIGINT UNSIGNED NOT NULL, google_email VARCHAR(190) NOT NULL,
 google_subject VARCHAR(255) NOT NULL, display_name VARCHAR(255) NULL,
 access_token_encrypted LONGTEXT NULL, refresh_token_encrypted LONGTEXT NULL, token_expiry DATETIME NULL,
 scopes TEXT NULL, token_updated_at DATETIME NULL, status VARCHAR(40) NOT NULL DEFAULT 'CONNECTED',
 created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL,
 UNIQUE KEY uq_google_subject_user (user_id,google_subject), CONSTRAINT fk_ga_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS youtube_channels (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, google_account_id BIGINT UNSIGNED NOT NULL, youtube_channel_id VARCHAR(100) NOT NULL UNIQUE,
 title VARCHAR(255) NOT NULL, handle VARCHAR(255) NULL, thumbnail_url TEXT NULL, status VARCHAR(40) NOT NULL DEFAULT 'CONNECTED',
 created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, CONSTRAINT fk_yc_ga FOREIGN KEY (google_account_id) REFERENCES google_accounts(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS channel_defaults (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, channel_id BIGINT UNSIGNED NOT NULL UNIQUE,
 title_template TEXT NULL, description_default LONGTEXT NULL, tags_default TEXT NULL, category_id VARCHAR(50) NULL,
 privacy_status VARCHAR(30) NOT NULL DEFAULT 'private', thumbnail_settings JSON NULL, scheduling_settings JSON NULL,
 created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, CONSTRAINT fk_cd_channel FOREIGN KEY(channel_id) REFERENCES youtube_channels(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS drive_folders (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, google_account_id BIGINT UNSIGNED NOT NULL, channel_id BIGINT UNSIGNED NULL,
 folder_type VARCHAR(60) NOT NULL, google_drive_folder_id VARCHAR(255) NOT NULL, folder_name VARCHAR(255) NOT NULL,
 parent_folder_id BIGINT UNSIGNED NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL,
 UNIQUE KEY uq_drive_folder (google_account_id,channel_id,folder_type), CONSTRAINT fk_df_ga FOREIGN KEY(google_account_id) REFERENCES google_accounts(id) ON DELETE CASCADE,
 CONSTRAINT fk_df_channel FOREIGN KEY(channel_id) REFERENCES youtube_channels(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS videos (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, channel_id BIGINT UNSIGNED NOT NULL, google_account_id BIGINT UNSIGNED NULL,
 source_platform VARCHAR(50) NULL, source_url TEXT NULL, source_video_id VARCHAR(255) NULL, source_author VARCHAR(255) NULL,
 source_publish_date DATETIME NULL, original_title TEXT NULL, status VARCHAR(40) NOT NULL DEFAULT 'IMPORTED',
 drive_file_id VARCHAR(255) NULL, drive_file_name VARCHAR(500) NULL, youtube_video_id VARCHAR(100) NULL, youtube_url TEXT NULL,
 scheduled_at DATETIME NULL, imported_at DATETIME NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL,
 UNIQUE KEY uq_source_per_channel (channel_id,source_platform,source_video_id),
 CONSTRAINT fk_v_channel FOREIGN KEY(channel_id) REFERENCES youtube_channels(id) ON DELETE CASCADE,
 CONSTRAINT fk_v_ga FOREIGN KEY(google_account_id) REFERENCES google_accounts(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS video_metadata (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, video_id BIGINT UNSIGNED NOT NULL UNIQUE, title TEXT NOT NULL, description LONGTEXT NULL,
 tags TEXT NULL, category_id VARCHAR(50) NULL, privacy_status VARCHAR(30) NOT NULL DEFAULT 'private',
 thumbnail_drive_file_id VARCHAR(255) NULL, scheduled_date DATE NULL, scheduled_time TIME NULL,
 created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, CONSTRAINT fk_vm_video FOREIGN KEY(video_id) REFERENCES videos(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS upload_jobs (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, video_id BIGINT UNSIGNED NOT NULL, channel_id BIGINT UNSIGNED NOT NULL,
 scheduled_at DATETIME NOT NULL, status VARCHAR(40) NOT NULL DEFAULT 'PENDING', attempts INT NOT NULL DEFAULT 0,
 max_attempts INT NOT NULL DEFAULT 3, locked_at DATETIME NULL, lock_token VARCHAR(100) NULL, last_attempt_at DATETIME NULL,
 error_message LONGTEXT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL,
 KEY idx_due_jobs(status,scheduled_at), CONSTRAINT fk_uj_video FOREIGN KEY(video_id) REFERENCES videos(id) ON DELETE CASCADE,
 CONSTRAINT fk_uj_channel FOREIGN KEY(channel_id) REFERENCES youtube_channels(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS upload_history (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, job_id BIGINT UNSIGNED NULL, video_id BIGINT UNSIGNED NOT NULL, channel_id BIGINT UNSIGNED NOT NULL,
 youtube_video_id VARCHAR(100) NULL, youtube_url TEXT NULL, upload_date DATETIME NULL, publish_date DATETIME NULL,
 status VARCHAR(40) NOT NULL, response_data JSON NULL, error_message LONGTEXT NULL, created_at DATETIME NOT NULL,
 KEY idx_uh_video(video_id), CONSTRAINT fk_uh_job FOREIGN KEY(job_id) REFERENCES upload_jobs(id) ON DELETE SET NULL,
 CONSTRAINT fk_uh_video FOREIGN KEY(video_id) REFERENCES videos(id) ON DELETE CASCADE,
 CONSTRAINT fk_uh_channel FOREIGN KEY(channel_id) REFERENCES youtube_channels(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS analytics_cache (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, channel_id BIGINT UNSIGNED NOT NULL, metric_type VARCHAR(80) NOT NULL,
 metric_date DATE NULL, entity_type VARCHAR(40) NULL, entity_id VARCHAR(255) NULL, payload JSON NOT NULL,
 fetched_at DATETIME NOT NULL, expires_at DATETIME NULL, created_at DATETIME NOT NULL,
 KEY idx_analytics(channel_id,metric_type,metric_date), CONSTRAINT fk_ac_channel FOREIGN KEY(channel_id) REFERENCES youtube_channels(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS system_settings (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, `key` VARCHAR(190) NOT NULL UNIQUE, `value` LONGTEXT NULL,
 created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS cron_logs (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, run_id VARCHAR(100) NOT NULL, task VARCHAR(100) NOT NULL,
 status VARCHAR(40) NOT NULL, message TEXT NULL, context JSON NULL, started_at DATETIME NULL, finished_at DATETIME NULL, created_at DATETIME NOT NULL,
 KEY idx_cron(run_id), KEY idx_cron_task(task)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS password_resets (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, user_id BIGINT UNSIGNED NOT NULL, token_hash VARCHAR(255) NOT NULL,
 expires_at DATETIME NOT NULL, used_at DATETIME NULL, created_at DATETIME NOT NULL,
 CONSTRAINT fk_pr_user FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS module_registry (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, module_key VARCHAR(100) NOT NULL UNIQUE, module_name VARCHAR(190) NOT NULL,
 enabled TINYINT(1) NOT NULL DEFAULT 1, config JSON NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS tiktok_imports (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, channel_id BIGINT UNSIGNED NOT NULL, profile_url TEXT NOT NULL,
 provider_key VARCHAR(100) NOT NULL, options JSON NULL, status VARCHAR(40) NOT NULL DEFAULT 'PENDING',
 created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL,
 CONSTRAINT fk_ti_channel FOREIGN KEY(channel_id) REFERENCES youtube_channels(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS video_duplicates (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, video_id BIGINT UNSIGNED NOT NULL, duplicate_video_id BIGINT UNSIGNED NULL,
 source_platform VARCHAR(50) NULL, source_video_id VARCHAR(255) NULL, detection_reason VARCHAR(255) NOT NULL,
 created_at DATETIME NOT NULL, KEY idx_vd_video(video_id), CONSTRAINT fk_vd_video FOREIGN KEY(video_id) REFERENCES videos(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

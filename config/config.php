<?php
return array (
  'app' => 
  array (
    'name' => 'YT Multi-Channel Automation',
    'base_url' => 'https://hishop.pro',
    'timezone' => 'Asia/Karachi',
    'app_key' => '05664805ba303c6022cb9e006f09dce509dd73fcf0db0f8c0cb027fca84d1a50',
    'cron_secret' => 'f01708ef62ccc6dcc716607f8cdce542eef27f397c062a408810588b6d1a8161',
    'session_name' => 'ytbot_admin',
  ),
  'db' => 
  array (
    'host' => 'localhost',
    'port' => 3306,
    'name' => 'u711616453_ytbot',
    'user' => 'u711616453_ytbot1',
    'pass' => 'Bot415855@',
    'charset' => 'utf8mb4',
  ),
  'google' => 
  array (
    'client_id' => '83765916887-i647cdopugilppn9d2cs7psk1eo2j3ih.apps.googleusercontent.com',
    'client_secret' => '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',
    'redirect_base' => 'https://hishop.pro/oauth/google.php',
    'scopes' => 
    array (
    //   'drive' => 'https://www.googleapis.com/auth/drive.readonly',
    'drive' => 'openid email profile https://www.googleapis.com/auth/drive.readonly',
      'youtube' => 'https://www.googleapis.com/auth/youtube.upload https://www.googleapis.com/auth/youtube.readonly',
    ),
  ),
  'upload' => 
  array (
    'chunk_size' => 8388608,
    'max_retries' => 4,
    'private_upload_lead_minutes' => 10,
    'max_temp_file_bytes' => 0,
    'temp_dir' => '/home/u711616453/domains/hishop.pro/public_html/app/../storage/tmp',
  ),
);

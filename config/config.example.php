<?php
return [
 'app'=>['name'=>'YT Multi-Channel Automation','base_url'=>'https://YOUR-DOMAIN.com','timezone'=>'Asia/Karachi','app_key'=>'REPLACE_WITH_64_HEX_CHARACTER_KEY','cron_secret'=>'REPLACE_WITH_LONG_RANDOM_CRON_SECRET','session_name'=>'ytbot_admin'],
 'db'=>['host'=>'localhost','port'=>3306,'name'=>'DATABASE_NAME','user'=>'DATABASE_USER','pass'=>'DATABASE_PASSWORD','charset'=>'utf8mb4'],
 'google'=>['client_id'=>'GOOGLE_OAUTH_CLIENT_ID','client_secret'=>'GOOGLE_OAUTH_CLIENT_SECRET','redirect_base'=>'https://YOUR-DOMAIN.com/oauth/google.php','scopes'=>['drive'=>'https://www.googleapis.com/auth/drive.readonly','youtube'=>'https://www.googleapis.com/auth/youtube.upload https://www.googleapis.com/auth/youtube.readonly']],
 'upload'=>['chunk_size'=>8*1024*1024,'max_retries'=>4,'private_upload_lead_minutes'=>10,'max_temp_file_bytes'=>0,'temp_dir'=>__DIR__.'/../storage/tmp'],
];

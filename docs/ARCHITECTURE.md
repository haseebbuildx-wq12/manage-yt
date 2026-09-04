# Proposed architecture

## Components

1. PHP front controller/admin UI
2. MySQL relational state
3. Google OAuth service
4. Google Drive REST client
5. YouTube REST client with resumable chunk uploads
6. Scheduler service
7. Cron workers
8. Encrypted token storage

## Database relationships

`youtube_channels 1 -> many drive_folders`
`google_drive_connections 1 -> many drive_folders`
`youtube_channels 1 -> many upload_schedules`
`youtube_channels 1 -> many videos`
`drive_folders 1 -> many videos`
`videos 1 -> many upload_attempts`

`drive_file_id` is globally unique to prevent the same Drive file being inserted twice.

## OAuth flow

Admin -> Google authorization -> callback -> authorization code exchange -> access + refresh token -> AES-256-GCM encrypted database record.

For unattended Cron operation, the refresh token is used to obtain new access tokens.

YouTube OAuth is stored per connected channel. Drive OAuth is stored as a Drive connection.

## Drive flow

Cron scans every enabled folder:
- list direct children
- filter video MIME types
- check `drive_file_id`
- insert unseen files
- copy channel defaults into the video record

## Scheduling flow

Queue worker finds Pending/Retry videos.
For each video it finds the first future enabled schedule slot for that channel that is not already occupied.
Manual schedules are never replaced.
The video is marked Scheduled.

## Upload flow

When scheduled time is due:
- mark Uploading
- refresh channel and Drive tokens if needed
- download Drive object to temporary storage
- initiate YouTube resumable upload
- send 8 MB chunks
- save YouTube ID
- mark Uploaded
- delete temporary file

## Failure flow

- save upload_attempt
- increment retry count
- classify obvious transient HTTP/network failures
- Retry with bounded backoff
- permanent failures remain Failed
- dashboard exposes manual Retry

## Cron isolation

Each worker has a MySQL lock row with an expiry. A locked worker exits cleanly. Errors are handled per folder/video so one failure does not terminate the entire batch.

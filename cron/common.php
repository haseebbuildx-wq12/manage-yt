<?php
declare(strict_types=1);require_once __DIR__.'/../app/bootstrap.php';
function cron_start(string $name,int $seconds=600):array{global $services;$token=$services->acquireLock($name,$seconds);if(!$token){echo "LOCKED $name\n";exit(0);}$services->log('INFO','cron.start','Cron started: '.$name);register_shutdown_function(function()use($name,$token,$services){$services->releaseLock($name,$token);$services->log('INFO','cron.end','Cron ended: '.$name);});return[$name,$token];}

<?php
namespace App\Controllers;

use App\Core\CSRF;
use App\Core\Session;
use App\Core\Database;
use PDO;

final class InstallController {
    private function json(array $data): never { header('Content-Type: application/json'); echo json_encode($data); exit; }
    private function requireCsrf(): void { if (!CSRF::validate()) $this->json(['ok'=>false,'message'=>'Invalid security token.']); }

    public function index(): void {
        if (is_file(BASE_PATH.'/storage/installed.lock')) { header('Location: /login'); exit; }
        require BASE_PATH.'/install/index.php';
    }

    public function requirements(): void {
        $this->requireCsrf();
        $checks = [
            'PHP 8.2+' => version_compare(PHP_VERSION, '8.2.0', '>='),
            'PDO MySQL' => extension_loaded('pdo_mysql'),
            'OpenSSL' => extension_loaded('openssl'),
            'cURL' => extension_loaded('curl'),
            'JSON' => extension_loaded('json'),
            'MBString' => extension_loaded('mbstring'),
            'FileInfo' => extension_loaded('fileinfo'),
            'storage writable' => is_writable(BASE_PATH.'/storage'),
            'config writable' => is_writable(BASE_PATH.'/config'),
        ];
        $this->json(['ok'=>!in_array(false,$checks,true),'checks'=>$checks]);
    }

    public function testDatabase(): void {
        $this->requireCsrf();
        $c = ['host'=>trim($_POST['host']??''),'database'=>trim($_POST['database']??''),'username'=>trim($_POST['username']??''),'password'=>(string)($_POST['password']??'')];
        try {
            Database::connect($c);
            Session::put('install.db', $c);
            $this->json(['ok'=>true,'message'=>'Database connection successful.']);
        } catch (\Throwable $e) { $this->json(['ok'=>false,'message'=>'Database connection failed: '.$e->getMessage()]); }
    }

    public function installDatabase(): void {
        $this->requireCsrf();
        $c = Session::get('install.db');
        if (!$c) $this->json(['ok'=>false,'message'=>'Test database connection first.']);
        try {
            $pdo = Database::connect($c);
            $sql = file_get_contents(BASE_PATH.'/database/schema.sql');
            $parts = preg_split('/;\s*(?:\r?\n|$)/', $sql);
            foreach ($parts as $statement) if (trim($statement)!=='') $pdo->exec($statement);
            $pdo->prepare("INSERT INTO system_settings (`key`,`value`,`created_at`,`updated_at`) VALUES ('schema_version','1.0.0',NOW(),NOW()) ON DUPLICATE KEY UPDATE `value`='1.0.0',updated_at=NOW()")->execute();
            $this->json(['ok'=>true,'message'=>'Complete database schema installed.']);
        } catch (\Throwable $e) { $this->json(['ok'=>false,'message'=>'Database installation failed: '.$e->getMessage()]); }
    }

    public function application(): void {
        $this->requireCsrf();
        Session::put('install.app', ['name'=>trim($_POST['name']??'Multi-Channel Content Manager'),'url'=>rtrim(trim($_POST['url']??''),'/'),'timezone'=>trim($_POST['timezone']??'UTC')]);
        $this->json(['ok'=>true]);
    }

    public function google(): void {
        $this->requireCsrf();
        Session::put('install.google', ['client_id'=>trim($_POST['client_id']??''),'client_secret'=>trim($_POST['client_secret']??'')]);
        $this->json(['ok'=>true]);
    }

    public function admin(): void {
        $this->requireCsrf();
        $name=trim($_POST['name']??''); $email=trim($_POST['email']??''); $password=(string)($_POST['password']??''); $confirm=(string)($_POST['confirm_password']??'');
        if ($name==='' || !filter_var($email,FILTER_VALIDATE_EMAIL) || strlen($password)<8 || $password!==$confirm) $this->json(['ok'=>false,'message'=>'Please provide valid admin details. Password must be 8+ characters and match.']);
        Session::put('install.admin',['name'=>$name,'email'=>$email,'password'=>$password]);
        $this->json(['ok'=>true]);
    }

    public function complete(): void {
        $this->requireCsrf();
        $db=Session::get('install.db'); $app=Session::get('install.app'); $google=Session::get('install.google'); $admin=Session::get('install.admin');
        if (!$db || !$app || !$google || !$admin) $this->json(['ok'=>false,'message'=>'Complete all installation steps first.']);
        try {
            $pdo=Database::connect($db);
            $pdo->beginTransaction();
            $settings=['app_name'=>$app['name'],'app_url'=>$app['url'],'timezone'=>$app['timezone'],'google_client_id'=>$google['client_id'],'google_client_secret'=>$google['client_secret']];
            foreach ($settings as $k=>$v) $pdo->prepare("INSERT INTO system_settings (`key`,`value`,`created_at`,`updated_at`) VALUES (?,?,NOW(),NOW()) ON DUPLICATE KEY UPDATE `value`=VALUES(`value`),updated_at=NOW()")->execute([$k,$v]);
            $pdo->prepare("INSERT INTO users (name,email,password_hash,role,status,created_at,updated_at) VALUES (?,?,?,'admin','active',NOW(),NOW())")->execute([$admin['name'],$admin['email'],password_hash($admin['password'],PASSWORD_DEFAULT)]);
            $pdo->commit();

            $config="<?php\nreturn ".var_export(['db'=>$db,'app'=>$app],true).";\n";
            file_put_contents(BASE_PATH.'/config/installed.php',$config,LOCK_EX);
            file_put_contents(BASE_PATH.'/storage/installed.lock',date('c'),LOCK_EX);
            @chmod(BASE_PATH.'/config/installed.php',0600);
            $this->json(['ok'=>true,'redirect'=>'/login']);
        } catch (\Throwable $e) {
            if (isset($pdo) && $pdo->inTransaction()) $pdo->rollBack();
            $this->json(['ok'=>false,'message'=>'Final installation failed: '.$e->getMessage()]);
        }
    }
}

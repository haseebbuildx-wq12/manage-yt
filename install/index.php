<?php use App\Core\CSRF; $csrf=CSRF::token(); ?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Install</title><link rel="stylesheet" href="/assets/css/app.css"></head>
<body class="center"><main class="installer card wide">
<h1>Multi-Channel Content Manager</h1><p id="subtitle">Production installer</p><div id="status"></div>
<section id="s1"><h2>1. Welcome</h2><p>This installer creates the complete database schema now, so future modules can be added without rebuilding tables.</p><button onclick="next(2)">Start Installation</button></section>
<section id="s2" hidden><h2>2. Server Requirements</h2><div id="checks"></div><button onclick="checkRequirements()">Run Check</button></section>
<section id="s3" hidden><h2>3. Database Setup</h2><input id="dbhost" placeholder="Database Host" value="localhost"><input id="dbname" placeholder="Database Name"><input id="dbuser" placeholder="Database Username"><input id="dbpass" placeholder="Database Password" type="password"><button onclick="testDb()">Test Connection</button><button id="installDbBtn" hidden onclick="installDb()">Install Complete Database</button></section>
<section id="s4" hidden><h2>4. Application Setup</h2><input id="appname" value="Multi-Channel Content Manager" placeholder="Application Name"><input id="appurl" placeholder="Application URL"><input id="timezone" value="UTC" placeholder="Timezone e.g. Asia/Karachi"><button onclick="saveApp()">Continue</button></section>
<section id="s5" hidden><h2>5. Google OAuth Setup</h2><input id="clientid" placeholder="Google Client ID"><input id="clientsecret" placeholder="Google Client Secret" type="password"><p>Redirect URI: <code id="redirectUri"></code></p><button onclick="saveGoogle()">Continue</button></section>
<section id="s6" hidden><h2>6. Admin Account</h2><input id="adminname" placeholder="Name"><input id="adminemail" placeholder="Email" type="email"><input id="adminpass" placeholder="Password (8+ characters)" type="password"><input id="adminconfirm" placeholder="Confirm Password" type="password"><button onclick="saveAdmin()">Continue</button></section>
<section id="s7" hidden><h2>7. Ready</h2><p>All database tables and base configuration are ready.</p><button onclick="completeInstall()">Complete Installation</button></section>
</main>
<script>window.CSRF='<?=htmlspecialchars($csrf,ENT_QUOTES)?>';</script><script src="/assets/js/installer.js"></script></body></html>

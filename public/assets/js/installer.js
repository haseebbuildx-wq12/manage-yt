let current=1;
document.getElementById('redirectUri').textContent=location.origin+'/auth/google/callback';
function next(n){document.getElementById('s'+current).hidden=true;document.getElementById('s'+n).hidden=false;current=n}
function status(m,ok=true){document.getElementById('status').innerHTML='<div class="'+(ok?'ok':'error')+'">'+m+'</div>'}
async function post(url,data={}){data._csrf=window.CSRF;const r=await fetch(url,{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:new URLSearchParams(data)});return r.json()}
async function checkRequirements(){let x=await post('/install/requirements');document.getElementById('checks').innerHTML=Object.entries(x.checks).map(([k,v])=>`<div>${v?'✓':'✗'} ${k}: ${v?'Passed':'Failed'}</div>`).join('');if(x.ok){status('All requirements passed.');setTimeout(()=>next(3),500)}else status('Fix failed requirements before continuing.',false)}
async function testDb(){let x=await post('/install/database/test',{host:dbhost.value,database:dbname.value,username:dbuser.value,password:dbpass.value});status(x.message,x.ok);if(x.ok)installDbBtn.hidden=false}
async function installDb(){let x=await post('/install/database');status(x.message,x.ok);if(x.ok)setTimeout(()=>next(4),500)}
async function saveApp(){let x=await post('/install/application',{name:appname.value,url:appurl.value||location.origin,timezone:timezone.value});if(x.ok)next(5)}
async function saveGoogle(){let x=await post('/install/google',{client_id:clientid.value,client_secret:clientsecret.value});if(x.ok)next(6)}
async function saveAdmin(){let x=await post('/install/admin',{name:adminname.value,email:adminemail.value,password:adminpass.value,confirm_password:adminconfirm.value});if(x.ok)next(7);else status(x.message,false)}
async function completeInstall(){let x=await post('/install/complete');status(x.message||'Installation complete.',x.ok);if(x.ok)location=x.redirect}

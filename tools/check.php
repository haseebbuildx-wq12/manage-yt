<?php
$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__.'/..'));
foreach($rii as $file){
 if(!$file->isFile() || $file->getExtension()!=='php') continue;
 $out=[];$code=0;exec('php -l '.escapeshellarg($file->getPathname()),$out,$code);
 if($code!==0){echo implode("\n",$out)."\n";exit(1);}
}
echo "PHP syntax OK\n";

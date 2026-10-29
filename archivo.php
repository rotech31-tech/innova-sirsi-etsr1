<?php
declare(strict_types=1);
require_once __DIR__.'/includes/init.php';
$t=(string)($_GET['t']??'');
if(!preg_match('/^[a-f0-9]{48}$/',$t)){http_response_code(404);exit;}
$q=db()->prepare('SELECT mime,contenido,tamano FROM archivos WHERE token=:t LIMIT 1');$q->execute(['t'=>$t]);$f=$q->fetch();
if(!$f){http_response_code(404);exit;}
header('Content-Type: '.$f['mime']); header('Content-Length: '.(int)$f['tamano']); header('Cache-Control: public, max-age=31536000, immutable');
$data=$f['contenido']; if(is_resource($data)) fpassthru($data); else echo $data;

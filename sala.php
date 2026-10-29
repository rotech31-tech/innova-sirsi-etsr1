<?php
declare(strict_types=1);
require_once __DIR__.'/includes/init.php';require_role('encargado','docente');
$id=filter_input(INPUT_GET,'id',FILTER_VALIDATE_INT);if(!$id||!can_view_room((int)$id)){http_response_code(404);require __DIR__.'/404.php';exit;}
$p=db();$q=$p->prepare("SELECT * FROM salas WHERE id=:id AND eliminado_en IS NULL");$q->execute(['id'=>$id]);$room=$q->fetch();if(!$room){http_response_code(404);require __DIR__.'/404.php';exit;}
$q=$p->prepare("SELECT * FROM equipos WHERE sala_id=:s AND activo=1 AND eliminado_en IS NULL ORDER BY codigo_inventario");$q->execute(['s'=>$id]);$equipment=$q->fetchAll();
$q=$p->prepare("SELECT ss.*,CONCAT(u.nombre,' ',u.apellido) docente FROM sesiones_sala ss JOIN usuarios u ON u.id=ss.docente_id WHERE ss.sala_id=:s ORDER BY ss.checkin_en DESC LIMIT 8");$q->execute(['s'=>$id]);$sessions=$q->fetchAll();
$q=$p->prepare("SELECT t.id,t.tipo_problema,t.estado,t.urgencia,t.creado_en,COALESCE(e.codigo_inventario,'Sin equipo') equipo FROM tickets t LEFT JOIN equipos e ON e.id=t.equipo_id WHERE t.sala_id=:s AND t.estado IN('abierto','en_revision','en_proceso') ORDER BY FIELD(t.urgencia,'critica','alta','media','baja'),t.creado_en DESC");$q->execute(['s'=>$id]);$tickets=$q->fetchAll();
$q=$p->prepare("SELECT ss.checkin_en,CONCAT(u.nombre,' ',u.apellido) docente FROM sesiones_sala ss JOIN usuarios u ON u.id=ss.docente_id WHERE ss.sala_id=:s AND ss.checkout_en IS NULL ORDER BY ss.checkin_en DESC LIMIT 1");$q->execute(['s'=>$id]);$live=$q->fetch();
$pageTitle='Ficha de '.$room['nombre'];$st=room_status_info((int)$id);require __DIR__.'/includes/header.php';
?>
<section class="room-profile-head card">
 <div class="card-body room-profile-head__inner"><div><p class="eyebrow">Sala institucional</p><h2><?=e($room['nombre'])?></h2><p><?=e($room['ubicacion']??'Ubicación no registrada')?><?=$room['capacidad']?' · Capacidad '.(int)$room['capacidad'].' personas':''?></p></div><div class="room-profile-state"><span class="badge badge-<?=e($st['class'])?>"><?=e($st['label'])?></span><?php if($live):?><small><?=e($live['docente'])?> · desde <?=e(format_datetime($live['checkin_en']))?></small><?php endif;?></div></div>
</section>
<section class="grid grid-3 mt-3">
 <article class="metric-panel"><span>Equipos activos</span><strong><?=count($equipment)?></strong></article>
 <article class="metric-panel"><span>Incidencias abiertas</span><strong><?=count($tickets)?></strong></article>
 <article class="metric-panel"><span>Últimos usos visibles</span><strong><?=count($sessions)?></strong></article>
</section>
<section class="grid grid-2 mt-3">
 <article class="card"><div class="card-header"><h2>Inventario de la sala</h2><a class="btn btn-secondary btn-small" href="<?=e(url('mapa_salas.php?sala_id='.(int)$id))?>">Ver mapa</a></div><div class="table-wrap"><?php if(!$equipment):?><div class="empty-state">No hay equipos registrados.</div><?php else:?><table><thead><tr><th>Código</th><th>Tipo</th><th>Marca</th><th>Sistema</th><th>Estado</th></tr></thead><tbody><?php foreach($equipment as $e1):?><tr><td><strong><?=e($e1['codigo_inventario'])?></strong></td><td><?=e(equipment_type_label($e1['tipo']))?></td><td><?=e($e1['marca']??'-')?></td><td><?=e($e1['sistema_operativo']??'-')?></td><td><?=status_badge($e1['estado'])?></td></tr><?php endforeach;?></tbody></table><?php endif;?></div></article>
 <article class="card"><div class="card-header"><h2>Incidencias abiertas</h2></div><div class="card-body"><?php if(!$tickets):?><div class="empty-state">No hay incidencias abiertas en esta sala.</div><?php else:?><ul class="activity-list"><?php foreach($tickets as $t):?><li><div><strong>#<?=(int)$t['id']?> · <?=e(ucwords(str_replace('_',' ',$t['tipo_problema'])))?></strong><span><?=e($t['equipo'])?> · <?=e(format_datetime($t['creado_en']))?></span></div><div><?=status_badge($t['urgencia'])?> <?=status_badge($t['estado'])?></div></li><?php endforeach;?></ul><?php endif;?></div></article>
</section>
<section class="card mt-3"><div class="card-header"><h2>Historial reciente de uso</h2></div><div class="table-wrap"><?php if(!$sessions):?><div class="empty-state">Todavía no hay registros de uso.</div><?php else:?><table><thead><tr><th>Docente</th><th>Ingreso</th><th>Salida</th><th>Observaciones</th></tr></thead><tbody><?php foreach($sessions as $ss):?><tr><td><?=e($ss['docente'])?></td><td><?=e(format_datetime($ss['checkin_en']))?></td><td><?=e(format_datetime($ss['checkout_en']))?></td><td><?=e($ss['observaciones_checkout']?:$ss['observaciones_checkin']?:'-')?></td></tr><?php endforeach;?></tbody></table><?php endif;?></div></section>
<?php require __DIR__.'/includes/footer.php';?>

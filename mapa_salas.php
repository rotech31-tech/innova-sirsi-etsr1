<?php
declare(strict_types=1);
require_once __DIR__.'/includes/init.php';
require_role('encargado','docente');
$pageTitle='Mapa interactivo de salas';
$p=db();
if(is_docente()){
  $q=$p->prepare("SELECT DISTINCT s.id,s.nombre,s.ubicacion FROM salas s JOIN asignaciones_salas a ON a.sala_id=s.id WHERE a.docente_id=:d AND a.anio_lectivo=YEAR(CURDATE()) AND a.activa=1 AND s.activa=1 AND s.eliminado_en IS NULL ORDER BY s.nombre");
  $q->execute(['d'=>(int)current_user()['id']]);$rooms=$q->fetchAll();
}else{$rooms=$p->query("SELECT id,nombre,ubicacion FROM salas WHERE activa=1 AND eliminado_en IS NULL ORDER BY nombre")->fetchAll();}
$selected=filter_input(INPUT_GET,'sala_id',FILTER_VALIDATE_INT) ?: (int)($rooms[0]['id']??0);
$allowed=array_map(fn($r)=>(int)$r['id'],$rooms);if($selected&&!in_array($selected,$allowed,true))$selected=(int)($rooms[0]['id']??0);
$room=null;$equipment=[];$openSession=null;
if($selected){
  $q=$p->prepare("SELECT * FROM salas WHERE id=:id AND activa=1 AND eliminado_en IS NULL");$q->execute(['id'=>$selected]);$room=$q->fetch();
  $q=$p->prepare("SELECT id,codigo_inventario,tipo,marca,sistema_operativo,estado,observaciones FROM equipos WHERE sala_id=:s AND activo=1 AND eliminado_en IS NULL ORDER BY FIELD(tipo,'pc','torre','monitor','switch','router','impresora','access_point'),codigo_inventario");$q->execute(['s'=>$selected]);$equipment=$q->fetchAll();
  $q=$p->prepare("SELECT ss.checkin_en,CONCAT(u.nombre,' ',u.apellido) docente FROM sesiones_sala ss JOIN usuarios u ON u.id=ss.docente_id WHERE ss.sala_id=:s AND ss.checkout_en IS NULL ORDER BY ss.checkin_en DESC LIMIT 1");$q->execute(['s'=>$selected]);$openSession=$q->fetch();
}
require __DIR__.'/includes/header.php';
?>
<section class="map-toolbar card">
  <div class="card-body map-toolbar__inner">
    <div>
      <p class="eyebrow">Vista operativa</p>
      <h2>Mapa interactivo de equipamiento</h2>
      <p class="text-muted">La vista se actualiza automáticamente según los equipos registrados en cada sala.</p>
    </div>
    <form method="get" class="inline-filter">
      <div class="form-group"><label for="sala_id">Sala</label><select id="sala_id" name="sala_id" onchange="this.form.submit()">
      <?php foreach($rooms as $r):?><option value="<?=(int)$r['id']?>" <?=$selected===(int)$r['id']?'selected':''?>><?=e($r['nombre'])?></option><?php endforeach;?>
      </select></div>
    </form>
  </div>
</section>

<?php if(!$room):?>
<div class="empty-state mt-3">No hay salas disponibles para mostrar.</div>
<?php else: $status=room_status_info((int)$room['id']);?>
<section class="room-map-layout mt-3">
  <article class="card room-map-card">
    <div class="card-header">
      <div><h2><?=e($room['nombre'])?></h2><p class="meta mt-0"><?=e($room['ubicacion']??'Ubicación no registrada')?> · <?=count($equipment)?> equipos</p></div>
      <span class="badge badge-<?=e($status['class'])?>"><?=e($status['label'])?></span>
    </div>
    <div class="card-body">
      <?php if($openSession):?><div class="room-live-note"><strong>Sala en uso</strong><span><?=e($openSession['docente'])?> · desde <?=e(format_datetime($openSession['checkin_en']))?></span></div><?php endif;?>
      <div class="room-isometric" aria-label="Plano interactivo de <?=e($room['nombre'])?>">
        <div class="room-wall room-wall--back"><span><?=e($room['nombre'])?></span></div>
        <div class="room-board">PIZARRA</div>
        <div class="room-floor">
          <?php if(!$equipment):?>
            <div class="room-map-empty">Esta sala todavía no tiene equipos registrados.</div>
          <?php else:?>
            <?php foreach($equipment as $i=>$eq):
              $data=[
                'codigo'=>$eq['codigo_inventario'],'tipo'=>equipment_type_label($eq['tipo']),
                'marca'=>$eq['marca']?:'-','so'=>$eq['sistema_operativo']?:'-',
                'estado'=>ucwords(str_replace('_',' ',$eq['estado'])),'observaciones'=>$eq['observaciones']?:'Sin observaciones'
              ];
            ?>
            <button type="button" class="map-station map-station--<?=e($eq['estado'])?>" data-map-equipment='<?=e(json_encode($data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES))?>' aria-label="Ver <?=e($eq['codigo_inventario'])?>">
              <span class="station-screen"></span><span class="station-base"></span><strong><?=e($eq['codigo_inventario'])?></strong><small><?=e(equipment_type_label($eq['tipo']))?></small>
            </button>
            <?php endforeach;?>
          <?php endif;?>
        </div>
      </div>
    </div>
  </article>

  <aside class="card map-detail-card" data-map-detail>
    <div class="card-header"><h2>Detalle del equipo</h2></div>
    <div class="card-body">
      <div class="map-detail-empty">Seleccioná un equipo del mapa para ver sus especificaciones.</div>
      <div class="map-detail-content" hidden>
        <div class="equipment-detail-code" data-field="codigo"></div>
        <dl class="detail-list">
          <div><dt>Tipo</dt><dd data-field="tipo"></dd></div>
          <div><dt>Marca</dt><dd data-field="marca"></dd></div>
          <div><dt>Sistema</dt><dd data-field="so"></dd></div>
          <div><dt>Estado</dt><dd data-field="estado"></dd></div>
          <div><dt>Observaciones</dt><dd data-field="observaciones"></dd></div>
        </dl>
      </div>
      <div class="form-actions mt-3"><a class="btn btn-secondary" href="<?=e(url('sala.php?id='.(int)$room['id']))?>">Abrir ficha completa de la sala</a></div>
    </div>
  </aside>
</section>
<?php endif;?>
<?php require __DIR__.'/includes/footer.php';?>

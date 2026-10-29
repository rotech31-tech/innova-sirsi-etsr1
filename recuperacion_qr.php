<?php
declare(strict_types=1);
require_once __DIR__.'/includes/init.php';
$pageTitle='QR de recuperación de contraseña';
$qr=absolute_url('recuperar_password.php');
require __DIR__.'/includes/header.php';
?>
<section class="card qr-card public-qr-card">
  <div class="card-body">
    <p class="eyebrow"><?=e(COMPANY_NAME)?></p>
    <h2>Recuperación de contraseña</h2>
    <p>Escanea este código para abrir el formulario de recuperación del <?=e(APP_NAME)?>.</p>

    <div id="qrcode" data-qr-text="<?=e($qr)?>"><p>Generando QR...</p></div>

    <p class="form-help">El QR no contiene cédulas, contraseñas ni tokens personales. Solo abre el formulario seguro de recuperación.</p>
    <p class="meta"><?=e($qr)?></p>

    <?php if(str_contains(APP_ORIGIN,'localhost')): ?>
      <div class="alert alert-warning text-left">
        Para escanear este QR desde otro dispositivo, cambia <strong>APP_ORIGIN</strong> en <strong>includes/config.php</strong> por la dirección de red o dominio desde el que se pueda acceder al sistema.
      </div>
    <?php endif; ?>

    <div class="form-actions no-print" style="justify-content:center">
      <button class="btn btn-primary" onclick="window.print()" type="button">Imprimir QR</button>
      <a class="btn btn-secondary" href="<?=e(url('login.php'))?>">Volver al login</a>
    </div>
  </div>
</section>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js" integrity="sha512-CNgIRecGo7nphbeZ04Sc13ka07paqdeTu0WR1IM4kNcpmBAUSHSQX0FslNhTDadL4O5SAGapGt4FodqL8My0mA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
document.addEventListener('DOMContentLoaded',()=>{
  const c=document.getElementById('qrcode'),t=c.dataset.qrText;
  c.innerHTML='';
  if(typeof QRCode==='undefined'){
    c.innerHTML='<p>No se pudo cargar el generador QR. Revisa la conexión a Internet.</p>';
    return;
  }
  new QRCode(c,{text:t,width:250,height:250,correctLevel:QRCode.CorrectLevel.H});
});
</script>
<?php require __DIR__.'/includes/footer.php'; ?>

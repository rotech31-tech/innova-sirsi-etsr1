<?php
declare(strict_types=1);
require_once __DIR__.'/includes/init.php';
if(is_logged_in()) redirect('dashboard.php');
$pageTitle='Iniciar sesión';
require __DIR__.'/includes/header.php';
?>
<section class="auth-card auth-card-institutional">
  <div class="auth-institutional-header">
    <div class="institution-seal" aria-hidden="true">
      <strong>ETSR</strong>
      <span>UTU</span>
    </div>
    <div>
      <p class="eyebrow">Escuela Técnica Superior de Rivera</p>
      <h1>Gestión de salas de informática</h1>
      <p class="auth-subtitle">Sistema institucional para organizar horarios, inventario, uso de salas y soporte técnico.</p>
    </div>
  </div>

  <div class="auth-form">
    <div class="auth-form-heading">
      <span class="auth-section-label">ACCESO INSTITUCIONAL</span>
      <h2>Ingresar al sistema</h2>
      <p class="text-muted">Utilizá el correo registrado y tu contraseña.</p>
    </div>

    <form action="<?=e(url('php/login_process.php'))?>" method="post">
      <?=csrf_field()?>
      <div class="form-group">
        <label for="email">Correo electrónico</label>
        <input id="email" name="email" type="email" maxlength="150" required autocomplete="username" placeholder="usuario@correo.com">
      </div>
      <div class="form-group mt-2">
        <label for="password">Contraseña</label>
        <input id="password" name="password" type="password" minlength="8" required autocomplete="current-password" placeholder="Contraseña">
      </div>
      <div class="form-actions auth-submit-row">
        <button class="btn btn-primary">Ingresar</button>
      </div>
    </form>

    <div class="auth-links">
      <a href="<?=e(url('recuperar_password.php'))?>">¿Olvidaste tu contraseña?</a>
      <a href="<?=e(url('recuperacion_qr.php'))?>">Recuperación mediante QR</a>
    </div>

    <div class="auth-support">
      <strong>Acceso restringido</strong>
      <span>El sistema está destinado a profesores y Encargados de Sala autorizados.</span>
    </div>
  </div>

  <div class="auth-footer-note">
    <span><?=e(COMPANY_NAME)?> · Desarrollo del sistema</span>
    <span>ETSR — UTU · Rivera, Uruguay</span>
  </div>
</section>
<?php require __DIR__.'/includes/footer.php'; ?>

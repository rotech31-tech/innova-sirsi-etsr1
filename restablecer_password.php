<?php
declare(strict_types=1);
require_once __DIR__.'/includes/init.php';

if(is_logged_in()) {
    redirect('dashboard.php');
}

$token=(string)($_GET['token'] ?? $_POST['token'] ?? '');
$reset=find_valid_password_reset($token);
$pageTitle='Crear nueva contraseña';

if(is_post()){
    verify_csrf();

    if(!$reset){
        flash('danger','El enlace de recuperación ya no es válido o ha vencido.');
        redirect('recuperar_password.php');
    }

    $password=(string)($_POST['password'] ?? '');
    $confirm=(string)($_POST['password_confirm'] ?? '');

    if(strlen($password)<8 || !preg_match('/[A-Za-zÁÉÍÓÚáéíóúÑñ]/u',$password) || !preg_match('/\d/',$password)){
        flash('danger','La nueva contraseña debe tener al menos 8 caracteres e incluir letras y números.');
        header('Location: '.url('restablecer_password.php?token='.urlencode($token)));
        exit;
    }
    if(!hash_equals($password,$confirm)){
        flash('danger','Las contraseñas no coinciden.');
        header('Location: '.url('restablecer_password.php?token='.urlencode($token)));
        exit;
    }

    try{
        $userId=complete_password_reset($token,$password);
        if(!$userId){
            flash('danger','El enlace de recuperación ya no es válido o ha vencido.');
            redirect('recuperar_password.php');
        }
        log_activity('Contraseña restablecida','usuarios',$userId,'Recuperación mediante enlace temporal');
        flash('success','Contraseña actualizada correctamente. Ya puedes iniciar sesión.');
        redirect('login.php');
    }catch(Throwable $e){
        error_log('[INNOVA RESET] '.$e->getMessage());
        flash('danger','No se pudo actualizar la contraseña. Intenta nuevamente.');
        header('Location: '.url('restablecer_password.php?token='.urlencode($token)));
        exit;
    }
}

require __DIR__.'/includes/header.php';
?>
<section class="auth-card">
  <div class="auth-hero">
    <div class="logo-box">INNOVA SRL</div>
    <p class="eyebrow" style="color:#7fd5f3"><?=e(COMPANY_NAME)?></p>
    <h1>Nueva contraseña</h1>
    <p>El enlace de recuperación es personal, temporal y de un solo uso.</p>
    <small><?=e(APP_NAME)?></small>
  </div>

  <div class="auth-form">
    <?php if(!$reset): ?>
      <div class="alert alert-danger">Este enlace no es válido, ya fue utilizado o venció.</div>
      <div class="form-actions">
        <a class="btn btn-primary" href="<?=e(url('recuperar_password.php'))?>">Solicitar otro enlace</a>
        <a class="btn btn-secondary" href="<?=e(url('login.php'))?>">Volver al login</a>
      </div>
    <?php else: ?>
      <h2>Hola, <?=e($reset['nombre'])?></h2>
      <p class="text-muted">Crea una contraseña nueva para tu cuenta. Debe tener al menos 8 caracteres e incluir letras y números.</p>

      <form method="post">
        <?=csrf_field()?>
        <input type="hidden" name="token" value="<?=e($token)?>">
        <div class="form-group">
          <label for="password">Nueva contraseña</label>
          <input id="password" name="password" type="password" minlength="8" required autocomplete="new-password">
        </div>
        <div class="form-group mt-2">
          <label for="password_confirm">Confirmar contraseña</label>
          <input id="password_confirm" name="password_confirm" type="password" minlength="8" required autocomplete="new-password">
        </div>
        <div class="form-actions">
          <button class="btn btn-primary">Guardar nueva contraseña</button>
        </div>
      </form>
    <?php endif; ?>
  </div>
</section>
<?php require __DIR__.'/includes/footer.php'; ?>

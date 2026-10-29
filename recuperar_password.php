<?php
declare(strict_types=1);
require_once __DIR__.'/includes/init.php';

if(is_logged_in()) {
    redirect('dashboard.php');
}

$pageTitle='Recuperar contraseña';
$submitted=false;

if(is_post()){
    verify_csrf();
    $cedula=normalize_cedula(input_string('cedula',30));
    $email=strtolower(input_string('email',150));

    // Respuesta deliberadamente genérica para no revelar qué usuarios existen.
    $submitted=true;

    if(valid_cedula($cedula) && filter_var($email,FILTER_VALIDATE_EMAIL)){
        $s=db()->prepare(
            "SELECT id,nombre,apellido,email,rol,activo
             FROM usuarios
             WHERE cedula=:c AND email=:e AND activo=1 AND eliminado_en IS NULL
               AND EXISTS (SELECT 1 FROM usuario_turnos ut WHERE ut.usuario_id=usuarios.id AND ut.activo=1 AND ut.eliminado_en IS NULL)
             LIMIT 1"
        );
        $s->execute(['c'=>$cedula,'e'=>$email]);
        $user=$s->fetch();

        if($user && !password_reset_rate_limited((int)$user['id'])){
            try{
                $reset=create_password_reset((int)$user['id'],null);
                $sent=send_password_reset_email($user,$reset['token'],$reset['expires_minutes']);
                if($sent){
                    log_activity('Recuperación de contraseña solicitada','usuarios',(int)$user['id'],'Solicitud desde login');
                }else{
                    error_log('[INNOVA RESET] No se pudo enviar recuperación al usuario ID '.(int)$user['id']);
                }
            }catch(Throwable $e){
                error_log('[INNOVA RESET] '.$e->getMessage());
            }
        }
    }
}

require __DIR__.'/includes/header.php';
?>
<section class="auth-card">
  <div class="auth-hero">
    <div class="logo-box">INNOVA SRL</div>
    <p class="eyebrow" style="color:#7fd5f3"><?=e(COMPANY_NAME)?></p>
    <h1>Recuperación de contraseña</h1>
    <p>Restablece el acceso a tu cuenta utilizando tus datos registrados en el sistema.</p>
    <small><?=e(APP_NAME)?></small>
  </div>

  <div class="auth-form">
    <?php if($submitted): ?>
      <div class="alert alert-success">
        Si la cédula y el correo corresponden a una cuenta activa, recibirás un enlace de recuperación. Revisa también la carpeta de correo no deseado.
      </div>
      <p class="text-muted">El enlace es temporal y solo puede utilizarse una vez.</p>
      <div class="form-actions">
        <a class="btn btn-primary" href="<?=e(url('login.php'))?>">Volver al login</a>
        <a class="btn btn-secondary" href="<?=e(url('recuperar_password.php'))?>">Intentar nuevamente</a>
      </div>
    <?php else: ?>
      <h2>Identificar mi cuenta</h2>
      <p class="text-muted">Ingresa la misma cédula y el mismo correo que fueron registrados por el Encargado de Sala.</p>

      <form method="post">
        <?=csrf_field()?>
        <div class="form-group">
          <label for="cedula">Cédula</label>
          <input id="cedula" name="cedula" inputmode="numeric" maxlength="20" data-ci-uy required placeholder="Ej.: 1.234.567-2" autocomplete="off"><span class="form-help" data-ci-help>La cédula debe tener un dígito verificador válido.</span>
          <span class="form-help">Puedes escribirla con o sin puntos y guion.</span>
        </div>
        <div class="form-group mt-2">
          <label for="email">Correo electrónico</label>
          <input id="email" name="email" type="email" maxlength="150" required autocomplete="email">
        </div>
        <div class="form-actions">
          <button class="btn btn-primary">Solicitar recuperación</button>
          <a class="btn btn-secondary" href="<?=e(url('login.php'))?>">Cancelar</a>
        </div>
      </form>

      <div class="security-note mt-3">
        <strong>Seguridad</strong>
        <p>El sistema nunca muestra si una cédula o un correo están registrados. Si los datos coinciden, el enlace llegará únicamente al correo asociado a la cuenta.</p>
      </div>
    <?php endif; ?>
  </div>
</section>
<?php require __DIR__.'/includes/footer.php'; ?>

<?php
declare(strict_types=1);
require_once __DIR__.'/includes/init.php';

$count=(int)db()->query("SELECT COUNT(*) FROM usuarios WHERE rol='encargado' AND eliminado_en IS NULL")->fetchColumn();
$turnos=db()->query("SELECT id,nombre FROM turnos WHERE activo=1 ORDER BY id")->fetchAll();

if(is_post()){
    verify_csrf();
    if($count>0) redirect('login.php');

    $n=input_string('nombre',80);
    $a=input_string('apellido',80);
    $c=normalize_cedula(input_string('cedula',30));
    $e=strtolower(input_string('email',150));
    $p=(string)($_POST['password']??'');
    $turno=input_int('turno_id');

    $validTurn=false;
    foreach($turnos as $t){if((int)$t['id']===$turno){$validTurn=true;break;}}
    if($n===''||$a===''||!valid_cedula($c)||!filter_var($e,FILTER_VALIDATE_EMAIL)||strlen($p)<8||!$validTurn){
        flash('danger','Completa los datos, selecciona un turno, ingresa una cédula uruguaya con dígito verificador válido y usa una contraseña de al menos 8 caracteres.');
    }else{
        $pdo=db();
        try{
            $pdo->beginTransaction();
            $s=$pdo->prepare("INSERT INTO usuarios(nombre,apellido,cedula,email,password_hash,rol,activo) VALUES(:n,:a,:c,:e,:p,'encargado',1) RETURNING id");
            $s->execute(['n'=>$n,'a'=>$a,'c'=>$c,'e'=>$e,'p'=>password_hash($p,PASSWORD_BCRYPT)]);
            $id=(int)$s->fetchColumn();
            $s=$pdo->prepare('INSERT INTO usuario_turnos(usuario_id,turno_id,activo) VALUES(:u,:t,1)');
            $s->execute(['u'=>$id,'t'=>$turno]);
            $pdo->commit();
            flash('success','Encargado creado para el turno '.turn_name((int)$turno).'. Elimina setup_admin.php e inicia sesión.');
            redirect('login.php');
        }catch(PDOException $x){
            if($pdo->inTransaction())$pdo->rollBack();
            flash('danger','No se pudo crear el encargado. Revisa que la cédula y el correo no estén repetidos.');
        }
    }
}

$pageTitle='Configuración inicial';
require __DIR__.'/includes/header.php';
?>
<section class="auth-card">
  <div class="auth-hero">
    <div class="logo-box">INNOVA SRL</div>
    <h1>Crear Encargado de Sala</h1>
    <p>Formulario de una sola utilización para la instalación inicial.</p>
  </div>
  <div class="auth-form">
    <?php if($count>0): ?>
      <div class="alert alert-warning">Ya existe un encargado. Elimina este archivo.</div>
      <a class="btn btn-primary" href="<?=e(url('login.php'))?>">Ir al login</a>
    <?php else: ?>
      <form method="post">
        <?=csrf_field()?>
        <div class="grid grid-2">
          <div class="form-group"><label>Nombre</label><input name="nombre" required></div>
          <div class="form-group"><label>Apellido</label><input name="apellido" required></div>
        </div>
        <div class="form-group mt-2"><label>Cédula</label><input name="cedula" required maxlength="20" inputmode="numeric" data-ci-uy placeholder="Ej.: 1.234.567-2"><span class="form-help" data-ci-help>Se valida automáticamente el dígito verificador uruguayo.</span></div>
        <div class="form-group mt-2"><label>Correo</label><input name="email" type="email" required></div>
        <div class="form-group mt-2"><label>Turno</label><select name="turno_id" required><option value="">Seleccionar...</option><?php foreach($turnos as $t):?><option value="<?=(int)$t['id']?>"><?=e($t['nombre'])?></option><?php endforeach;?></select></div>
        <div class="form-group mt-2"><label>Contraseña</label><input name="password" type="password" minlength="8" required></div>
        <div class="form-actions"><button class="btn btn-primary">Crear encargado</button></div>
      </form>
    <?php endif; ?>
  </div>
</section>
<?php require __DIR__.'/includes/footer.php';?>

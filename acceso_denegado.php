<?php
declare(strict_types=1);require_once __DIR__.'/includes/init.php';require_login();http_response_code(403);$pageTitle='Acceso restringido';require __DIR__.'/includes/header.php';?>
<section class="system-error"><span class="system-error__code">403</span><h1>Esta sección no corresponde a tu perfil</h1><p>Tu sesión está activa, pero no tenés permisos para acceder a este recurso.</p><div class="form-actions"><a class="btn btn-primary" href="<?=e(url('dashboard.php'))?>">Volver al panel</a></div></section>
<?php require __DIR__.'/includes/footer.php';?>

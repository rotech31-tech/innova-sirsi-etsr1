<?php
declare(strict_types=1);if(!function_exists('e'))require_once __DIR__.'/includes/init.php';http_response_code(404);$pageTitle='Página no encontrada';require __DIR__.'/includes/header.php';?>
<section class="system-error"><span class="system-error__code">404</span><h1>No encontramos esa página</h1><p>La dirección puede haber cambiado o el recurso ya no está disponible.</p><div class="form-actions"><a class="btn btn-primary" href="<?=e(is_logged_in()?url('dashboard.php'):url('login.php'))?>">Volver al inicio</a></div></section>
<?php require __DIR__.'/includes/footer.php';?>

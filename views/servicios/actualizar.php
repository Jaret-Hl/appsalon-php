<h1 class="nombre-pagina">Actualizar Servicio</h1>
<p class="descripcion-pagina">Módifica los valores del formulario</p>

<?php
    include_once __DIR__ . '/../templates/barra.php';
?>

<form method="POST" class="formulario">

    <?php include_once __DIR__ . '/formulario.php'; ?>

    <input type="submit" class="boton" value="Guardar Servicio">
</form>
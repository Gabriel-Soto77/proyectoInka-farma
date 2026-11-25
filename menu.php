<?php
// menu.php: barra lateral izquierda + barra superior (se incluye dentro del <body>)
$paginaActual = $paginaActual ?? "";              // Página activa para resaltar el menú
$tituloPagina = $tituloPagina ?? "Inkafarma";     // Título que se muestra arriba
?>
<div class="d-lg-flex">

    <!-- BARRA LATERAL -->
    <aside class="barra-lateral">
        <div class="marca-lateral d-flex align-items-center justify-content-between">
            <a href="catalogo.php" class="d-flex align-items-center text-decoration-none">
                <img src="imagenes/logo_circulo.png" class="logo-circulo" alt="Inkafarma">
                <div class="ms-2">
                    <span class="marca-nombre">Inkafarma</span>
                    <small class="marca-sede">Sede Chaclacayo</small>
                </div>
            </a>
            <button class="btn btn-outline-light btn-sm d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#menuLateral">Menú</button>
        </div>

        <div class="collapse d-lg-block" id="menuLateral">
            <p class="titulo-menu">Portal</p>
            <nav class="nav flex-column">
                <a class="enlace-menu <?php if ($paginaActual == 'catalogo') { echo 'activo'; } ?>" href="catalogo.php"><span class="icono">🏠</span> Inicio</a>
                <a class="enlace-menu <?php if ($paginaActual == 'equipo') { echo 'activo'; } ?>" href="catalogo.php#equipo"><span class="icono">👥</span> Nuestro equipo</a>
                <a class="enlace-menu" href="catalogo.php#medicamentos"><span class="icono">💊</span> Medicamentos</a>
            </nav>

            <?php if (isset($_SESSION['usuario_id'])) { ?>
                <p class="titulo-menu">Administración</p>
                <nav class="nav flex-column">
                    <a class="enlace-menu <?php if ($paginaActual == 'productos') { echo 'activo'; } ?>" href="index.php"><span class="icono">📦</span> Productos</a>
                    <a class="enlace-menu <?php if ($paginaActual == 'agregar') { echo 'activo'; } ?>" href="agregar.php"><span class="icono">➕</span> Agregar producto</a>
                    <a class="enlace-menu <?php if ($paginaActual == 'reportes') { echo 'activo'; } ?>" href="reportes.php"><span class="icono">📊</span> Reportes</a>
                </nav>

                <div class="usuario-lateral">
                    <small class="d-block text-white-50">Sesión iniciada</small>
                    <strong><?php echo htmlspecialchars($_SESSION['nombre']); ?></strong>
                    <a href="logout.php" class="btn btn-sm btn-danger w-100 mt-2">Cerrar sesión</a>
                </div>
            <?php } else { ?>
                <div class="usuario-lateral">
                    <small class="d-block text-white-50 mb-2">Acceso para el personal</small>
                    <a href="login.php" class="btn btn-sm btn-warning fw-semibold w-100">Ingresar</a>
                </div>
            <?php } ?>
        </div>
    </aside>

    <!-- CONTENIDO PRINCIPAL -->
    <main class="contenido flex-grow-1">
        <div class="barra-superior">
            <h5><?php echo $tituloPagina; ?></h5>
            <div class="d-flex align-items-center gap-3">
                <small class="text-muted d-none d-md-inline">Hoy: <?php echo date("d/m/Y H:i"); ?></small>
                <?php if (isset($_SESSION['usuario_id'])) { ?>
                    <span class="chip-usuario"><span class="mini-avatar"><?php echo strtoupper(substr($_SESSION['nombre'], 0, 1)); ?></span><?php echo htmlspecialchars($_SESSION['nombre']); ?></span>
                <?php } else { ?>
                    <a href="login.php" class="btn btn-sm btn-verde">Ingresar</a>
                <?php } ?>
            </div>
        </div>
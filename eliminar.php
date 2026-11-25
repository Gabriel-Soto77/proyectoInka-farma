<?php
include("conexion.php");
verificarSesion();

// Solo se elimina por POST (desde el formulario de index.php), nunca por un enlace GET
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header("Location: index.php");
    exit;
}

$id = (int)($_POST['id'] ?? 0);                   // ID que llega por el formulario

if ($id > 0) {
    try {
        // 1. Buscar la foto del producto
        $stmt = $conexion->prepare("SELECT foto FROM insumos_medicos WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $fila = $stmt->fetch();

        if ($fila) {
            // 2. Borrar la foto del servidor
            if ($fila['foto'] != null && file_exists("uploads/" . $fila['foto'])) {
                unlink("uploads/" . $fila['foto']);
            }
            // 3. Borrar el registro de la base de datos
            $stmt = $conexion->prepare("DELETE FROM insumos_medicos WHERE id = :id");
            $stmt->execute([':id' => $id]);
        }
        header("Location: index.php?msg=eliminado");
        exit;
    } catch (PDOException $e) {
        error_log("eliminar.php: " . $e->getMessage());
        die("No se pudo eliminar el producto. Intente nuevamente.");
    }
}

// Si no llegó un ID válido, volver al listado
header("Location: index.php");
exit;
?>

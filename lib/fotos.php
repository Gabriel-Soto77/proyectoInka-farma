<?php
// lib/fotos.php: subida segura de la foto de un producto (la usan agregar.php y editar.php)
// Devuelve el nombre del archivo guardado, o null si no se eligió foto o hubo un error.
// Si hubo un error, deja el mensaje en $error (por referencia).

function subirFoto($archivo, &$error) {
    // Sin archivo elegido (código 4): la foto es opcional
    if ($archivo === null || $archivo['error'] == 4) {
        return null;
    }
    if ($archivo['error'] != 0) {
        $error = "No se pudo subir la foto.";
        return null;
    }
    if ($archivo['size'] > 2 * 1024 * 1024) {
        $error = "La foto no debe pesar más de 2 MB.";
        return null;
    }

    // Se valida la extensión Y el contenido real del archivo (tipo MIME)
    $permitidas = array(
        "jpg"  => "image/jpeg",
        "jpeg" => "image/jpeg",
        "png"  => "image/png",
        "gif"  => "image/gif",
        "webp" => "image/webp"
    );
    $ext  = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
    $info = @getimagesize($archivo['tmp_name']);       // false si no es una imagen real

    if (!isset($permitidas[$ext]) || $info === false || $info['mime'] != $permitidas[$ext]) {
        $error = "La foto debe ser una imagen real JPG, PNG, GIF o WEBP.";
        return null;
    }

    $nombre = uniqid() . "." . $ext;                   // Nombre único
    if (!move_uploaded_file($archivo['tmp_name'], "uploads/" . $nombre)) {
        $error = "No se pudo guardar la foto en el servidor.";
        return null;
    }
    return $nombre;
}
?>

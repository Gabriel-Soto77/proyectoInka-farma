<?php
session_start();                                  // Activar la sesión para poder cerrarla
$_SESSION = array();                              // Vaciar todos los datos guardados
session_destroy();                                // Regla 3: destruir la sesión
header("Location: login.php");                    // Volver al login
exit;
?>

<?php

// Tu código actual aquí

// Esto es solo un ejemplo de cómo registrar la ejecución exitosa
$timestamp = date('Y-m-d H:i:s');
$file = 'cron_log.txt'; // Nombre del archivo de registro

// Abre el archivo en modo de escritura y añade el registro
file_put_contents($file, "El script se ejecutó correctamente en: $timestamp\n", FILE_APPEND);

?>
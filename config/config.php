<?php

namespace config;

//Carga el autoload de Composer
require_once __DIR__ . '/../vendor/autoload.php';

//Importa la clase DatabaseConfig
require_once __DIR__ . '/DatabaseConfig.php';

//Crea una instancia de la configuración de base de datos
$db = new DatabaseConfig();

//Obtiene la conexión a la base de datos
$pdo = $db->getConnection();

//Esto devuelve la conexión para que pueda ser utilizada en otros archivos
return $pdo; 
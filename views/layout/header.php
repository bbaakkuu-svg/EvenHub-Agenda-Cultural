<?php
if (!defined('APP_NAME')) {
    define('APP_NAME', 'FLOCK — Agenda Cultural');
}
$basePath = $basePath ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Agenda cultural oficial del Ayuntamiento de Villaverde de la Ribera. Consulta eventos, festivales, conciertos e inscríbete.">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | ' . APP_NAME : APP_NAME ?></title>
    
    <!-- Bootstrap 5 CSS (Uso exclusivo: rejilla responsive y navbar colapsable) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <!-- Hoja de Estilos Propia (Identidad visual corporativa municipal) -->
    <link rel="stylesheet" href="<?= $basePath ?>css/styles.css">
    
    <!-- Hoja de Estilos Alternativa (Tema accesible / oscuro) -->
    <link rel="stylesheet" href="<?= $basePath ?>css/theme-dark.css" id="theme-dark-stylesheet" disabled>
</head>
<body>
<?php require_once __DIR__ . '/navbar.php'; ?>
<main class="main-content py-4">

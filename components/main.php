<?php
require_once dirname(__DIR__) . '/utils/routes.php';
?>

<!DOCTYPE html>
<html lang="es-MX">

<head>
    <!-- Title Meta -->
    <meta charset="utf-8" />
    <title>Código & Chips | <?php echo $pageTitle; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="Código & chips | Administrar empresas" />
    <meta name="author" content="Código & chips" />
    <meta name="keywords"
        content="administración, código, chips, gestión, usuarios, empresas" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="robots" content="index, follow" />
    <meta name="theme-color" content="#ffffff">
    <meta name="app-base" content="<?php echo htmlspecialchars(appBasePath(), ENT_QUOTES, 'UTF-8'); ?>">

    <!-- App favicon -->
    <link rel="shortcut icon" href="<?php echo assetsUrl('images/logos/Logo-Codigo-Chips-Invertido.png'); ?>">

    <!-- Google Font Family link -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap"
        rel="stylesheet">

    <!-- Vendor css -->
    <link href="<?php echo assetsUrl('css/vendor.min.css'); ?>" rel="stylesheet" type="text/css" />

    <!-- Icons css -->
    <link href="<?php echo assetsUrl('css/icons.min.css'); ?>" rel="stylesheet" type="text/css" />

    <!-- App css -->
    <link href="<?php echo assetsUrl('css/style.min.css'); ?>" rel="stylesheet" type="text/css" />

    <!-- Custom CSS -->
    <link href="<?php echo pagesUrl('business/styles/manage.css'); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo assetsUrl('css/glass-ui.css'); ?>" rel="stylesheet" type="text/css" />

    <!-- Theme Config js -->
    <script src="<?php echo assetsUrl('js/config.js'); ?>"></script>
    <script src="<?php echo assetsUrl('js/path-resolver.js'); ?>"></script>
    <script src="<?php echo assetsUrl('js/glass-ui.js'); ?>"></script>

    <!-- Auth protection js -->
    <script src="<?php echo assetsUrl('js/auth.js'); ?>"></script>
    <script src="<?php echo assetsUrl('js/permissions.js'); ?>"></script>
</head>

<body>

    <!-- START Wrapper -->
    <div class="app-wrapper">
        <?php include __DIR__ . '../../components/topbar.php'; ?>

<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/helpers.php';
?>
<!-- includes/header_etudiant.php -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plateforme de Stages - <?= $page_title ?? 'Étudiant' ?></title>
    <meta name="csrf-token" content="<?= esc(csrf_token()) ?>">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Theme base -->
    <link rel="stylesheet" href="../assets/css/theme.css">

    <link rel="stylesheet" href="../assets/css/admin.css">
    
    <script defer src="../assets/js/app-helpers.js"></script>

    <style>
        /* --- Couleurs principales (legacy overrides) --- */
        :root {
            --primary-blue: #0d6efd;
            --secondary-orange: #fd7e14;
            --sidebar-bg: #0a2342;
            --sidebar-hover: #0d3b66;
            --text-light: #f8f9fa;
        }

        /* --- Sidebar --- */
        .sidebar {
            min-height: 100vh;
            background-color: var(--sidebar-bg);
            color: var(--text-light);
            transition: width 0.3s;
            width: 250px;
        }
        .sidebar .nav-link {
            color: var(--text-light);
            padding: 12px 20px;
            border-radius: 6px;
            margin: 2px 10px;
            transition: all 0.2s;
        }
        .sidebar .nav-link.active, 
        .sidebar .nav-link:hover {
            color: var(--sidebar-bg);
            background-color: var(--secondary-orange);
            font-weight: bold;
        }
        .sidebar .nav-link i {
            margin-right: 10px;
        }

        /* --- Main --- */
        main {
            background-color: #f8f9fa;
            min-height: 100vh;
            flex: 1;
            padding: 20px;
        }

        /* --- Header --- */
        header.navbar {
            background-color: var(--primary-blue);
        }
        header .navbar-brand {
            font-weight: bold;
            font-size: 1.3rem;
            color: var(--text-light);
        }
        .profile-info {
            display: flex;
            align-items: center;
            gap: 15px;
            color: var(--text-light);
        }
        .profile-info img {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--secondary-orange);
        }
        .btn-logout {
            border-color: var(--text-light);
            color: var(--text-light);
        }
        .btn-logout:hover {
            background-color: var(--secondary-orange);
            border-color: var(--secondary-orange);
            color: var(--sidebar-bg);
        }

        /* --- Responsive --- */
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                min-height: auto;
            }
            main {
                padding: 10px;
            }
        }
    </style>
</head>
<body>

<header class="navbar navbar-dark shadow-sm px-3">
    <a href="dashboard.php" class="navbar-brand d-flex align-items-center">
        <img src="../logo_formatec.png" alt="Logo" style="width:40px; margin-right:10px; border-radius:5px;" onerror="this.style.display='none'">
        Plateforme Stages - Étudiant
    </a>
    <div class="profile-info">
        <img src="<?= $_SESSION['avatar'] ?? '../assets/img/default-avatar.png' ?>" alt="Profil" onerror="this.src='../assets/img/default-avatar.png'">
        <span><?= htmlspecialchars($_SESSION['nomEtud'] ?? 'Étudiant') ?></span>
        <a href="../auth/logout.php" class="btn btn-outline-light btn-sm btn-logout">Déconnexion</a>
    </div>
</header>

<div class="d-flex">
    <!-- Sidebar -->
    <nav class="sidebar d-flex flex-column p-3">
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item">
                <a href="dashboard.php" class="nav-link <?= ($page_title ?? '') === 'Tableau de bord' ? 'active' : '' ?>">
                    <i class="bi bi-speedometer2"></i> Tableau de bord
                </a>
            </li>
            <li>
                <a href="consulter_stages.php" class="nav-link <?= ($page_title ?? '') === 'Consulter les stages' ? 'active' : '' ?>">
                    <i class="bi bi-search"></i> Consulter les stages
                </a>
            </li>
            <li>
                <a href="mon_stage.php" class="nav-link <?= ($page_title ?? '') === 'Mon stage' ? 'active' : '' ?>">
                    <i class="bi bi-briefcase"></i> Mon stage
                </a>
            </li>
            <li>
                <a href="ma_note.php" class="nav-link <?= ($page_title ?? '') === 'Mes notes' ? 'active' : '' ?>">
                    <i class="bi bi-file-earmark-text"></i> Mes notes
                </a>
            </li>
        </ul>
        <div class="mt-auto pt-3 border-top">
            <a href="../auth/logout.php" class="nav-link text-danger">
                <i class="bi bi-box-arrow-right"></i> Déconnexion
            </a>
        </div>
    </nav>

    <!-- Main Content -->
    <main>



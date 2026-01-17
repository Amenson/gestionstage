<?php
// includes/header.php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/helpers.php';
// Security headers
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: SAMEORIGIN");
header("Referrer-Policy: no-referrer-when-downgrade");
header("Permissions-Policy: geolocation=()" );
header("Content-Security-Policy: default-src 'self' https: data:; script-src 'self' https: 'unsafe-inline' 'unsafe-eval'; style-src 'self' https: 'unsafe-inline'; font-src 'self' https: data:;");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plateforme de Stages - <?= $page_title ?? 'Gestion' ?></title>
    <meta name="csrf-token" content="<?= esc(csrf_token()) ?>">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Theme (variables & utilities) -->
    <link rel="stylesheet" href="../assets/css/theme.css">

    <!-- CSS Admin personnalisé -->
    <link rel="stylesheet" href="../assets/css/admin.css">

    <!-- Small app helpers (fetch wrapper, csrf helper, notify) -->
    <script defer src="../assets/js/app-helpers.js"></script>
    
    <style>
        /* Styles inline minimaux pour le header uniquement */
    </style>
</head>
<body>

<header class="navbar navbar-dark shadow-sm px-3">
    <!-- Bouton toggle sidebar mobile -->
    <button class="btn btn-link text-white d-lg-none me-2" type="button" id="sidebarToggle">
        <i class="bi bi-list fs-4"></i>
    </button>
    
    <a href="<?php 
        $dashboardPath = (file_exists(__DIR__ . '/../admin/dashboard.php')) ? 'admin/dashboard.php' : 'Admin/dashboard.php';
        echo $dashboardPath;
    ?>" class="navbar-brand d-flex align-items-center">
        <img src="../logo_formatec.png" alt="Logo" style="width:40px; height:40px; margin-right:10px; border-radius:5px; object-fit:cover;" onerror="this.style.display='none'">
        <span>Plateforme Stages - Admin</span>
    </a>
    
    <div class="ms-auto d-flex align-items-center gap-3">
        <!-- Breadcrumb pour la page actuelle -->
        <div class="d-none d-md-block">
            <small class="text-white-50">
                <i class="bi bi-<?= 
                    basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'speedometer2' : 
                    (basename($_SERVER['PHP_SELF']) == 'ajouter_etudiant.php' ? 'person-plus' : 
                    (basename($_SERVER['PHP_SELF']) == 'ajouter_entreprise.php' ? 'building' : 
                    (basename($_SERVER['PHP_SELF']) == 'ajouter_stage.php' ? 'briefcase' : 
                    (basename($_SERVER['PHP_SELF']) == 'affecter_stage.php' ? 'person-check' : 
                    (basename($_SERVER['PHP_SELF']) == 'noter_stage.php' ? 'file-earmark-text' : 
                    (basename($_SERVER['PHP_SELF']) == 'ajouter_domaine.php' ? 'tags' : 'gear')))))) ?>"></i>
                <?= htmlspecialchars($page_title ?? 'Administration') ?>
            </small>
        </div>
        
        <!-- Profile info -->
        <div class="profile-info">
            <img src="<?= $_SESSION['avatar'] ?? '../assets/img/default-avatar.png' ?>" alt="Profil" onerror="this.src='../assets/img/default-avatar.png'">
            <span class="d-none d-md-inline"><?= htmlspecialchars($_SESSION['admin'] ?? $_SESSION['full_name'] ?? 'Admin') ?></span>
        </div>
    </div>
</header>

<div class="d-flex">
    <!-- Sidebar -->
    <nav class="sidebar d-flex flex-column" id="adminSidebar">
        <!-- Logo et titre -->
        <div class="sidebar-header p-3 border-bottom border-secondary">
            <div class="d-flex align-items-center">
                <i class="bi bi-shield-check text-warning fs-3 me-2"></i>
                <div>
                    <h5 class="mb-0 text-white fw-bold">Admin Panel</h5>
                    <small class="text-muted">Gestion Stages</small>
                </div>
            </div>
        </div>

        <!-- Menu principal -->
        <div class="sidebar-body flex-grow-1 p-3 overflow-auto">
            <ul class="nav nav-pills flex-column mb-auto">
                <!-- Dashboard -->
                <li class="nav-item mb-2">
                    <a href="dashboard.php" class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : '' ?>">
                        <i class="bi bi-speedometer2"></i>
                        <span>Tableau de bord</span>
                    </a>
                </li>

                <!-- Section Étudiants -->
                <li class="nav-item mb-2">
                    <div class="nav-section-title text-muted small text-uppercase fw-bold px-3 py-2">
                        <i class="bi bi-people me-2"></i>Étudiants
                    </div>
                    <a href="ajouter_etudiant.php" class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'ajouter_etudiant.php') ? 'active' : '' ?>">
                        <i class="bi bi-person-plus"></i>
                        <span>Ajouter Étudiant</span>
                    </a>
                </li>

                <!-- Section Entreprises -->
                <li class="nav-item mb-2">
                    <div class="nav-section-title text-muted small text-uppercase fw-bold px-3 py-2">
                        <i class="bi bi-building me-2"></i>Entreprises
                    </div>
                    <a href="ajouter_entreprise.php" class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'ajouter_entreprise.php') ? 'active' : '' ?>">
                        <i class="bi bi-building-add"></i>
                        <span>Ajouter Entreprise</span>
                    </a>
                </li>

                <!-- Section Stages -->
                <li class="nav-item mb-2">
                    <div class="nav-section-title text-muted small text-uppercase fw-bold px-3 py-2">
                        <i class="bi bi-briefcase me-2"></i>Stages
                    </div>
                    <a href="ajouter_stage.php" class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'ajouter_stage.php') ? 'active' : '' ?>">
                        <i class="bi bi-briefcase-fill"></i>
                        <span>Ajouter Stage</span>
                    </a>
                    <a href="affecter_stage.php" class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'affecter_stage.php') ? 'active' : '' ?>">
                        <i class="bi bi-person-check"></i>
                        <span>Affecter Stage</span>
                    </a>
                    <a href="noter_stage.php" class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'noter_stage.php') ? 'active' : '' ?>">
                        <i class="bi bi-file-earmark-text"></i>
                        <span>Noter Stage</span>
                    </a>
                    <a href="offres.php" class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'offres.php') ? 'active' : '' ?>">
                        <i class="bi bi-list-check"></i>
                        <span>Liste des Offres</span>
                    </a>
                </li>

                <!-- Section Paramètres -->
                <li class="nav-item mb-2">
                    <div class="nav-section-title text-muted small text-uppercase fw-bold px-3 py-2">
                        <i class="bi bi-gear me-2"></i>Paramètres
                    </div>
                    <a href="ajouter_domaine.php" class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'ajouter_domaine.php') ? 'active' : '' ?>">
                        <i class="bi bi-tags"></i>
                        <span>Domaines d'Activité</span>
                    </a>
                    <a href="ajouter_type_entreprise.php" class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'ajouter_type_entreprise.php') ? 'active' : '' ?>">
                        <i class="bi bi-building-gear"></i>
                        <span>Types d'Entreprise</span>
                    </a>
                    <a href="ajouter_type_stage.php" class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'ajouter_type_stage.php') ? 'active' : '' ?>">
                        <i class="bi bi-briefcase-fill"></i>
                        <span>Types de Stage</span>
                    </a>
                    <a href="ajouter_competence.php" class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'ajouter_competence.php') ? 'active' : '' ?>">
                        <i class="bi bi-award"></i>
                        <span>Compétences</span>
                    </a>
                    <a href="ajouter_pays.php" class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'ajouter_pays.php') ? 'active' : '' ?>">
                        <i class="bi bi-globe"></i>
                        <span>Pays</span>
                    </a>
                    <a href="associer_competence.php" class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'associer_competence.php') ? 'active' : '' ?>">
                        <i class="bi bi-link-45deg"></i>
                        <span>Associer Compétences</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Footer Sidebar -->
        <div class="sidebar-footer p-3 border-top border-secondary">
            <div class="d-flex flex-column gap-2">
                <a href="../index.php" class="nav-link text-center" target="_blank">
                    <i class="bi bi-house-door"></i>
                    <span>Retour au site</span>
                </a>
                <a href="../auth/logout.php" class="nav-link nav-link-danger text-center">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Déconnexion</span>
                </a>
            </div>
            <div class="text-center mt-3">
                <small class="text-muted">
                    <i class="bi bi-shield-lock"></i> Session sécurisée
                </small>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow-1">

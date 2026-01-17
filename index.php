<?php
session_start();
require_once("config/db.php");

$loginSuccess = isset($_GET["login"]) && $_GET["login"] === "success";
$userRole = $_GET["role"] ?? null;
$redirectDelay = 3000; // 3 secondes avant redirection automatique

// Statistiques publiques
$stats = [
    'total_stages' => $pdo->query("SELECT COUNT(*) as total FROM Stage WHERE fkEtudiant IS NULL")->fetch()["total"],
    'total_entreprises' => $pdo->query("SELECT COUNT(*) as total FROM Entreprise")->fetch()["total"],
    'total_etudiants' => $pdo->query("SELECT COUNT(*) as total FROM Etudiant")->fetch()["total"],
    'stages_affectes' => $pdo->query("SELECT COUNT(*) as total FROM Stage WHERE fkEtudiant IS NOT NULL")->fetch()["total"]
];

// Stages récents (3 derniers)
$stagesRecents = $pdo->query("
    SELECT s.*, e.nomEntreprise, ts.libTypeStage
    FROM Stage s
    LEFT JOIN Entreprise e ON s.fkEntreprise = e.numSiret
    LEFT JOIN TypeStage ts ON s.fkTypeStage = ts.codeTypeStage
    WHERE s.fkEtudiant IS NULL
    ORDER BY s.dateParution DESC
    LIMIT 3
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plateforme de Gestion de Stages - Formatec</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Theme base -->
    <link rel="stylesheet" href="assets/css/theme.css">

    <!-- CSS personnalisé -->
    <link rel="stylesheet" href="assets/css/index.css">

    <!-- Small app helpers (fetch wrapper, csrf helper, notify) -->
    <script defer src="assets/js/app-helpers.js"></script>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top shadow">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <i class="bi bi-briefcase-fill me-2"></i>
                <strong>Plateforme Stages</strong>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#accueil">Accueil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#stages">Stages</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#statistiques">Statistiques</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#connexion">Connexion</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Message de connexion réussie -->
    <?php if ($loginSuccess): ?>
    <div class="alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3" 
         role="alert" 
         style="z-index: 9999; min-width: 400px; max-width: 600px; box-shadow: 0 10px 40px rgba(0,0,0,0.3);"
         id="loginSuccessAlert">
        <div class="d-flex align-items-center gap-3">
            <i class="bi bi-check-circle-fill fs-1"></i>
            <div class="flex-grow-1">
                <h5 class="mb-2 fw-bold">Connexion réussie !</h5>
                <p class="mb-1">
                    Bienvenue, <strong><?= htmlspecialchars($userRole === 'admin' ? ($_SESSION['admin'] ?? 'Administrateur') : (trim(($_SESSION['prenomEtud'] ?? '') . ' ' . ($_SESSION['nomEtud'] ?? '')) ?: 'Étudiant')) ?></strong> !
                </p>
                <small class="text-muted d-block">
                    Redirection automatique vers votre tableau de bord dans <span id="countdown" class="fw-bold text-dark">3</span> secondes...
                </small>
                <small class="text-muted d-block mt-2">
                    <i class="bi bi-info-circle"></i> Cliquez sur cette notification pour accéder immédiatement
                </small>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" id="closeAlert"></button>
        </div>
    </div>
    <?php endif; ?>

    <!-- Hero Section -->
    <section id="accueil" class="hero-section">
        <div class="container">
            <div class="row align-items-center min-vh-100">
                <div class="col-lg-6">
                    <div class="hero-content">
                        <?php if (isset($_SESSION["admin"]) || isset($_SESSION["idEtudiant"])): ?>
                            <h1 class="display-4 fw-bold mb-4">
                                Bienvenue, <span class="text-warning">
                                    <?php 
                                        if (isset($_SESSION["admin"])) {
                                            echo htmlspecialchars($_SESSION["admin"]);
                                        } elseif (isset($_SESSION["prenomEtud"])) {
                                            echo htmlspecialchars($_SESSION["prenomEtud"] . " " . $_SESSION["nomEtud"]);
                                        }
                                    ?>
                                </span> !
                            </h1>
                            <p class="lead mb-4">
                                Vous êtes connecté à la plateforme de gestion de stages.
                            </p>
                            <div class="hero-buttons">
                                <?php if (isset($_SESSION["admin"])): ?>
                                    <a href="admin/dashboard.php" class="btn btn-primary btn-lg me-3">
                                        <i class="bi bi-speedometer2"></i> Tableau de bord Admin
                                    </a>
                                <?php else: ?>
                                    <a href="etudiant/dashboard.php" class="btn btn-primary btn-lg me-3">
                                        <i class="bi bi-speedometer2"></i> Mon Tableau de bord
                                    </a>
                                <?php endif; ?>
                                <a href="#stages" class="btn btn-outline-light btn-lg">
                                    <i class="bi bi-search"></i> Voir les stages
                                </a>
                            </div>
                        <?php else: ?>
                            <h1 class="display-4 fw-bold mb-4">
                                Bienvenue sur la <span class="text-primary">Plateforme de Gestion de Stages</span>
                            </h1>
                            <p class="lead mb-4">
                                Connectez étudiants et entreprises pour faciliter la recherche et la gestion des stages. 
                                Une solution complète pour Formatec.
                            </p>
                            <div class="hero-buttons">
                                <a href="#connexion" class="btn btn-primary btn-lg me-3">
                                    <i class="bi bi-box-arrow-in-right"></i> Se connecter
                                </a>
                                <a href="#stages" class="btn btn-outline-light btn-lg">
                                    <i class="bi bi-search"></i> Voir les stages
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="hero-image">
                        <div class="floating-card card-1">
                            <i class="bi bi-briefcase"></i>
                            <p>Stages</p>
                        </div>
                        <div class="floating-card card-2">
                            <i class="bi bi-building"></i>
                            <p>Entreprises</p>
                        </div>
                        <div class="floating-card card-3">
                            <i class="bi bi-people"></i>
                            <p>Étudiants</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistiques -->
    <section id="statistiques" class="stats-section py-5">
        <div class="container">
            <h2 class="text-center mb-5">Statistiques de la plateforme</h2>
            <div class="row g-4">
                <div class="col-md-3 col-sm-6">
                    <div class="stat-box">
                        <div class="stat-icon bg-primary">
                            <i class="bi bi-briefcase"></i>
                        </div>
                        <h3 class="stat-number" data-target="<?= $stats['total_stages'] ?>">0</h3>
                        <p class="stat-label">Stages disponibles</p>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-box">
                        <div class="stat-icon bg-success">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <h3 class="stat-number" data-target="<?= $stats['stages_affectes'] ?>">0</h3>
                        <p class="stat-label">Stages affectés</p>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-box">
                        <div class="stat-icon bg-warning">
                            <i class="bi bi-building"></i>
                        </div>
                        <h3 class="stat-number" data-target="<?= $stats['total_entreprises'] ?>">0</h3>
                        <p class="stat-label">Entreprises</p>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-box">
                        <div class="stat-icon bg-info">
                            <i class="bi bi-people"></i>
                        </div>
                        <h3 class="stat-number" data-target="<?= $stats['total_etudiants'] ?>">0</h3>
                        <p class="stat-label">Étudiants</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stages récents -->
    <section id="stages" class="stages-section py-5 bg-light">
        <div class="container">
            <div class="row mb-4">
                <div class="col-12 text-center">
                    <h2 class="mb-3">Stages récents disponibles</h2>
                    <p class="text-muted">Découvrez les dernières offres de stage publiées</p>
                </div>
            </div>
            <div class="row g-4">
                <?php if (count($stagesRecents) > 0): ?>
                    <?php foreach ($stagesRecents as $stage): ?>
                    <div class="col-md-4">
                        <div class="stage-card">
                            <div class="stage-header">
                                <h5><?= htmlspecialchars($stage["libStage"]) ?></h5>
                                <span class="badge bg-primary"><?= htmlspecialchars($stage["libTypeStage"]) ?></span>
                            </div>
                            <div class="stage-body">
                                <p class="stage-company">
                                    <i class="bi bi-building"></i>
                                    <?= htmlspecialchars($stage["nomEntreprise"]) ?>
                                </p>
                                <?php if ($stage["periodeStage"]): ?>
                                <p class="stage-period">
                                    <i class="bi bi-calendar"></i>
                                    <?= htmlspecialchars($stage["periodeStage"]) ?>
                                </p>
                                <?php endif; ?>
                                <?php if ($stage["dateParution"]): ?>
                                <p class="stage-date text-muted small">
                                    <i class="bi bi-clock"></i>
                                    Publié le <?= date("d/m/Y", strtotime($stage["dateParution"])) ?>
                                </p>
                                <?php endif; ?>
                            </div>
                            <div class="stage-footer">
                                <a href="etudiant/consulter_stages.php?id=<?= $stage["numOffre"] ?>" class="btn btn-sm btn-primary">
                                    Voir les détails <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="alert alert-info text-center">
                            <i class="bi bi-info-circle"></i> Aucun stage disponible pour le moment
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <div class="text-center mt-4">
                <a href="etudiant/consulter_stages.php" class="btn btn-outline-primary">
                    Voir tous les stages <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Section Connexion -->
    <section id="connexion" class="login-section py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="login-container">
                        <?php if (isset($_SESSION["admin"]) || isset($_SESSION["idEtudiant"])): ?>
                            <!-- Utilisateur connecté -->
                            <div class="alert alert-info text-center">
                                <div class="mb-3">
                                    <i class="bi bi-check-circle-fill fs-1 text-success"></i>
                                </div>
                                <h3>Vous êtes déjà connecté !</h3>
                                <p class="mb-4">
                                    <?php if (isset($_SESSION["admin"])): ?>
                                        Vous êtes connecté en tant qu'<strong>administrateur</strong>.
                                    <?php else: ?>
                                        Vous êtes connecté en tant qu'<strong>étudiant</strong>.
                                        <br>
                                        <small>Nom : <?= htmlspecialchars($_SESSION["prenomEtud"] ?? '') . ' ' . htmlspecialchars($_SESSION["nomEtud"] ?? '') ?></small>
                                    <?php endif; ?>
                                </p>
                                <div class="d-flex gap-3 justify-content-center flex-wrap">
                                    <?php if (isset($_SESSION["admin"])): ?>
                                        <a href="admin/dashboard.php" class="btn btn-primary btn-lg">
                                            <i class="bi bi-speedometer2"></i> Accéder au tableau de bord Admin
                                        </a>
                                    <?php else: ?>
                                        <a href="etudiant/dashboard.php" class="btn btn-primary btn-lg">
                                            <i class="bi bi-speedometer2"></i> Accéder à mon tableau de bord
                                        </a>
                                    <?php endif; ?>
                                    <a href="auth/logout.php" class="btn btn-outline-secondary btn-lg">
                                        <i class="bi bi-box-arrow-right"></i> Se déconnecter
                                    </a>
                                </div>
                            </div>
                        <?php else: ?>
                            <h2 class="text-center mb-4">Connexion à la plateforme</h2>
                            <p class="text-center text-muted mb-5">Choisissez votre type de connexion</p>
                            
                            <div class="row g-4">
                                <!-- Connexion Étudiant -->
                                <div class="col-md-6">
                                    <div class="login-card student-card">
                                        <div class="login-icon">
                                            <i class="bi bi-person-circle"></i>
                                        </div>
                                        <h4>Étudiant</h4>
                                        <p class="text-muted">Accédez à votre espace étudiant pour consulter les stages et suivre vos candidatures.</p>
                                        <a href="auth/login_etudiant.php" class="btn btn-primary w-100">
                                            <i class="bi bi-box-arrow-in-right"></i> Se connecter en tant qu'étudiant
                                        </a>
                                        <div class="login-features mt-3">
                                            <small class="text-muted">
                                                <i class="bi bi-check-circle"></i> Consulter les offres<br>
                                                <i class="bi bi-check-circle"></i> Suivre votre stage<br>
                                                <i class="bi bi-check-circle"></i> Voir vos notes
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Connexion Administrateur -->
                                <div class="col-md-6">
                                    <div class="login-card admin-card">
                                        <div class="login-icon">
                                            <i class="bi bi-shield-lock"></i>
                                        </div>
                                        <h4>Administrateur</h4>
                                        <p class="text-muted">Accédez au panneau d'administration pour gérer les stages, étudiants et entreprises.</p>
                                        <a href="auth/login.php" class="btn btn-success w-100">
                                            <i class="bi bi-box-arrow-in-right"></i> Se connecter en tant qu'admin
                                        </a>
                                        <div class="login-features mt-3">
                                            <small class="text-muted">
                                                <i class="bi bi-check-circle"></i> Gérer les stages<br>
                                                <i class="bi bi-check-circle"></i> Gérer les étudiants<br>
                                                <i class="bi bi-check-circle"></i> Gérer les entreprises
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer (modern) -->
    <footer class="footer-section py-5">
      <div class="container">
        <div class="row gy-4">
          <div class="col-md-4">
            <div class="d-flex align-items-start gap-3">
              <img src="logo_formatec.png" alt="Formatec" class="me-2" style="width:120px; border-radius:8px;" onerror="this.style.display='none'">
              <div>
                <p class="mb-0">Formatec, l'excellence universitaire au service de l'innovation et du savoir.</p>
              </div>
            </div>
          </div>

          <div class="col-md-2">
            <h5 class="mb-3 text-light">Liens utiles</h5>
            <ul class="list-unstyled">
              <li><a href="#accueil" class="text-muted text-decoration-none">Accueil</a></li>
              <li><a href="#stages" class="text-muted text-decoration-none">Stages</a></li>
              <li><a href="#statistiques" class="text-muted text-decoration-none">Statistiques</a></li>
              <li><a href="#connexion" class="text-muted text-decoration-none">Connexion</a></li>
            </ul>
          </div>

          <div class="col-md-3">
            <h5 class="mb-3 text-light">Contact</h5>
            <p class="mb-1"><a href="mailto:contact@formatec.tg" class="text-muted text-decoration-none">contact@formatec.tg</a></p>
            <p class="mb-1 text-muted">Téléphone : +228 90 00 00 00</p>
            <p class="mb-0 text-muted">Adresse : Lomé, Togo</p>
          </div>

          <div class="col-md-3">
            <h5 class="mb-3 text-light">Suivez-nous</h5>
            <div class="d-flex gap-2">
              <a href="#" class="btn btn-outline-light btn-sm" aria-label="Twitter"><i class="bi bi-twitter"></i></a>
              <a href="#" class="btn btn-outline-light btn-sm" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
              <a href="#" class="btn btn-outline-light btn-sm" aria-label="LinkedIn"><i class="bi bi-linkedin"></i></a>
              <a href="#" class="btn btn-outline-light btn-sm" aria-label="GitHub"><i class="bi bi-github"></i></a>
            </div>
          </div>
        </div>

        <div class="text-center mt-4 text-muted small">
          &copy; <?= date('Y') ?> Formatec. Tous droits réservés.
        </div>
      </div>
    </footer>

    <!-- end footer -->
                    <p class="text-muted mb-0">
                        &copy; <?= date('Y') ?> Formatec. Tous droits réservés.
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- JavaScript personnalisé -->
    <script src="assets/js/index.js"></script>
</body>
</html>

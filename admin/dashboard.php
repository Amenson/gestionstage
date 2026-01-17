<?php
require_once("../includes/security.php");
require_once("../config/db.php");

$page_title = "Dashboard";

// Statistiques pour le dashboard
$stats = [
    'total_etudiants' => $pdo->query("SELECT COUNT(*) as total FROM Etudiant")->fetch()["total"],
    'total_entreprises' => $pdo->query("SELECT COUNT(*) as total FROM Entreprise")->fetch()["total"],
    'total_stages' => $pdo->query("SELECT COUNT(*) as total FROM Stage")->fetch()["total"],
    'stages_affectes' => $pdo->query("SELECT COUNT(*) as total FROM Stage WHERE fkEtudiant IS NOT NULL")->fetch()["total"],
    'stages_disponibles' => $pdo->query("SELECT COUNT(*) as total FROM Stage WHERE fkEtudiant IS NULL")->fetch()["total"]
];
?>

<?php include '../includes/header.php'; ?>

<div class="container-dashboard">
    <div class="dashboard-header">
        <h1>Tableau de bord Administrateur</h1>
        <p class="text-muted">Bienvenue, <?= htmlspecialchars($_SESSION['admin'] ?? 'Admin') ?></p>
    </div>

    <!-- Statistiques -->
    <div class="row g-4 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="stat-box bg-primary text-white">
                <div class="stat-icon">
                    <i class="bi bi-people"></i>
                </div>
                <h3 class="stat-number"><?= $stats['total_etudiants'] ?></h3>
                <p class="stat-label">Étudiants</p>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-box bg-success text-white">
                <div class="stat-icon">
                    <i class="bi bi-building"></i>
                </div>
                <h3 class="stat-number"><?= $stats['total_entreprises'] ?></h3>
                <p class="stat-label">Entreprises</p>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-box bg-warning text-white">
                <div class="stat-icon">
                    <i class="bi bi-briefcase"></i>
                </div>
                <h3 class="stat-number"><?= $stats['total_stages'] ?></h3>
                <p class="stat-label">Total Stages</p>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-box bg-info text-white">
                <div class="stat-icon">
                    <i class="bi bi-check-circle"></i>
                </div>
                <h3 class="stat-number"><?= $stats['stages_affectes'] ?></h3>
                <p class="stat-label">Stages Affectés</p>
            </div>
        </div>
    </div>

    <!-- Menu d'actions -->
    <div class="dashboard-menu">
        <a href="ajouter_etudiant.php" class="dashboard-card">
            <i class="bi bi-person-plus"></i>
            <span>Ajouter Étudiant</span>
            <small class="card-description">Enregistrer un nouvel étudiant</small>
        </a>
        <a href="ajouter_entreprise.php" class="dashboard-card">
            <i class="bi bi-building"></i>
            <span>Ajouter Entreprise</span>
            <small class="card-description">Ajouter une nouvelle entreprise</small>
        </a>
        <a href="ajouter_stage.php" class="dashboard-card">
            <i class="bi bi-briefcase"></i>
            <span>Ajouter Stage</span>
            <small class="card-description">Créer une nouvelle offre de stage</small>
        </a>
        <a href="affecter_stage.php" class="dashboard-card">
            <i class="bi bi-person-check"></i>
            <span>Affecter Stage</span>
            <small class="card-description">Affecter un stage à un étudiant</small>
        </a>
        <a href="noter_stage.php" class="dashboard-card">
            <i class="bi bi-file-earmark-text"></i>
            <span>Noter Stage</span>
            <small class="card-description">Évaluer et noter un stage</small>
        </a>
        <a href="ajouter_domaine.php" class="dashboard-card">
            <i class="bi bi-tags"></i>
            <span>Domaines</span>
            <small class="card-description">Gérer les domaines d'activité</small>
        </a>
        <a href="ajouter_type_entreprise.php" class="dashboard-card">
            <i class="bi bi-building-add"></i>
            <span>Types Entreprise</span>
            <small class="card-description">Gérer les types d'entreprise</small>
        </a>
        <a href="ajouter_type_stage.php" class="dashboard-card">
            <i class="bi bi-briefcase-fill"></i>
            <span>Types Stage</span>
            <small class="card-description">Gérer les types de stage</small>
        </a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

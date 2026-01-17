<?php
require_once '../includes/auth.php';
requireRole('professeur');
require_once '../includes/config.php';

$page_title = "Gestion des offres de stage";

// Suppression
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM stage WHERE id = ? AND publie_par = ?");
    $stmt->execute([$id, $_SESSION['user_id']]);
    header("Location: offres.php?msg=supprime");
    exit;
}

// Récupération des offres
$stmt = $pdo->prepare("
    SELECT s.*, e.nom AS entreprise_nom 
    FROM stage s 
    LEFT JOIN entreprise e ON s.id_entreprise = e.id 
    WHERE s.publie_par = ?
    ORDER BY s.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$offres = $stmt->fetchAll();
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<div class="pt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2">Mes offres de stage</h1>
        <a href="offres_ajout.php" class="btn btn-success">+ Nouvelle offre</a>
    </div>

    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'supprime'): ?>
        <div class="alert alert-success alert-dismissible fade show">Offre supprimée avec succès</div>
    <?php endif; ?>

    <?php if (empty($offres)): ?>
        <div class="alert alert-info">Vous n'avez pas encore publié d'offre.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Titre</th>
                        <th>Entreprise</th>
                        <th>Début</th>
                        <th>Durée</th>
                        <th>État</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($offres as $offre): ?>
                    <tr>
                        <td class="fw-bold"><?= htmlspecialchars($offre['titre']) ?></td>
                        <td><?= htmlspecialchars($offre['entreprise_nom'] ?? '—') ?></td>
                        <td><?= $offre['date_debut'] ? date('d/m/Y', strtotime($offre['date_debut'])) : '—' ?></td>
                        <td><?= $offre['duree_mois'] ?> mois</td>
                        <td>
                            <span class="badge bg-<?= $offre['etat']==='ouvert'?'success':($offre['etat']==='pourvu'?'warning':'secondary') ?>">
                                <?= ucfirst($offre['etat']) ?>
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="offres_edit.php?id=<?= $offre['id'] ?>" class="btn btn-sm btn-outline-primary me-1">
                                <i class="bi bi-pencil"></i> Modifier
                            </a>
                            <a href="?delete=<?= $offre['id'] ?>" class="btn btn-sm btn-outline-danger" 
                               onclick="return confirm('Confirmer la suppression définitive ?')">
                                <i class="bi bi-trash"></i> Supprimer
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

</main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
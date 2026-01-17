<?php
require_once("../config/db.php");

// simulation étudiant id = 1
$idEtud = 1;

$stmt = $pdo->prepare("SELECT * FROM Stage WHERE fkEtudiant = ?");
$stmt->execute([$idEtud]);
$stage = $stmt->fetch();

if ($stage) {
    echo "<h2>Mon stage : " . $stage["libStage"] . "</h2>";
} else {
    echo "Aucun stage affecté";
}

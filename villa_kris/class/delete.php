<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../admin/login.php');
    exit;
}
include '../config.php';

$id = $_GET['id'];

$stmt = $pdo->prepare("DELETE FROM villas WHERE id = ?");
$stmt->execute([$id]);

header('Location: ../admin/villas.php');
exit;
    
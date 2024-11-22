<?php
require 'db.php';
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // Obtener todos los tenis
    $stmt = $pdo->query('SELECT * FROM tenis');
    echo json_encode($stmt->fetchAll());
} elseif ($method === 'POST') {
    // Agregar un nuevo tenis
    $data = json_decode(file_get_contents('php://input'), true);
    $stmt = $pdo->prepare('INSERT INTO tenis (imagen_url, sku, precio_retail, precio_reventa, talla, disponibilidad) 
                           VALUES (?, ?, ?, ?, ?, ?)');
    $stmt->execute([
        $data['imagenUrl'],
        $data['sku'],
        $data['precioRetail'],
        $data['precioReventa'],
        $data['talla'],
        $data['disponibilidad'] ? 1 : 0
    ]);
    echo json_encode(['id' => $pdo->lastInsertId()]);
} elseif ($method === 'PUT') {
    // Editar un tenis
    parse_str($_SERVER['QUERY_STRING'], $query);
    $id = $query['id'];
    $data = json_decode(file_get_contents('php://input'), true);
    $stmt = $pdo->prepare('UPDATE tenis SET imagen_url = ?, sku = ?, precio_retail = ?, precio_reventa = ?, talla = ?, disponibilidad = ? WHERE id = ?');
    $stmt->execute([
        $data['imagenUrl'],
        $data['sku'],
        $data['precioRetail'],
        $data['precioReventa'],
        $data['talla'],
        $data['disponibilidad'] ? 1 : 0,
        $id
    ]);
    echo json_encode(['status' => 'success']);
} elseif ($method === 'DELETE') {
    // Borrar un tenis
    parse_str($_SERVER['QUERY_STRING'], $query);
    $id = $query['id'];
    $stmt = $pdo->prepare('DELETE FROM tenis WHERE id = ?');
    $stmt->execute([$id]);
    echo json_encode(['status' => 'deleted']);
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
}
?>

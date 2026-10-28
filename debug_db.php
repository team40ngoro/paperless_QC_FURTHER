<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=paperless_futher', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt1 = $pdo->query("SELECT 'Per Pack' as metric, COUNT(id) as value FROM pengemasan_plastik WHERE tanggal IS NOT NULL");
    echo 'Result 1: ' . json_encode($stmt1->fetchAll(PDO::FETCH_ASSOC)) . PHP_EOL;

    $stmt2 = $pdo->query("SELECT count(p.id) as count FROM pengemasan_plastik p LEFT JOIN berat_produk_bag b ON p.id = b.id_pengemasan_plastik WHERE p.tanggal IS NOT NULL");
    echo 'Result 2: ' . json_encode($stmt2->fetchAll(PDO::FETCH_ASSOC)) . PHP_EOL;

    $stmt3 = $pdo->query("SELECT count(id) FROM pengemasan_plastik");
    echo 'Total rows in pengemasan_plastik: ' . $stmt3->fetchColumn() . PHP_EOL;
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}
?>

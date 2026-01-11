<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');

require __DIR__ . '/config.php';

$device = preg_replace('/[^a-zA-Z0-9_\-]/', '', (string)($_GET['device'] ?? 'esp8266'));
$hours = (int)($_GET['hours'] ?? 12);
if ($hours < 1) $hours = 1;
if ($hours > 168) $hours = 168;

try {
  $pdo = new PDO(DB_DSN, DB_USER, DB_PASS, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
  ]);

  // ab >12h auf 1min mitteln für flüssige Charts
  $step = ($hours <= 12) ? 0 : 60;

  if ($step === 0) {
    $stmt = $pdo->prepare(
      "SELECT UNIX_TIMESTAMP(ts) AS t, red_c AS r, yellow_c AS y, green_c AS g
       FROM temp_log
       WHERE device=:device AND ts >= (NOW() - INTERVAL :h HOUR)
       ORDER BY ts ASC"
    );
    $stmt->bindValue(':device', $device);
    $stmt->bindValue(':h', $hours, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
  } else {
    $stmt = $pdo->prepare(
      "SELECT (UNIX_TIMESTAMP(ts) DIV :step)*:step AS t,
              AVG(red_c) AS r, AVG(yellow_c) AS y, AVG(green_c) AS g
       FROM temp_log
       WHERE device=:device AND ts >= (NOW() - INTERVAL :h HOUR)
       GROUP BY t
       ORDER BY t ASC"
    );
    $stmt->bindValue(':step', $step, PDO::PARAM_INT);
    $stmt->bindValue(':device', $device);
    $stmt->bindValue(':h', $hours, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  $t=[]; $r=[]; $y=[]; $g=[];
  foreach ($rows as $row) {
    $t[] = (int)$row['t'];
    $r[] = $row['r'] !== null ? round((float)$row['r'], 2) : null;
    $y[] = $row['y'] !== null ? round((float)$row['y'], 2) : null;
    $g[] = $row['g'] !== null ? round((float)$row['g'], 2) : null;
  }

  echo json_encode(['device'=>$device,'hours'=>$hours,'t'=>$t,'r'=>$r,'y'=>$y,'g'=>$g]);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['ok'=>false,'error'=>'db_error']);
}
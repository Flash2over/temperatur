<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');

require __DIR__ . '/config.php';

$key = $_SERVER['HTTP_X_API_KEY'] ?? '';
if (!hash_equals(API_KEY, $key)) {
  http_response_code(401);
  echo json_encode(['ok'=>false,'error'=>'unauthorized']);
  exit;
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!is_array($data)) {
  http_response_code(400);
  echo json_encode(['ok'=>false,'error'=>'invalid_json']);
  exit;
}

$device = preg_replace('/[^a-zA-Z0-9_\-]/', '', (string)($data['device'] ?? 'esp8266'));

$toFloatOrNull = function($v) {
  if ($v === null) return null;
  if (is_numeric($v)) return (float)$v;
  return null;
};

$red    = $toFloatOrNull($data['red'] ?? null);
$yellow = $toFloatOrNull($data['yellow'] ?? null);
$green  = $toFloatOrNull($data['green'] ?? null);

try {
  $pdo = new PDO(DB_DSN, DB_USER, DB_PASS, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
  ]);

  $stmt = $pdo->prepare(
    'INSERT INTO temp_log (device, red_c, yellow_c, green_c) VALUES (:device, :r, :y, :g)'
  );
  $stmt->execute([
    ':device' => $device,
    ':r' => $red,
    ':y' => $yellow,
    ':g' => $green,
  ]);

  echo json_encode(['ok'=>true]);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['ok'=>false,'error'=>'db_error']);
}
<?php
require 'config.php';
header('Content-Type: application/json; charset=utf-8');

$token = $_POST['token'] ?? $_GET['token'] ?? '';
if (!hash_equals(token_atual(), (string)$token)) {
  http_response_code(403);
  echo json_encode(['ok'=>false,'erro'=>'Token inválido'], JSON_UNESCAPED_UNICODE);
  exit;
}

$raw = $_POST['devices'] ?? file_get_contents('php://input');
$payload = json_decode((string)$raw, true);
$data = $payload['devices'] ?? $payload;

if (!is_array($data)) {
  http_response_code(400);
  echo json_encode(['ok'=>false,'erro'=>'Lista inválida'], JSON_UNESCAPED_UNICODE);
  exit;
}

$count = 0;
$invalidos = 0;
foreach (array_slice($data, 0, 512) as $d) {
  $ip = trim((string)($d['ip'] ?? ''));
  $mac = trim((string)($d['mac'] ?? ''));
  $name = mb_substr(trim((string)($d['name'] ?? '')), 0, 80);
  if (valid_ip($ip) && valid_mac($mac)) {
    registrar_presenca($ip, $mac, $name);
    $count++;
  } else {
    $invalidos++;
  }
}

$status = jread('scan_status', []);
$status['ultimo_scan'] = now();
$status['ultimo_envio'] = now();
$status['ultimo_total'] = $count;
$status['ultimo_invalidos'] = $invalidos;
$status['ultimo_erro'] = '';
$status['leitor_versao'] = mb_substr((string)($payload['version'] ?? 'python'), 0, 40);
jwrite('scan_status', $status);

fechar_offlines();
echo json_encode(['ok'=>true,'recebidos'=>$count,'invalidos'=>$invalidos,'hora'=>now()], JSON_UNESCAPED_UNICODE);

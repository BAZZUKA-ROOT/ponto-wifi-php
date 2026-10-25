<?php
require 'config.php';
header('Content-Type: application/json; charset=utf-8');
$token = $_GET['token'] ?? $_POST['token'] ?? '';
if (!hash_equals(token_atual(), (string)$token)) {
  http_response_code(403);
  echo json_encode(['ok'=>false,'erro'=>'Token inválido'], JSON_UNESCAPED_UNICODE);
  exit;
}
$cad = jread('cadastros', []);
$out = [];
$changed = false;
foreach ($cad as $code => &$c) {
  if (strtotime($c['expira_em'] ?? '2000-01-01') < time()) {
    if (($c['status'] ?? '') !== 'vinculado') { $c['status'] = 'expirado'; $changed = true; }
    continue;
  }
  if (($c['status'] ?? '') === 'vinculado') continue;
  $ip = trim($c['ip'] ?? '');
  if (valid_ip($ip)) {
    $out[] = [
      'codigo' => $code,
      'ip' => $ip,
      'funcionario_id' => $c['funcionario_id'] ?? '',
      'device_uid' => $c['device_uid'] ?? '',
      'apelido' => $c['apelido'] ?? '',
      'criado_em' => $c['criado_em'] ?? '',
      'status' => $c['status'] ?? ''
    ];
  }
}
if ($changed) jwrite('cadastros', $cad);
echo json_encode(['ok'=>true,'pendentes'=>$out,'hora'=>now()], JSON_UNESCAPED_UNICODE);

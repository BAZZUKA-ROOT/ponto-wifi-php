<?php
require 'config.php';
header('Content-Type: application/json; charset=utf-8');
$codigo = strtoupper(trim($_GET['codigo'] ?? ''));
$cad = jread('cadastros', []);
if (!$codigo || !isset($cad[$codigo])) {
  echo json_encode(['ok'=>false,'erro'=>'Cadastro não encontrado'], JSON_UNESCAPED_UNICODE);
  exit;
}
$c = $cad[$codigo];
$func = nome_funcionario($c['funcionario_id'] ?? '');
echo json_encode([
  'ok'=>true,
  'codigo'=>$codigo,
  'funcionario'=>$func,
  'status'=>$c['status'] ?? '',
  'ip'=>$c['ip'] ?? '',
  'mac'=>$c['mac'] ?? '',
  'device_uid'=>!empty($c['device_uid']),
  'hora'=>now()
], JSON_UNESCAPED_UNICODE);

<?php
require 'config.php';
header_page('Verificar instalação');
$checks=[];
$checks[]=['PHP funcionando', true, PHP_VERSION];
$checks[]=['Fuso horário Brasília', date_default_timezone_get()==='America/Sao_Paulo', date_default_timezone_get().' - '.date('H:i:s')];
$checks[]=['Pasta data existe', is_dir(DATA_DIR), DATA_DIR];
$checks[]=['Pasta data gravável', is_writable(DATA_DIR), is_writable(DATA_DIR)?'OK':'Sem permissão'];
$checks[]=['Token configurado', token_atual() !== '', token_atual()];
$checks[]=['JSON funcionários', is_array(jread('funcionarios',[])), 'OK'];
$checks[]=['JSON dispositivos', is_array(jread('dispositivos',[])), 'OK'];
$checks[]=['JSON presenças', is_array(jread('presencas',[])), 'OK'];
$st=jread('scan_status',[]);
$ultimo=$st['ultimo_scan'] ?? '';
$okScan=$ultimo && (time()-strtotime($ultimo) < 180);
$checks[]=['Leitor recente', $okScan, $ultimo ?: 'Ainda sem leitura'];
?>
<div class="card"><h2>Verificação da instalação</h2><table><tr><th>Item</th><th>Status</th><th>Detalhe</th></tr><?php foreach($checks as $c): ?><tr><td><?=h($c[0])?></td><td><?= $c[1] ? '🟢 OK' : '🔴 Atenção' ?></td><td><?=h($c[2])?></td></tr><?php endforeach; ?></table><p class="notice">Use esta tela antes de testar na empresa. Se o leitor recente estiver vermelho, abra o leitor Python e aguarde uma leitura.</p></div>
<?php footer_page(); ?>

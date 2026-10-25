<?php
require 'config.php';
$pres = jread('presencas', []);
$hoje = date('Y-m-d');
$alterou = false;
foreach ($pres as $k => &$p) {
  if (($p['data'] ?? '') === $hoje) {
    // Zera somente entrada/saída errada do dia para o sistema registrar novamente no horário correto.
    unset($pres[$k]);
    $alterou = true;
  }
}
if ($alterou) jwrite('presencas', $pres);
header_page('Corrigir horário');
echo '<div class="card"><h2>Horário corrigido</h2><p>Timezone configurado para Brasília: <b>'.h(date_default_timezone_get()).'</b></p><p>Hora atual do sistema: <b>'.h(now()).'</b></p><p>Registros de ponto de hoje foram apagados para o leitor marcar novamente no horário certo.</p><p><a class="btn" href="index.php">Voltar ao painel</a></p></div>';
footer_page();

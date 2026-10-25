<?php require 'config.php'; fechar_offlines(); atualizar_notificacoes_automaticas(); header_page('Linha do tempo');
$funs=jread('funcionarios',[]); $pres=jread('presencas',[]); $data=$_GET['data']??date('Y-m-d'); $fid=$_GET['funcionario']??'';
?>
<section class="hero"><div><h2>Linha do tempo</h2><p>Entrada, almoço, retorno e saída em formato simples.</p></div></section>
<section class="card"><form class="inline" method="get"><label>Data<input type="date" name="data" value="<?=h($data)?>"></label><label>Funcionário<select name="funcionario"><option value="">Todos</option><?php foreach($funs as $f): ?><option value="<?=h($f['id'])?>" <?=$fid===$f['id']?'selected':''?>><?=h($f['nome'])?></option><?php endforeach; ?></select></label><button>Filtrar</button></form></section><br>
<section class="timeline-list">
<?php $tem=false; foreach($funs as $f): if($fid && $fid!==$f['id']) continue; $p=$pres[$data.'_'.$f['id']]??null; if(!$p) continue; $tem=true; $calc=calcular_ponto_array($p); ?>
  <div class="card timeline-card"><div class="timeline-head"><div><h2><?=h($f['nome'])?></h2><p><?=date('d/m/Y',strtotime($data))?> · Total <?=hhmm_from_min($calc['liquido'])?> · Extra <?=hhmm_from_min($calc['extra'])?></p></div><a class="btn ghost" href="funcionario.php?id=<?=urlencode($f['id'])?>">Ficha</a></div>
  <div class="timeline">
    <?php foreach(eventos_do_ponto($p) as $ev): ?><div class="timeline-item"><div class="time"><?=date('H:i',strtotime($ev['hora']))?></div><div class="dot"><?=$ev['icone']?></div><div><b><?=h($ev['tipo'])?></b><p><?=h($ev['texto'])?></p></div></div><?php endforeach; ?>
  </div></div>
<?php endforeach; if(!$tem): ?><div class="card"><h2>Nenhum ponto encontrado</h2><p>Não há registros para esse filtro.</p></div><?php endif; ?>
</section>
<?php footer_page(); ?>

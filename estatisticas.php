<?php require 'config.php'; fechar_offlines(); atualizar_notificacoes_automaticas(); header_page('Estatísticas');
$funs=jread('funcionarios',[]); $ym=$_GET['mes']??date('Y-m'); $pres=jread('presencas',[]);
$totalMin=0;$totalExtra=0;$dias=0;$atrasos=0;$almoco=0;
foreach($funs as $f){ $r=resumo_funcionario_mes($f['id'],$ym); $totalMin+=$r['minutos']; $totalExtra+=$r['extra']; $dias+=$r['dias']; $atrasos+=$r['atrasos']; $almoco+=$r['almoco']; }
?>
<section class="hero"><div><h2>Estatísticas</h2><p>Médias, horas, atrasos e comportamento do mês.</p></div></section>
<section class="card"><form class="inline" method="get"><label>Mês<input type="month" name="mes" value="<?=h($ym)?>"></label><button>Filtrar</button></form></section><br>
<section class="grid kpis"><div class="card"><p>Horas trabalhadas</p><div class="big"><?=hhmm_from_min($totalMin)?></div></div><div class="card"><p>Horas extras</p><div class="big warn"><?=hhmm_from_min($totalExtra)?></div></div><div class="card"><p>Dias trabalhados</p><div class="big ok"><?=$dias?></div></div><div class="card"><p>Atrasos</p><div class="big off"><?=$atrasos?></div></div><div class="card"><p>Almoço/pausas</p><div class="big"><?=hhmm_from_min($almoco)?></div></div></section><br>
<section class="grid employee-grid">
<?php foreach($funs as $f): $r=resumo_funcionario_mes($f['id'],$ym); $me=media_entrada_func($f['id'],60); $ms=media_saida_func($f['id'],60); $ma=media_almoco_func($f['id'],60); ?>
  <div class="card employee stats-card"><div class="employee-top"><div class="avatar"><?=mb_strtoupper(mb_substr($f['nome']??'F',0,1))?></div><div><h2><?=h($f['nome'])?></h2><p><?=h($f['cargo']??'Funcionário')?></p></div></div>
  <div class="mini-grid"><p>Horas mês <b><?=hhmm_from_min($r['minutos'])?></b></p><p>Extra mês <b><?=hhmm_from_min($r['extra'])?></b></p><p>Dias <b><?=$r['dias']?></b></p><p>Atrasos <b><?=$r['atrasos']?></b></p><p>Média entrada <b><?=$me!==null?hhmm_from_min($me):'--:--'?></b></p><p>Média saída <b><?=$ms!==null?hhmm_from_min($ms):'--:--'?></b></p><p>Média almoço <b><?=$ma!==null?hhmm_from_min($ma):'--:--'?></b></p><p>Almoço mês <b><?=hhmm_from_min($r['almoco'])?></b></p></div>
  <a class="btn ghost" href="timeline.php?funcionario=<?=urlencode($f['id'])?>">Ver linha do tempo</a></div>
<?php endforeach; ?>
</section>
<?php footer_page(); ?>

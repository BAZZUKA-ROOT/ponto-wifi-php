<?php require 'config.php'; fechar_offlines(); atualizar_notificacoes_automaticas(); header_page('Mapa da loja');
$funs=jread('funcionarios',[]); $pres=jread('presencas',[]); $conf=jread('config',[]); $tol=(int)($conf['tolerancia_offline_min']??15); $hoje=date('Y-m-d');
$grupos=['trabalhando'=>[],'almoco'=>[],'offline'=>[]];
foreach($funs as $f){ $dev=dispositivo_do_funcionario($f['id']); $p=$pres[$hoje.'_'.$f['id']]??null; $on=$dev && !empty($dev['ultimo_visto']) && time()-strtotime($dev['ultimo_visto']) <= $tol*60; $st=($p['status']??''); if($st==='almoco') $grupos['almoco'][]=[$f,$p,$dev]; elseif($on) $grupos['trabalhando'][]=[$f,$p,$dev]; else $grupos['offline'][]=[$f,$p,$dev]; }
?>
<section class="hero"><div><h2>Mapa da loja</h2><p>Visão rápida de quem está presente, em almoço ou fora da rede.</p></div><span class="pill ok"><?=count($grupos['trabalhando'])?> trabalhando</span></section>
<section class="status-columns">
<?php $labels=['trabalhando'=>['🟢 Trabalhando','ok'],'almoco'=>['🟡 Almoço/Pausa','warn'],'offline'=>['🔴 Offline/Saiu','off']]; foreach($labels as $key=>$meta): ?>
  <div class="card status-col"><h2 class="<?=$meta[1]?>"><?=$meta[0]?></h2>
    <?php if(!$grupos[$key]): ?><p class="muted">Nenhum funcionário aqui.</p><?php endif; ?>
    <?php foreach($grupos[$key] as [$f,$p,$dev]): ?>
      <a class="map-person" href="funcionario.php?id=<?=urlencode($f['id'])?>">
        <span class="avatar small-avatar"><?=mb_strtoupper(mb_substr($f['nome']??'F',0,1))?></span>
        <span><b><?=h($f['nome'])?></b><small><?=h($f['cargo']??'Funcionário')?> · Último visto: <?=($dev&&!empty($dev['ultimo_visto']))?date('H:i:s',strtotime($dev['ultimo_visto'])):'--'?></small></span>
      </a>
    <?php endforeach; ?>
  </div>
<?php endforeach; ?>
</section>
<?php footer_page(); ?>

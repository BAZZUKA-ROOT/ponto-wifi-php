<?php require 'config.php'; fechar_offlines(); header_page('Funcionários');
$funs=jread('funcionarios',[]); $ym=$_GET['mes']??date('Y-m'); ?>
<section class="hero"><div><h2>Funcionários</h2><p>Ficha completa, indicadores do mês, celular vinculado e padrão de presença.</p></div><form><input type="month" name="mes" value="<?=h($ym)?>"><button class="btn">Filtrar</button></form></section>
<section class="grid employee-grid">
<?php foreach($funs as $f): $dev=dispositivo_do_funcionario($f['id']); $p=presenca_hoje_func($f['id']); $mes=resumo_funcionario_mes($f['id'],$ym); $media=media_entrada_func($f['id']); ?>
<div class="card employee"><div class="employee-top"><div class="avatar"><?=mb_strtoupper(mb_substr($f['nome']??'F',0,1))?></div><div><h2><?=h($f['nome'])?></h2><p><?=h($f['cargo']??'Funcionário')?> · <?=h($f['telefone']??'')?></p></div></div><div class="mini-grid"><p>Dias <b><?=$mes['dias']?></b></p><p>Horas/mês <b><?=hhmm_from_min($mes['minutos'])?></b></p><p>Extras/mês <b><?=hhmm_from_min($mes['extra'])?></b></p><p>Atrasos <b><?=$mes['atrasos']?></b></p><p>Média entrada <b><?=$media!==null?hhmm_from_min($media):'--'?></b></p><p>Hoje <b><?=hhmm_from_min($p['minutos']??0)?></b></p></div><p class="muted">Celular: <?= $dev?h(($dev['apelido']?:$dev['mac']).' / '.$dev['ip']):'não vinculado' ?></p><a class="btn" href="funcionario.php?id=<?=urlencode($f['id'])?>">Ver ficha completa</a></div>
<?php endforeach; if(!$funs): ?><div class="card"><h2>Nenhum funcionário cadastrado.</h2><p>Cadastre em Configurações.</p></div><?php endif; ?>
</section><?php footer_page(); ?>

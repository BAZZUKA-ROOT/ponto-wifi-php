<?php require 'config.php'; fechar_offlines(); atualizar_notificacoes_automaticas();
if($_SERVER['REQUEST_METHOD']==='POST'){ if(isset($_POST['limpar'])) jwrite('notificacoes',[]); if(isset($_POST['marcar_lidas'])){ $n=jread('notificacoes',[]); foreach($n as &$x) $x['lida']=1; jwrite('notificacoes',$n); } header('Location:notificacoes.php'); exit; }
header_page('Notificações'); $not=array_reverse(jread('notificacoes',[])); $nao=count(array_filter($not,fn($n)=>empty($n['lida'])));
?>
<section class="hero"><div><h2>Central de notificações</h2><p>Entradas, saídas, horas extras e problemas do leitor ficam registrados aqui.</p></div><span class="pill <?=$nao?'warn':'ok'?>"><?=$nao?> não lidas</span></section>
<section class="card"><form method="post" class="inline"><button name="marcar_lidas" value="1">Marcar como lidas</button><button class="danger" name="limpar" value="1" onclick="return confirm('Limpar notificações?')">Limpar tudo</button></form></section><br>
<section class="notifications">
<?php if(!$not): ?><div class="card"><h2>Nenhuma notificação</h2><p>Quando houver eventos importantes, eles aparecerão aqui.</p></div><?php endif; ?>
<?php foreach($not as $n): ?><div class="card notification <?=empty($n['lida'])?'unread':''?>"><div><span class="pill"><?=h($n['tipo']??'aviso')?></span><h2><?=h($n['titulo']??'Notificação')?></h2><p><?=h($n['texto']??'')?></p></div><time><?=!empty($n['data'])?date('d/m/Y H:i',strtotime($n['data'])):'--'?></time></div><?php endforeach; ?>
</section>
<?php footer_page(); ?>

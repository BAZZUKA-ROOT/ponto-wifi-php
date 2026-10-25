<?php require 'config.php';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $pres=jread('presencas',[]); $aj=jread('ajustes',[]); $fid=$_POST['funcionario_id']??''; $data=$_POST['data']??date('Y-m-d'); $key=$data.'_'.$fid;
  if(!isset($pres[$key])) $pres[$key]=['id'=>$key,'funcionario_id'=>$fid,'data'=>$data,'entrada'=>'','saida'=>'','ultimo_visto'=>'','minutos'=>0,'minutos_brutos'=>0,'almoco_minutos'=>0,'extra_minutos'=>0,'pausas'=>[],'status'=>'manual','origem'=>'manual'];
  $entrada=post_time('entrada',''); $saida=post_time('saida',''); $almoco=post_int('almoco_manual_min',0,0,300);
  $pres[$key]['entrada']=$entrada?$data.' '.$entrada.':00':''; $pres[$key]['saida']=$saida?$data.' '.$saida.':00':''; $pres[$key]['ultimo_visto']=$pres[$key]['saida'] ?: ($pres[$key]['entrada'] ?: now()); $pres[$key]['almoco_manual_min']=$almoco; $pres[$key]['status']=!empty($pres[$key]['saida'])?'finalizado':'trabalhando'; $pres[$key]['origem']='manual'; $pres[$key]['motivo_ajuste']=mb_substr(trim((string)($_POST['motivo']??'')),0,300); recalcular_presenca($pres[$key]); jwrite('presencas',$pres);
  $aj[]=['id'=>uniqid('aj_'),'funcionario_id'=>$fid,'data'=>$data,'entrada'=>$entrada,'saida'=>$saida,'almoco_manual_min'=>$almoco,'motivo'=>mb_substr(trim((string)($_POST['motivo']??'')),0,300),'criado_em'=>now()]; jwrite('ajustes',$aj); log_event('ajuste','Ponto corrigido manualmente',['funcionario_id'=>$fid,'data'=>$data]);
  header('Location: ajustes.php?ok=1&data='.urlencode($data)); exit;
}
header_page('Correção manual'); $funs=jread('funcionarios',[]); $pres=jread('presencas',[]); $data=$_GET['data']??date('Y-m-d');
?>
<div class="card"><h2>Correção manual do ponto</h2><p class="notice">Use quando o PC estava desligado, o funcionário esqueceu o Wi‑Fi ou houve erro de leitura. O motivo fica salvo no histórico.</p><?php if(isset($_GET['ok'])): ?><p class="success">Correção salva.</p><?php endif; ?><form method="get" class="inline"><label>Data</label><input type="date" name="data" value="<?=h($data)?>"><button>Carregar</button></form></div><br>
<div class="grid">
<?php foreach($funs as $f): $p=$pres[$data.'_'.$f['id']]??[]; ?>
<div class="card"><h2><?=h($f['nome'])?></h2><form method="post"><input type="hidden" name="funcionario_id" value="<?=h($f['id'])?>"><input type="hidden" name="data" value="<?=h($data)?>"><div class="twocol"><div><label>Entrada</label><input type="time" name="entrada" value="<?=!empty($p['entrada'])?date('H:i',strtotime($p['entrada'])):''?>"></div><div><label>Saída</label><input type="time" name="saida" value="<?=!empty($p['saida'])?date('H:i',strtotime($p['saida'])):''?>"></div></div><label>Almoço descontado manualmente em minutos</label><input type="number" name="almoco_manual_min" value="<?=h($p['almoco_manual_min']??($p['almoco_minutos']??60))?>"><label>Motivo da correção</label><textarea name="motivo" required placeholder="Ex: PC estava desligado / celular sem Wi‑Fi"><?=h($p['motivo_ajuste']??'')?></textarea><p>Total atual: <b><?=hhmm_from_min($p['minutos']??0)?></b> · Extra: <b><?=hhmm_from_min($p['extra_minutos']??0)?></b></p><button>Salvar correção</button></form></div>
<?php endforeach; ?>
</div>
<?php footer_page(); ?>

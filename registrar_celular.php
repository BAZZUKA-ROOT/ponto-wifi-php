<?php require 'config.php';

function mac_por_ip_windows($ip){
  if(!valid_ip($ip)) return '';
  $cmd = 'arp -a ' . escapeshellarg($ip);
  $out = [];
  @exec($cmd, $out);
  $txt = implode("\n", $out);
  if(!$txt){
    $out=[]; @exec('arp -a', $out); $txt=implode("\n", $out);
  }
  if(preg_match('/'.preg_quote($ip,'/').'\s+([0-9a-fA-F]{2}[-:][0-9a-fA-F]{2}[-:][0-9a-fA-F]{2}[-:][0-9a-fA-F]{2}[-:][0-9a-fA-F]{2}[-:][0-9a-fA-F]{2})\s+/m', $txt, $m)){
    return strtolower(str_replace(':','-',$m[1]));
  }
  return '';
}
function criar_ou_atualizar_dispositivo_cadastro($ip,$mac,$uid,$apelido,$userAgent,$funcionarioId){
  $disp=jread('dispositivos',[]);
  $idx=null;
  foreach($disp as $i=>$d){
    if(($mac && norm_mac($d['mac']??'')===norm_mac($mac)) || ($uid && ($d['device_uid']??'')===$uid) || (($d['ip']??'')===$ip && !empty($d['funcionario_id']) && ($d['funcionario_id']??'')===$funcionarioId)) { $idx=$i; break; }
  }
  if($idx===null){
    $disp[]=['id'=>uniqid('dev_'),'mac'=>$mac ?: ('uid-'.substr(sha1($uid ?: $ip),0,12)),'ip'=>$ip,'nome_dispositivo'=>'Cadastro pelo celular','apelido'=>$apelido ?: 'Celular cadastrado','funcionario_id'=>$funcionarioId,'device_uid'=>$uid,'user_agent'=>$userAgent,'primeiro_visto'=>now(),'ultimo_visto'=>now(),'leituras_online'=>0,'leituras_offline'=>0];
  } else {
    if($mac) $disp[$idx]['mac']=$mac;
    $disp[$idx]['ip']=$ip;
    $disp[$idx]['funcionario_id']=$funcionarioId;
    if($uid) $disp[$idx]['device_uid']=$uid;
    $disp[$idx]['user_agent']=$userAgent;
    $disp[$idx]['apelido']=$apelido ?: ($disp[$idx]['apelido']??'Celular cadastrado');
    $disp[$idx]['ultimo_visto']=now();
  }
  jwrite('dispositivos',$disp);
  return $mac;
}

$codigo=strtoupper(trim($_GET['codigo'] ?? $_POST['codigo'] ?? ''));
$uid=trim($_POST['device_uid'] ?? $_GET['device_uid'] ?? '');
$apelido=trim($_POST['apelido'] ?? '');
$cad=jread('cadastros',[]); $disp=jread('dispositivos',[]); $funs=jread('funcionarios',[]);
$status='erro'; $msg='Código inválido ou expirado.'; $funcNome=''; $ip=client_ip(); $mac='';
if($codigo && isset($cad[$codigo])){
  $c=$cad[$codigo]; foreach($funs as $f){ if($f['id']===$c['funcionario_id']) $funcNome=$f['nome']; }
  if(strtotime($c['expira_em']) < time()) { $cad[$codigo]['status']='expirado'; jwrite('cadastros',$cad); $msg='Este cadastro expirou. Gere outro link no painel.'; }
  else {
    $cad[$codigo]['ip']=$ip; $cad[$codigo]['user_agent']=$_SERVER['HTTP_USER_AGENT'] ?? ''; if($uid) $cad[$codigo]['device_uid']=$uid; if($apelido) $cad[$codigo]['apelido']=$apelido;
    foreach($disp as $d){ if((($d['ip']??'')===$ip || ($uid && ($d['device_uid']??'')===$uid)) && !empty($d['mac']) && strpos($d['mac'],'uid-')!==0){ $mac=$d['mac']; break; } }
    if(!$mac){ $mac = mac_por_ip_windows($ip); }
    // Cadastro instantâneo: ao abrir o link no celular, já vincula pelo IP/UID.
    // Se o Windows conseguir descobrir o MAC pela tabela ARP, salva o MAC real também.
    criar_ou_atualizar_dispositivo_cadastro($ip,$mac,$uid,$apelido,$cad[$codigo]['user_agent'],$c['funcionario_id']);
    $cad[$codigo]['mac']=$mac;
    $cad[$codigo]['status']='vinculado';
    $cad[$codigo]['vinculado_em']=now();
    $cad[$codigo]['modo']='cadastro_instantaneo';
    jwrite('cadastros',$cad);
    if($mac){ registrar_presenca($ip,$mac,$apelido ?: 'Cadastrado pelo celular'); }
    $status='ok';
    $msg='Celular cadastrado com sucesso para '.$funcNome.'. Se aparecer MAC UID temporário no painel, deixe o leitor aberto que ele troca pelo MAC real automaticamente.';
  }
}
?><!doctype html><html lang="pt-br"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Cadastro do celular</title><link rel="stylesheet" href="assets/style.css"></head><body><div class="wrap"><main><div class="card phone"><h1>Cadastro do celular</h1><p class="big <?=h($status)?>"><?=h($msg)?></p><?php if($funcNome): ?><p>Funcionário: <b><?=h($funcNome)?></b></p><?php endif; ?><p>IP deste celular na loja: <b><?=h($ip)?></b></p><?php if($mac): ?><p>MAC detectado: <b><?=h($mac)?></b></p><?php endif; ?><?php if($status==='aguardando'): ?><form id="uidForm" method="post"><input type="hidden" name="codigo" value="<?=h($codigo)?>"><input type="hidden" name="device_uid" id="device_uid"><label>Nome do aparelho, opcional</label><input name="apelido" placeholder="Ex: iPhone do Maicon"><button>Registrar este aparelho</button></form><p class="notice">Mantenha o Wi‑Fi ligado e esta página aberta. Normalmente vincula em 5 a 15 segundos. Se não vincular, confirme que o leitor Python está aberto no PC.</p><div class="progressbox"><div class="spinner"></div><span id="statusCadastro">Procurando este celular na rede...</span></div><a class="btn" href="registrar_celular.php?codigo=<?=h($codigo)?>">Atualizar agora</a><?php endif; ?></div></main></div><script>
(function(){
  var codigo='<?=h($codigo)?>';
  var k='ponto_wifi_device_uid';
  var uid=localStorage.getItem(k);
  if(!uid){uid='cel_'+Date.now()+'_'+Math.random().toString(16).slice(2); localStorage.setItem(k,uid);}
  var el=document.getElementById('device_uid');
  if(el && !el.value){
    el.value=uid;
    if(!location.search.includes('device_uid') && !sessionStorage.getItem('uid_sent_'+codigo)){
      sessionStorage.setItem('uid_sent_'+codigo,'1');
      document.getElementById('uidForm').submit();
    }
  }
  var statusEl=document.getElementById('statusCadastro');
  if(statusEl && codigo){
    var tentativas=0;
    setInterval(function(){
      tentativas++;
      fetch('api_cadastro_status.php?codigo='+encodeURIComponent(codigo), {cache:'no-store'})
        .then(function(r){return r.json();})
        .then(function(j){
          if(!j.ok) return;
          if(j.status==='vinculado'){
            statusEl.innerHTML='Celular vinculado com sucesso. Você já pode fechar esta tela.';
            setTimeout(function(){ location.reload(); }, 900);
          } else {
            statusEl.innerHTML='Procurando este celular na rede... tentativa '+tentativas+' · IP '+(j.ip||'detectando');
          }
        }).catch(function(){});
    }, 2500);
  }
})();
</script></body></html>

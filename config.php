<?php
// Sistema de ponto WiFi — configure conforme seu negócio
date_default_timezone_set('America/Sao_Paulo');
session_start();

define('DATA_DIR', __DIR__ . '/data');
define('TOKEN_FILE', DATA_DIR . '/token.txt');
if (!is_dir(DATA_DIR)) mkdir(DATA_DIR, 0775, true);
if (!file_exists(DATA_DIR.'/.htaccess')) file_put_contents(DATA_DIR.'/.htaccess', "Require all denied\nDeny from all\n", LOCK_EX);
if (!file_exists(DATA_DIR.'/index.html')) file_put_contents(DATA_DIR.'/index.html', '', LOCK_EX);

function token_atual(){
  if(!file_exists(TOKEN_FILE)) file_put_contents(TOKEN_FILE,'MinhaEmpresa', LOCK_EX);
  return trim((string)file_get_contents(TOKEN_FILE));
}
function set_token($t){ file_put_contents(TOKEN_FILE, trim((string)$t), LOCK_EX); }
function safe_name($n){ return preg_match('/^[a-z0-9_\-]+$/i', (string)$n) ? $n : 'invalid'; }
function jfile($n){ return DATA_DIR.'/'.safe_name($n).'.json'; }
function jread($n,$default=[]){
  $f=jfile($n); if(!file_exists($f)) return $default;
  $raw=@file_get_contents($f); if($raw===false || trim($raw)==='') return $default;
  $d=json_decode($raw,true); return is_array($d)?$d:$default;
}
function jwrite($n,$data){
  $f=jfile($n); $tmp=$f.'.tmp';
  $ok=file_put_contents($tmp, json_encode($data, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT), LOCK_EX);
  if($ok!==false) rename($tmp,$f);
}

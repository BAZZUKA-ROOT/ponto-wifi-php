// Atualiza o painel automaticamente a cada 60s, sem atrapalhar formulários.
(function(){
  if(location.pathname.endsWith('/index.php') || location.pathname.endsWith('/ponto/') || location.pathname.endsWith('/ponto')){
    setTimeout(function(){ location.reload(); }, 60000);
  }
})();

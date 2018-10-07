<?php if(!class_exists('Rain\Tpl')){exit;}?><h1>Olá <?php echo htmlspecialchars( $nome, ENT_COMPAT, 'UTF-8', FALSE ); ?>!!!</h1>
<p>Teste de tpl</p>
<p>A versão do php é <?php echo htmlspecialchars( $versao, ENT_COMPAT, 'UTF-8', FALSE ); ?></p>
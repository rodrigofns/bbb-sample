<html>
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8"/>
	<title>Conferência BigBlueButton</title>
</head>
<body>

<?php
require_once('bbb_api.php');
require_once('bbb_api_conf.php');

if(isset($_POST['criador'])) { // criação de nova sala
	$ret = BigBlueButton::createMeetingArray($_POST['criador'], $_POST['sala'],
		null, 'serpromod', 'serpro', $salt, $url, 'http://10.200.118.193/git/bbb/');
	if(!$ret) die('Pau geral, servidor nao respondeu.');
	$ret = (object)$ret;
	if($ret->returncode == 'FAILED')
		die("Erro ({$ret->messageKey}): {$ret->message}");
	else if($ret->returncode == 'SUCCESS' && $ret->messageKey == 'duplicateWarning')
		die("Já existe uma sala chamada \"{$ret->meetingID}\", escolha outro nome.");

	$urlmod = BigBlueButton::joinURL($_POST['sala'], $_POST['criador'], 'serpromod', $salt, $url);
	echo "<p><a target=\"_blank\" href=\"$urlmod\">Sua URL para entrar na sala.</a></p>";
	echo 'URLs dos convidados:<ul>';
	for($i = 1; $i <= 8; ++$i) {
		$conv = $_POST["convidado_$i"];
		if(!strlen($conv)) continue;
		$ismod = isset($_POST["mod_$i"]);
		$urlconv = BigBlueButton::joinURL($_POST['sala'], $conv,
			$ismod ? 'serpromod' : 'serpro', $salt, $url);
		echo "<li><a target=\"_blank\" href=\"$urlconv\">URL de $conv</a></li>";
	}
	echo '</ul>';
}
else { ?>
	<!-- primeira exibição da página -->
	<h2>Criar sala de conferência</h2>
	<form method="post">
		<p>Informe seu login (ex.: fulano.silva): <input type="text" name="criador"/></p>
		<p>Nome da sala: <input type="text" name="sala"/></p>
		Logins dos convidados (ex.: fulano.silva):<ul>
		<? for($i = 1; $i <= 8; ++$i) { ?>
			<li><?=$i?> <input type="text" name="convidado_<?=$i?>"/>
				<label><input type="checkbox" name="mod_<?=$i?>"/> moderador</label></li>
		<? } ?>
		</ul>
		<input type="submit" value="Criar sala"/>
	</form>
<? } ?>

</body>
</html>
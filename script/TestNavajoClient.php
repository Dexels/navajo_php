<?php
	session_start();
	
	include_once "NavajoPhpClient.php";
	startupNavajo('http://ficus:3000/sportlink/knvb/servlet/Postman','ROOT','');


	$res = $navajoClient->doInitSend('InitNavajoStatus');
	
	?>

<form action="UpdateNavajo.php" method="post">

<div>

<?php  
	class LayoutDataSource extends NavajoLayout {
		protected function render($nav,$msg,$client) {
			NavajoPhpClient::showProperty($nav,'Name',$msg);
			NavajoPhpClient::showProperty($nav,'IsAlive',$msg);
			NavajoPhpClient::showProperty($nav,'OpenConnections',$msg);
			echo '<br/><hr/>';
		}
	}

	$mm = $res->getMessage('NavajoStatus/SQLMapInfo/Datasources');
	$l = new LayoutDataSource();
	$l->doRender($res,$mm,$phpclient);
	
	?>

<?php // $phpclient->showArray('aap'); ?>

	
<?php $phpclient->showAbsoluteProperty($res,'NavajoStatus/Kernel/Repository','aap'); ?>	
</div>

<?php //$phpclient->showTable('NavajoStatus/Kernel','aap'); ?>	

<br/>	
<?php $phpclient->showAbsoluteProperty($res,'NavajoStatus/Kernel/Version'); ?>	

<br/>	
<?php 

$res->getMessage('NavajoStatus/SQLMapInfo/Datasources@6');
$phpclient->showAbsoluteProperty($res,'NavajoStatus/SQLMapInfo/Datasources@6/Name'); ?>	

<input type="submit" value="Do something"/>

<a href="UpdateNavajo.php">aaaaaahhh!!!!!!</a>

<?php
//echo session_encode();
	
?><a></a>
</form>

<form action="DestroySession.php" method="post">
<input type="submit" value="Clear session"/>
</form>


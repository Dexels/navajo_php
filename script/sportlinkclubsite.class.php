<?php
class ClubSite extends NavajoSite {
	public function onStartSession() {
		startupNavajo('http://slwebsvracc.sportlink.enovation.net/sportlink/knvb/servlet/Postman','ROOT','R20T');
		NavajoClient::callInitService('external/competition/InitCompetitionData');
		echo "INIT SESSION!";

	}
	public function onDestroySession() {
	}
	
	public function echoHeader() {

		global $siteHome,$defaultPage,$siteContent,$siteTitle;

	?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-gb" lang="en-gb" dir="ltr" >
<head>
  <title><?php echo $siteTitle;?></title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <meta name="description" content="<?php echo $siteContent;?>">
  <meta name="robots" content="index,follow"> 
  <link rel="stylesheet" type="text/css" href="/<?php echo $siteHome; ?>navajo/css/navajo.css" />
</head>
<body bgcolor="#ccccff">
<div class="navajo">
<form action="index.php" method="post">

<?php
	}
	
	public function echoFooter() {
?></form>
</div>
</body>
</html>
<?php
	}
}
?>
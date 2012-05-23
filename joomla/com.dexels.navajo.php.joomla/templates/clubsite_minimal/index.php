<?php
  defined( '_JEXEC' ) or die( 'Restricted index access' );
  define( 'YOURBASEPATH', dirname(__FILE__) );
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" >
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Sportlink Clubsite plugin - Powered by Navajo Integrator</title>
    <link href="http://<?php echo $_SERVER['HTTP_HOST'] ;?>/templates/<?php echo $this->template?>/css/template.css" rel="stylesheet" type="text/css" />
    <script src="http://<?php echo $_SERVER['HTTP_HOST'] ;?>/templates/<?php echo $this->template?>/js/mootools.js" type="text/javascript"></script>
    <script src="http://<?php echo $_SERVER['HTTP_HOST'] ;?>/templates/<?php echo $this->template?>/js/sortableTable.js" type="text/javascript"></script>
</head>
<body>
    <div id="sportlink_wrapper">
        <?php if ($this->countModules('links_boven')) : ?>
        <div id="left">
            <jdoc:include type="modules" name="links_boven" style="xhtml" />
        </div>
        <?php endif; ?>
        <?php if ($this->countModules('menu')) : ?>
        <div id="top">
            <jdoc:include type="modules" name="menu" style="xhtml" />
        </div>
        <?php endif; ?>
        <jdoc:include type="component" />
    </div>
</body>
</html>

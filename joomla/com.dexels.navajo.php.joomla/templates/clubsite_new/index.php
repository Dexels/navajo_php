<?php 
    # Dit bestand mag niet direct aangeroepen worden
    defined( '_JEXEC' ) or die( 'Restricted access' );
    
    # Hebben we een linker en/of rechterbalk?
    if ($this->countModules('links_boven') + $this->countModules('links_onder') > 0) : 
        $toon_linkerbalk = TRUE;
    else :
        $toon_linkerbalk = FALSE;
    endif;
    
    if ($this->countModules('rechts_boven') + $this->countModules('rechts_onder') > 0) : 
        $toon_rechterbalk = TRUE;
    else :
        $toon_rechterbalk = FALSE;
    endif;

    # Bepaal breedte middenste kolom : het totaal aantal kolommen is 24
    # Totale breedte 950px : 23 maal 30px met 10px marge rechts en 1x 30px zonder marge
    if ($toon_linkerbalk AND $toon_rechterbalk) : 
        $colspan_midden = 'span-14';
    elseif ($toon_linkerbalk) :
        $colspan_midden = 'span-19 last';
    elseif ($toon_rechterbalk) :
        $colspan_midden = 'span-19';
    else : 
        $colspan_midden = 'span-24 last';
    endif;

    # Bepaal het aantal kolommen onderaan. Bij drie kolommen worden de middelste twee samengevoegd.
    $aantal_kolommen_onderaan = 0;
    if ($this->countModules('onder_links') > 0) : 
        $aantal_kolommen_onderaan++;
    endif;
    if ($this->countModules('onder_links_midden') > 0) : 
        $aantal_kolommen_onderaan++;
    endif;
    if ($this->countModules('onder_rechts_midden') > 0) : 
        $aantal_kolommen_onderaan++;
    endif;
    if ($this->countModules('onder_rechts') > 0) : 
        $aantal_kolommen_onderaan++;
    endif;

    # Aan de hand van het aantal kolommen onderaan bepaal de breedtes van de onderste modules
    if ($aantal_kolommen_onderaan == 4) :
        $colspan_onder_links         = 'span-5';
        $colspan_onder_links_midden  = 'span-7';
        $colspan_onder_rechts_midden = 'span-7';
        $colspan_onder_rechts        = 'span-5 last';
    endif;
    if ($aantal_kolommen_onderaan == 3) :
        $colspan_onder_links         = 'span-5';
        $colspan_onder_links_midden  = 'span-14';
        $colspan_onder_rechts_midden = 'span-0';
        $colspan_onder_rechts        = 'span-5 last';
    endif;
    if ($aantal_kolommen_onderaan == 2) :
        $colspan_onder_links         = 'span-12';
        $colspan_onder_links_midden  = 'span-0';
        $colspan_onder_rechts_midden = 'span-0';
        $colspan_onder_rechts        = 'span-12 last';
    endif;
    if ($aantal_kolommen_onderaan == 1) :
        $colspan_onder_links         = 'span-24 last';
        $colspan_onder_links_midden  = 'span-0';
        $colspan_onder_rechts_midden = 'span-0';
        $colspan_onder_rechts        = 'span-0';
    endif;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" >
<head>
    <jdoc:include type="head" />

    <!-- Standaard stylesheets -->
    <link href="http://<?php echo $_SERVER['HTTP_HOST'] ;?>/templates/<?php echo $this->template?>/blueprint/screen.css" rel="stylesheet" type="text/css" media="screen, projection" />
    <link href="http://<?php echo $_SERVER['HTTP_HOST'] ;?>/templates/<?php echo $this->template?>/blueprint/print.css" rel="stylesheet" type="text/css" media="print" />
    <!--[if lt IE 8]><link href="http://<?php echo $_SERVER['HTTP_HOST'] ;?>/templates/<?php echo $this->template?>/blueprint/screen.css" rel="stylesheet" type="text/css" media="screen, projection"/><![endif]-->
    <link href="http://<?php echo $_SERVER['HTTP_HOST'] ;?>/templates/<?php echo $this->template?>/blueprint/typography.css" rel="stylesheet" type="text/css" media="screen" />
     <!-- Eigen stylesheet -->
    <link href="http://<?php echo $_SERVER['HTTP_HOST'] ;?>/templates/<?php echo $this->template?>/css/clubsite.css" rel="stylesheet" type="text/css" media="screen"/ />
    <link href="http://<?php echo $_SERVER['HTTP_HOST'] ;?>/templates/<?php echo $this->template?>/css/navajo.css" rel="stylesheet" type="text/css" media="screen"/ />

</head>
<body>
    <div id="site" class="container">  
        <!-- Bovenkant: logo, sponsors en menu -->
        <div id="logo" class="span-5">
            <?php if ($this->countModules('logo')) : ?>
            <jdoc:include type="modules" name="logo" style="xhtml" />
            <?php endif; ?>
        </div>

        <div id="sponsors" class="span-19 last">
            <?php if ($this->countModules('sponsors')) : ?>
            <jdoc:include type="modules" name="sponsors" style="xhtml" />
            <?php endif; ?>
        </div>
        <hr class="space" />
        <!-- Menu -->
        <div id="menu" class="span-24 last">
            <?php if ($this->countModules('menu')) : ?>
            <jdoc:include type="modules" name="menu" style="xhtml" />
            <?php endif; ?>
        </div>
        <hr class="space" />

        <?php if ($toon_linkerbalk) : ?>
        <!-- Linkerbalk : -->
        <div id="links" class="span-5">
            <?php if ($this->countModules('links_boven')) : ?>
            <div id="links_boven" class="span-5">
                <jdoc:include type="modules" name="links_boven" style="xhtml" />
            </div>
            <?php endif; ?>

            <?php if ($this->countModules('links_onder')) : ?>
            <div id="links_onder" class="span-5">
                <jdoc:include type="modules" name="links_onder" style="xhtml" />
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Middelste kolom : -->
        <div id="midden" class="<?php echo $colspan_midden; ?>">
            <?php if ($this->countModules('slideshow')) : ?>
            <!-- Slideshow : -->
            <div id="slideshow" class="<?php echo $colspan_midden; ?>">
                <jdoc:include type="modules" name="slideshow" style="xhtml" />
            </div>
            <?php endif; ?>

            <?php if ($this->countModules('nieuwsflits')) : ?>
            <!-- Nieuwsflits : -->
            <div id="nieuwsflits" class="<?php echo $colspan_midden; ?>">
                <jdoc:include type="modules" name="nieuwsflits" style="xhtml" />
            </div>
            <?php endif; ?>

            <!-- "Mainbody" : de artikelen : -->
            <div id="artikelen" class="<?php echo $colspan_midden; ?>">
                <jdoc:include type="component" />
            </div>
        </div>

        <?php if ($toon_rechterbalk) : ?>
        <!-- Rechterbalk : -->
        <div id="rechts" class="span-5 last">
            <?php if ($this->countModules('rechts_boven')) : ?>
            <div id="rechts_boven" class="span-5">
                <jdoc:include type="modules" name="rechts_boven" style="xhtml" />
            </div>
            <?php endif; ?>

            <?php if ($this->countModules('rechts_onder')) : ?>
            <div id="rechts_onder" class="span-5">
                <jdoc:include type="modules" name="rechts_onder" style="xhtml" />
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php if ($aantal_kolommen_onderaan > 0) : ?>
        <!-- Onderkant : -->
        <div id="onder_links" style="clear:both" class="<?php echo $colspan_onder_links; ?>">
            <jdoc:include type="modules" name="onder_links" style="xhtml" />
        </div>
        <?php endif; ?>
        <?php if ($aantal_kolommen_onderaan > 2) : ?>
        <div id="onder_links_midden" class="<?php echo $colspan_onder_links_midden; ?>">
            <jdoc:include type="modules" name="onder_links_midden" style="xhtml" />
        </div>
        <?php endif; ?>
        <?php if ($aantal_kolommen_onderaan > 3) : ?>
        <div id="onder_rechts_midden" class="<?php echo $colspan_onder_rechts_midden; ?>">
            <jdoc:include type="modules" name="onder_rechts_midden" style="xhtml" />
        </div>
        <?php endif; ?>
        <?php if ($aantal_kolommen_onderaan > 1) : ?>
        <div id="onder_rechts" class="<?php echo $colspan_onder_rechts; ?>">
            <jdoc:include type="modules" name="onder_rechts" style="xhtml" />
        </div>
        <?php endif; ?>
        <?php if ($aantal_kolommen_onderaan > 0) : ?>
        <hr class="space">
        <?php endif; ?>
         
        <?php if ($this->countModules('footer')) : ?>
        <!-- Footer / copyright : -->
        <div id="footer" class="span-24 last">
            <jdoc:include type="modules" name="footer" style="xhtml"/>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>

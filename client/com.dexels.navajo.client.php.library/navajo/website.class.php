<?php
require_once "./navajo/NavajoSite.php";

if(class_exists('WebSite') != true) {

    class WebSite extends NavajoSite {

        public function echoHeader() {
            echo "<?xml version='1.0' encoding='utf-8'?>";
            echo "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>\n";
            echo "<html xmlns='http://www.w3.org/1999/xhtml' lang='en-gb' dir='ltr'>\n";
            echo "<head>\n";
            echo "<title>Navajo Integrator PHP plugin</title>\n";
            echo "<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1' />\n";
            echo "</head>";
            echo "<body>";
            echo "<div id='wrapper'>";
            echo "<p align='center'>This is a default header</p><hr/>";
            echo "<form action='index.php' method='post' id='navajo_form'>";
        }

        public function echoFooter() {
            # to prevent duplicate submission of a form use some unique key for every submission
            echo "<input type='hidden' name='form_id' value='" . $_SESSION['formId'] . "'/>";
            echo "</form>";
            echo "</div>";
            echo "<hr/><p align='center'>This is a default footer</p>";
            echo "</body>";
            echo "</html>";
        }
    }
    
    } else { # error: reload, we didn't figure this one out yet.. 
    ?>
    <script type="text/javascript">
        <!--
        parent.location = "http://<?php echo $_SERVER['HTTP_HOST'];?>/index.php"
        //-->
    </script>
<?php
}
?>

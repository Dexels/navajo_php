<?php
/* ******************************************************************************
 * History: 
 * $Log: LoadConf.php,v $
 * Revision 1.1.2.5  2005/10/18 09:25:45  mos
 * AW references removed
 *
 * Revision 1.1.2.3  2005/06/28 08:59:18  mike
 * switch to Ascii
 * 
 * ****************************************************************************** 
 * Last CheckIn : $Author: mos $ 
 * Date : $Date: 2005/10/18 09:25:45 $ 
 * Revision : $Revision: 1.1.2.5 $ 
 * Repository File : $Source: /cvs/as/WLP_NEW/src_php/Attic/LoadConf.php,v $ 
 * ******************************************************************************
 */

/*
* load the config file and save the data into an array
* @return array (with configfile data)
*/

function LoadConfiguration() {

    $conf_data=array();

    $file = fopen("config.conf", 'r');

    if($file) {
        while(!feof($file)) {

            $buffer = fgets($file);

            $buffer = trim($buffer);
            if(!empty( $buffer ) ) {

                $pos = strpos($buffer, '=');
            
                if($pos > 0 ) {

                  $dumb = trim(substr($buffer, 0, $pos));
                
                  if( !empty($dumb)  ) {
                        $conf_data[ strtoupper( substr($buffer, 0 , $pos) ) ] = substr($buffer, $pos +1);
                  }
                }

            }
        }
    
    }

    fclose($file);
    return $conf_data;
}

?>
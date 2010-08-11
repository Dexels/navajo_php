<?php
function print_var( $var )
{
   if( is_string( $var ) )
       return( '"'.str_replace( array("\x00", "\x0a", "\x0d", "\x1a", "\x09"), array('\0', '\n', '\r', '\Z', '\t'), $var ).'"' );
   else if( is_bool( $var ) )
   {
       if( $var )
           return( 'true' );
       else
           return( 'false' );
   }
   else if( is_array( $var ) )
   {
       $result = 'array( ';
       $comma = '';
       foreach( $var as $key => $val )
       {
           $result .= $comma.print_var( $key ).' => '.print_var( $val );
           $comma = ', ';
       }
       $result .= ' )';
       return( $result );
   }
  
   return( var_export( $var, true ) );    // anything else, just let php try to print it
}

function trace( $msg )
{
   echo "<pre>\n";
  
   $trace = array_reverse( debug_backtrace() );
   $indent = '';
   $func = '';
  
   echo $msg."\n";
  
   foreach( $trace as $val)
   {
       echo $indent.$val['file'].' on line '.$val['line'];
      
       if( $func ) echo ' in function '.$func;
      
       if( $val['function'] == 'include' ||
           $val['function'] == 'require' ||
           $val['function'] == 'include_once' ||
           $val['function'] == 'require_once' )
           $func = '';
       else
       {
           $func = $val['function'].'(';
          
           if( isset( $val['args'][0] ) )
           {
               $func .= ' ';
               $comma = '';
               foreach( $val['args'] as $val )
               {
                   $func .= $comma.print_var( $val );
                   $comma = ', ';
               }
               $func .= ' ';
           }
          
           $func .= ')';
       }
       echo "\n";
       $indent .= "\t";
   }
   echo "</pre>\n";
}
?>

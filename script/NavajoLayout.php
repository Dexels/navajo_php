<?php
class arraySort {
    var $aData;
    var $aSortKey;
    var $aSortDir;

    function _sortcmp($a, $b) {
        $r = strnatcmp($a[$this->aSortKey], $b[$this->aSortKey]);
        if ($this->aSortDir == "DESC")
            $r = $r * -1;        
        return $r;
    }

    function sort() {
        usort($this->aData, array (
            $this,
            "_sortcmp"
        ));
    }
}

abstract class NavajoLayout {
    abstract protected function render($nav, $message_element);
    abstract protected function renderHeader($nav, $message_element);

    protected function beforeRendering() {
    
    }

    public function doRender($nav, $array_message) {

        $myNavajo = getNavajo($nav);
        $this->beforeRendering();
        $nodelist = $array_message->childNodes;
                
		# render header (ugly but hey it seems to work)		
        if ($nodelist->length > 0 && get_class($nodelist->item(1)) == 'DOMElement') {
            $element = $nodelist->item(1);
            $this->renderHeader($nav, $element);
        }
        # parse DOMXML and convert it to an array
        $data = array();
        $index = 0;
        for ($i = 0; $i < $nodelist->length; $i++) {
            if (get_class($nodelist->item($i)) == 'DOMElement') {
                $element = $nodelist->item($i);
                $propertylist = $element->childNodes;
                for ($j = 0; $j < $propertylist->length; $j++) {
                    if (get_class($propertylist->item($j)) == 'DOMElement') {
                        $property = $propertylist->item($j);
                        $array_key = $property->getAttribute('name');
                        $data[$index][$array_key] = $property->getAttribute('value');
                    }
                    $data[$index]["domIndex"] = $i;
                }
                $data[$index]["navajoIndex"] = $index;
                $index++;
            }
        }
		
		$msgName = $array_message->getAttribute('name');
        if (!isset ($_SESSION['ordering'][$nav][$msgName]["sortKey"])) {
             $_SESSION['ordering'][$nav][$msgName]["sortKey"] = '';
             $_SESSION['ordering'][$nav][$msgName]["sortDir"] = 'ASC';            
        } 
        if( $_SESSION['ordering'][$nav][$msgName]["sortKey"] != '' ) {
        	$sortedArray = new arraySort;
        	$sortedArray->aData = $data;
        	$sortedArray->aSortKey = $_SESSION['ordering'][$nav][$msgName]["sortKey"];
        	$sortedArray->aSortDir = $_SESSION['ordering'][$nav][$msgName]["sortDir"];     
            $sortedArray->sort();
        } else {
            $sortedArray->aData = $data;            
        }         
   
        for ($i = 0; $i < sizeof($sortedArray->aData); $i++) {
            $element = $nodelist->item($sortedArray->aData[$i]["domIndex"]);             
            $this->render($nav, $element);
        } 
        $this->afterRendering();
    }

    protected function afterRendering() {
    }

}
?>
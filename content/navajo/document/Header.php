<?php
class Header extends BaseNode {
    public $transactionNode;
    public $accessId;
    
    public function __construct($navajo) {
            parent::__construct($navajo);
            $this->transactionNode = new Transaction($navajo);
    }
    
    public function getTagName() {
        return 'header';
    }

    public function getChildren() {
        return array($this->transactionNode);
    }
        
    public function getAttribute($name) {
        return $this->accessId;
    }
    
    public function getAttributeNames() {
        return array('accessId');
    }
    
    public function getTextNode() {
        return null;
    }
    
    public function getCurrentService() {
        return $this->transactionNode->getCurrentService();
    }
    
    function setHeaderAttributes($user, $password, $service) {
        if($this->transactionNode==null) {
            $this->transactionNode = new Transaction($this->getRootDoc());
        }
        $this->transactionNode->setHeaderAttributes($user, $password, $service);
    }
    
    function getAccessId() {
        return $this->accessId;
    }
    
    public function getService() {
        return $this->transactionNode->getService();
    }
    
    public function parse($domNode) {
        $nodelist = $domNode->childNodes;
        $accessId = $domNode->getAttribute('accessId');
        $this->accessId = $accessId;

        for ($i = 0; $i < $nodelist->length; $i++) {
             $item = $nodelist->item($i);
             if (get_class($item) == 'DOMElement') {
                if($item->tagName == 'transaction') {
                    $rpc_usr = $item->getAttribute('rpc_usr');
                    $s = new Transaction($this->getRootDoc());
                    $s->parse($item);
                    $this->transactionNode = $s;
                }
            }
        }            
    }
}
?>

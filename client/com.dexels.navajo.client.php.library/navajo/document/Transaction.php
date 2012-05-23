<?php
class Transaction extends BaseNode {

    public $rpc_usr;
    public $rpc_pwd;
    public $rpc_name;
    public $currentService;

    public function getTagName() {
        return 'transaction';
    }

    public function getService() {
        return $this->rpc_name;
    }

    public function getChildren() {
        return array ();
    }

    public function getTextNode() {
        return null;
    }

    public function getAttributeNames() {
        return array (
            'rpc_usr',
            'rpc_pwd',
            'rpc_name'
        );
    }

    public function getCurrentService() {
        return $this->currentService;
    }

    public function getAttribute($n) {
        if ($n == 'rpc_usr') {
            return $this->rpc_usr;
        }
        if ($n == 'rpc_pwd') {
            return $this->rpc_pwd;
        }
        if ($n == 'rpc_name') {
            return $this->rpc_name;
        }
    }
    
    function setHeaderAttributes($user, $password, $service) {
        $this->rpc_usr = $user;
        $this->rpc_pwd = $password;
        $this->rpc_name = $service;
    }
    
    public function parse($domNode) {
        $this->rpc_usr = $domNode->getAttribute('rpc_usr');
        $this->rpc_pwd = $domNode->getAttribute('rpc_pwd');
        $this->rpc_name = $domNode->getAttribute('rpc_name');
        $this->currentService = $domNode->getAttribute('rpc_name');
    }

    public function __construct($root) {
        parent :: __construct($root);
    }
}
?>

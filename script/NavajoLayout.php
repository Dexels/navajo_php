<?php
abstract class NavajoLayout {
	abstract protected function render($nav,$message_element);
	
	protected function beforeRendering() {
	}
	
	public function doRender($nav,$array_message) {
		$this->beforeRendering();
		$nodelist = $array_message->childNodes;
	//	echo '# of child nodes: '.$nodelist->length.'<br/>';
		for ($i = 0; $i < $nodelist->length; $i++) {
		//	echo 'aap';
			if(get_class($nodelist->item($i))=='DOMElement') {
				$element = $nodelist->item($i);
				$this->render($nav,$element);
			} 
		}
		$this->afterRendering();
	}

	protected function afterRendering() {
	}
	
}
?>
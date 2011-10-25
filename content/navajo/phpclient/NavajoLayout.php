<?php
abstract class NavajoLayout {
    abstract protected function render($nav, $message_element, $params);

    protected function beforeRendering($nav, $params) {
    }

    public function doRender($nav, $amsg, $params) {
        $myNavajo = getNavajo($nav);
        $subm = $amsg->getSubMessages();
        if ($amsg->getArraySize() > 0) {
            $this->beforeRendering($nav, $params);
            $keys = array_keys($subm);
            $cc = count($keys);
            $elt = $subm[0];
            $this->renderHeader($nav, $elt, $params);
            foreach ($subm as $mm) {
                $this->render($nav, $mm, $params);
            }
            $this->renderFooter($nav, $amsg, $params);
            $this->afterRendering($nav, $params);
        } else {
	    if (isset($params["nietsgevonden"])) {
	       echo "<p>".$params["nietsgevonden"]."</p>";
	    } else {
              echo "<p>Niets gevonden..</p>";
            }
        }
    }

    protected function afterRendering($nav, $params) {
    }

}
?>

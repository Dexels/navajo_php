<?php
abstract class NavajoLayout {
    abstract protected function render($nav, $message_element, $params);

    protected function beforeRendering($params) {
    }

    public function doRender($nav, $amsg, $params) {
        $myNavajo = getNavajo($nav);
        $this->beforeRendering($params);
        $subm = $amsg->getSubMessages();
        if ($amsg->getArraySize() > 0) {
            $keys = array_keys($subm);
            $cc = count($keys);
            $elt = $subm[0];
            $this->renderHeader($nav, $elt, $params);
        }

        foreach ($subm as $mm) {
                $this->render($nav, $mm, $params);
        }
        $this->afterRendering($params);
    }

    protected function afterRendering($params) {
    }

}
?>

<?php

  class RenderService {

    private $destination = null;
    private $renderItems = null;

    public function __construct($templateName = null) {
      $this->renderItems = array();
      $this->setDest($templateName);
    }

    public function setDest($templateName) {
      $this->destination = __DIR__."/../../html/$templateName.html.template";
      return $this;
    }

    public function pushItem($marker, $value, $escapeFlg = false) {
      $this->renderItems[$this->phpMarker($marker)] = $escapeFlg ?
        htmlspecialchars($value, ENT_QUOTES, 'UTF-8') : $value;
      return $this;
    }

    public function render() {
      if ($this->destination == null || $this->renderItems == null) {
        $this->echoError('Elements required for rendering are not set.');
      }

      if (is_readable($this->destination)) {
        $fp = fopen($this->destination, 'r');
        $rp = $this->renderItems;
        while (!feof($fp)) {
          echo strtr(fgets($fp), $rp);
        }
        fclose($fp);
      } else {
        $this->echoError($this->destination.' is not readable.');
      }
    }

    public function phpMarker($name) {
      return '$$PHP-'.$name.'$$';
    }

    private function echoError($msg) {
      echo "<h1>RenderService ERROR!!</h1><p>$msg</p>";
    }
  }

?>

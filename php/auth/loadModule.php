<?php

  require '../../env.php';

  if (isset($DependentModules)) {
    if (isset($DependentModules['utils'])) {
      foreach ($DependentModules['utils'] as $module) {
        require '../utils/'.$module.'.php';
      }
    }
  }

?>

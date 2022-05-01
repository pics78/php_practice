<?php

  require_once __DIR__.'/../services/sessionService.php';

  $session = new SessionService();
  $session->destroy();

  header('Location: ../routes/login.php');

?>

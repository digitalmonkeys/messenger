<?php
if(!isset($controller))
{
  require_once 'classes/class_controller.php';
  $controller = new Controller();
  $controller->do_action($_POST);
}


?>
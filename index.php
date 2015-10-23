<?php
/*
 * How it works
 * index.php creates a new Controller and then calls invoke in controler/Controller.php
 */
session_start();
include_once 'model/Model.php';
include_once 'controller/Controller.php';
include_once 'shared/common.php';
include_once 'shared/database.php';

/*
  include error handler routine, used as:
  try {
      statement
  } 
  catch (Exception $e) {
      
  }
*/
set_error_handler('exceptions_error_handler');
function exceptions_error_handler($severity, $message, $filename, $lineno) {
  if (error_reporting() == 0) {
    return;
  }
  if (error_reporting() & $severity) {
    throw new ErrorException($message, 0, $severity, $filename, $lineno);
  }
}

$controller = new Controller();
$controller->invoke();
?>
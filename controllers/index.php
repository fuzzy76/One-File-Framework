<?php

class Index extends OFFController {

  function index() {
  
    $this->OFF->message['notice'] = 'The controller is f... working!';
    
    return array('b' => "Variable from controller", 'c' => var_export($_SERVER, TRUE));
  }
  
}
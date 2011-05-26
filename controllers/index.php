<?php

class Index extends OFFController {

  function index() {
  
    $this->OFF->message['notice'] = 'The controller is f... working!';

  }
  
  function __construct($OFF) {
    OFFController::__construct($OFF);
  }

}
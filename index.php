<?php

require('settings.php');

if (isset($settings['libs'])) {
  foreach ($settings['libs'] as $lib) {
    include "libs/$lib";
  }
}

$OFF = new OFF($settings);
$OFF->parseRequest();
$OFF->controller();
$OFF->view();

// End of execution

class OFF {

  public $router;
  public $theme = 'default';
  public $settings = array();
  public $message = array();
  
  function __construct($settings) {
    $this->router = (object) array(
      'controller' => 'index',
      'action' => 'index',
      'view_controller' => 'index',
      'view_action' => 'index',
      'calculated_view' => '',
      'args' => array(),
      'title' => 'OFF site',
    );
    $this->settings = (object) $settings;
    if (isset($this->settings->theme))
      $this->theme = $this->settings->theme;
    if (isset($this->settings->site_title))
      $this->router->title = $this->settings->site_title;
  }

  function parseRequest() {
    if (isset($_GET['p'])) {
      if (preg_match('|^[0-9a-zA-Z\/]*\z|',$_GET['p'])) {
        $router = explode('/', strtolower($_GET['p']), 2);
        $this->router->controller = $router[0];
        if (count($router) > 1)
          $this->router->action = $router[1];
        for ($i = 1; $i <= 9 ; $i++) {
          if (isset($_GET["arg$i"]))
            $this->args[$i] = $_GET["arg$i"];
        }
      }
      else {
        $this->router->controller = 'index';
        $this->router->action = 'error';
        $this->router->args = array(1 => 404);
      }
      $this->router->view_controller = $this->router->controller;
      $this->router->view_action = $this->router->action;
    }
  }

  function controller() {
    if (is_file("controllers/{$this->router->controller}.php")) {
      include "controllers/{$this->router->controller}.php";
      $classname = ucfirst($this->router->controller);
      $methodname = $this->router->action;
      $controller = new $classname($this);
      $controller->$methodname();
    }
  }

  function view() {
    // Check for view implementation
    if (is_file("themes/{$this->theme}/{$this->router->view_controller}/{$this->router->view_action}.php")) { // Current theme
      $this->router->calculated_view = "themes/{$this->theme}/{$this->router->view_controller}/{$this->router->view_action}.php";
    } elseif (is_file("themes/default/{$this->router->view_controller}/{$this->router->view_action}.php")) { // Default theme
      $this->router->calculated_view = "themes/default/{$this->router->view_controller}/{$this->router->view_action}.php";
    } elseif (is_file("themes/{$this->theme}/index/error.php")) { // 404 from current theme
      $this->router->calculated_view = "themes/{$this->theme}/index/error.php";
      $this->router->args = array(1 => 404);
    } elseif (is_file("themes/default/index/error.php")) { // 404 from default theme
      $this->router->calculated_view = "themes/default/index/error.php";
      $this->router->args = array(1 => 404);
    } else { // Time to panic
      $this->router->calculated_view = '';
      header('HTTP/1.0 404 Not Found');
      $this->message['error'] = "Neither page nor errorpage found.";
    }

    if (empty($this->theme)) { // No wireframe, do raw output
      $this->outputView();
      return;
    } elseif (is_file("themes/{$this->theme}/wireframe.php")) { // Check current theme for wireframe
      $wireframe = "themes/{$this->theme}/wireframe.php";
    } elseif (is_file("themes/default/wireframe.php")) { // Check default theme for wireframe
      $wireframe = "themes/default/wireframe.php";
    } else { // Time to panic
      header('HTTP/1.0 404 Not Found');
      die("No themes found.");
    }

    include $wireframe;
  }

  function outputView() {
    if (!empty($this->router->calculated_view))
      include $this->router->calculated_view;
  }

}

abstract class OFFController {
  public $OFF;
  function __construct($OFF) {
    $this->OFF = $OFF;
  }
}
<?php

require('settings.php');

$OFF = new OFF($settings);
$OFF->parseRequest();

if (isset($settings->libs)) {
  foreach ($settings->libs as $lib) {
    include "libs/$lib";
  }
}

$OFF->controller();
$OFF->view();

// End of execution

/**
 * The OFF class is the heart of the OFF framework.
 */
class OFF {

  /**
   * A stdobject containing details on how the current request is handled.
   * 
   * @var mixed
   * @access public
   */
  public $router;
  /**
   * The theme being used for output
   * 
   * @var string
   * @access public
   */
  public $theme = 'default';
  /**
   * The settings for the site as derived from settings.php (unchanged)
   * 
   * @var array
   * @access public
   */
  public $settings = array();
  /**
   * An array of messages meant for the user
   * 
   * @var array
   * @access public
   */
  public $message = array();

  /**
   * The root of OFF (in it's simplest form, '/')
   * 
   * @var mixed
   * @access public
   */
  public $site_root;

  /**
   * Constructor for the OFF class.
   * 
   * @access public
   * @param mixed $settings An array of settings for the site. 
   * @return void
   */
  function __construct($settings) {
    $this->router = (object) array(
      'controller' => 'index',
      'action' => 'index',
      'view_controller' => 'index',
      'view_action' => 'index',
      'calculated_view' => '',
      'args' => array(),
      'title' => 'OFF site',
      'variables' => array(),
    );
    $this->settings = $settings;

    if (isset($this->settings->theme))
      $this->theme = $this->settings->theme;
    if (isset($this->settings->site_title))
      $this->router->title = $this->settings->site_title;
    if (isset($this->settings->site_root)) {
      $this->site_root = $this->settings->site_root;
    } else {
      $this->site_root = $_SERVER['SCRIPT_NAME'];
    }
  }

  /**
   * Parses the current request.
   * 
   * @access public
   * @return void
   */
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

  /**
   * Invokes the controller/action.
   * 
   * @access public
   * @return void
   */
  function controller() {
    if (is_file("controllers/{$this->router->controller}.php")) {
      include "controllers/{$this->router->controller}.php";
      $classname = ucfirst($this->router->controller);
      $methodname = $this->router->action;
      $controller = new $classname($this);
      $controllerout = $controller->$methodname();
      if (is_array($controllerout))
      $this->router->variables = $controllerout;
    }
  }

  /**
   * Invokes the view (wrapping it in a wireframe). The wireframe is called with
   * $this pointing to the OFF object executing.
   * 
   * @access public
   * @return void
   */
  function view() {
    // Check for view implementations

    if (!empty($this->router->view_action) && is_file("themes/{$this->theme}/views/{$this->router->view_controller}/{$this->router->view_action}.php")) {
      // Current theme controller/action
      $this->router->calculated_view = "themes/{$this->theme}/views/{$this->router->view_controller}/{$this->router->view_action}.php";
    } elseif (!empty($this->router->view_action) && is_file("themes/default/views/{$this->router->view_controller}/{$this->router->view_action}.php")) {
      // Default theme controller/action
      $this->router->calculated_view = "themes/default/views/{$this->router->view_controller}/{$this->router->view_action}.php";
    } elseif (is_file("themes/{$this->theme}/views/{$this->router->view_controller}.php")) {
       // Current theme controller
      $this->router->calculated_view = "themes/{$this->theme}/views/{$this->router->view_controller}.php";
    } elseif (is_file("themes/default/views/{$this->router->view_controller}.php")) {
      // Default theme controller
      $this->router->calculated_view = "themes/default/views/{$this->router->view_controller}.php";
    } elseif (is_file("themes/{$this->theme}/views/index/error.php")) {
      // 404 from current theme
      $this->router->calculated_view = "themes/{$this->theme}/views/index/error.php";
      $this->router->args = array(1 => 404);
    } elseif (is_file("themes/default/views/index/error.php")) {
      // 404 from default theme
      $this->router->calculated_view = "themes/default/views/index/error.php";
      $this->router->args = array(1 => 404);
    } else {
      // Time to panic
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

  /**
   * Output the current view. This is called from within the wireframe. The view
   * has the router variables exposed as local variables as well as $this which
   * points to the OFF object currently executing.
   * 
   * @access public
   * @return void
   */
  function outputView() {
    if (!empty($this->router->calculated_view)) {
      extract($this->router->variables, EXTR_SKIP | EXTR_REFS);
      include $this->router->calculated_view;
    }
  }
  
  /**
   * Outputs the given fragment. The fragment has access to an OFF object as
   * $this, the provided variables as well as the router variables (in that
   * prioritized order).
   * 
   * @access public
   * @param mixed $fragment The id of the fragment to output
   * @param array $variables Optional array of variables to pass to the fragment.
   * @return void
   */
  function outputFragment($fragment, $variables = array()) {
    if (is_file("themes/{$this->theme}/fragments/{$fragment}.php")) {
      $fragmentfile = "themes/{$this->theme}/fragments/{$fragment}.php";
    } elseif (is_file("themes/default/fragments/{$fragment}.php")) {
      $fragmentfile = "themes/default/fragments/{$fragment}.php";
    } else {
      return FALSE;
    }
    extract($variables, EXTR_SKIP | EXTR_REFS);
    extract($this->router->variables, EXTR_SKIP | EXTR_REFS);
    include $fragmentfile;
  }

}


/**
 * The class all controllers needs to subclass. Currently this wrapper only
 * makes sure to store a referance to the OFF object that created us.
 * 
 * @abstract
 */
abstract class OFFController {
  public $OFF;
  function __construct() {
    global $OFF;
    $this->OFF = $OFF;
  }
}

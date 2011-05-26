<?php header('Content-Type: text/html; charset=utf-8');?><!DOCTYPE HTML>
<html>
<head>
  <title><?php echo $this->router->title;?></title>
  <meta http-equiv="Content-Type" content="text/html;charset=utf-8" >
  <!--[if lt IE 9]>
  <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->
  <link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/3.1.1/build/cssreset/reset-min.css" />
  <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js"></script>
  <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.13/jquery-ui.min.js"></script>
  <link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css">
  <script type="text/javascript" src="http://ajax.cdnjs.com/ajax/libs/modernizr/1.7/modernizr-1.7.min.js"></script>
  <script type="text/javascript" src="<?php echo "themes/{$this->theme}/script.js"; ?>"></script>
  <link rel="stylesheet" type="text/css" href="<?php echo "themes/{$this->theme}/stylesheet.css"; ?>">
  <link rel="shortcut icon" href="favicon.ico"><!-- Some clients look in the root folder any way, might as well put it there -->
  <link rel="icon" href="favicon.ico">
  <meta name="viewport" content="width=800; user-scalable=1;"/>
</head>
<body class="<?php echo "controller-{$this->router->view_controller} action-{$this->router->view_action} routed-{$this->router->view_controller}-{$this->router->view_action} ";?> no-js">
  <header>
    <h1><a href="<?php echo $this->settings->site_root; ?>"><?php echo $this->settings->site_title; ?></a></h1>
    <nav><button class="controller-default">Dashboard</button><button class="controller-admin">Admin</button><button>Log out</button></nav>
  </header>
  <?php if (isset($this->message['error'])) { ?>
    <div class="ui-state-error msgbox"><?php echo $this->message['error']; ?></div>
  <?php } ?>
  <?php if (isset($this->message['notice'])) { ?>
    <div class="ui-state-highlight msgbox"><?php echo $this->message['notice']; ?></div>
  <?php } ?>
  <section id="content">
    <?php $this->outputView(); ?>
  </section>
  <footer>
    Made by Håvard Pedersen. Fork me baby. Requires a good browser. Your mileage may vary.
  </footer>
</body>
</html>

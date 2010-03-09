<?php
Router::connectNamed(array(), array('argSeparator' => '~'));

// mobile only のroutingはここに書きます
Router::connect('/m/copyright', array('controller' => 'copyright', 'action' => 'index', 'prefix' => 'mobile'));

// root_controller.php を作ろう
Router::connect('/', array('controller' => 'root', 'action' => 'index'));
Router::connect('/m', array('controller' => 'root', 'action' => 'index', 'prefix' => 'mobile'));

// PC向けのControllerは全部書かないとまずいかも
Router::connect('/users/:action', array('controller' => 'users'));
// Router::connect('/pccontroller/:action', array('controller' => 'pccontroller'));

Router::connect('/m/:controller/:action/*', array('prefix' => 'mobile'));
?>

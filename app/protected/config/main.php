<?php

return array(
	'name'=>'Cinemania Server',
	'defaultController'=>'devices',
	
	// preloading 'log' component
	'preload'=>array('log'),
	
	// autoloading model and component classes
  'import'=>array(
    'application.models.*',
    'application.components.*',
    'application.helpers.*',
  ),
  
  // generator
  'modules'=>array(
        'gii'=>array(
            'class'=>'system.gii.GiiModule',
            'password'=>'test',
        ),
    ),
  
  
	'components'=>array(
	  // rooter
		'urlManager'=>array(
			'urlFormat'=>'path',
			'rules'=>array(
				'gii'=>'gii',
            'gii/<controller:\w+>'=>'gii/<controller>',
            'gii/<controller:\w+>/<action:\w+>'=>'gii/<controller>/<action>',
			),
		),
		
    // db
    'db'=>array(
      'connectionString' => 'mysql:host=localhost;dbname=cinemania',
      'emulatePrepare' => true,
      'username' => 'cinemania',
      'password' => 'kdl5Opc',
      'charset' => 'utf8',
    ),
    'errorHandler'=>array(
      'errorAction'=>'devices/error',
    ),
    'log'=>array(
      'class'=>'CLogRouter',
      'routes'=>array(
        array(
          'class'=>'CFileLogRoute',
          'levels'=>'error, warning',
        ),
        // uncomment the following to show log messages on web pages
        array(
          'class'=>'CWebLogRoute',
        ),
      ),
    ),
    /*'cache'=>array(
      'class'=>'system.caching.CFileCache',
      'cachePath'=>'cache'      
    ),*/
	),

);
<?php
return CMap::mergeArray(
    require(dirname(__FILE__).'/main.php'), 
    array(
        'components'=>array(
            'db'=>array(
            'connectionString' => 'mysql:host=localhost;dbname=cinemania_test',
            'emulatePrepare' => true,
            'username' => 'cinemania_test',
            'password' => 'kdl5Opc',
            'charset' => 'utf8',
          )
        ),
    )
);
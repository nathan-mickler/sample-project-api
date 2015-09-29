<?php
	use Illuminate\Database\Capsule\Manager as Capsule;  

	$capsule = new Capsule; 

	$capsule->addConnection([
		'driver' => 'sqlite',
		'database' => __DIR__ . '/database.sqlite',
		'prefix' => ''
	]);

	// $capsule->addConnection([
	// 	'driver'    => 'mysql',
 //    'host'      => 'localhost',
 //    'database'  => 'acapella',
 //    'username'  => 'acapella',
 //    'password'  => 'Dr@g0nRac3r!@',
 //    'charset'   => 'utf8',
 //    'collation' => 'utf8_unicode_ci',
 //    'prefix'    => ''
	// ]);

	$capsule->setAsGlobal();
	$capsule->bootEloquent();
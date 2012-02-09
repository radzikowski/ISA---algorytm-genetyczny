<?php 

require_once __DIR__.'/vendor/Silex/silex.phar';

require_once __DIR__.'/src/lib/Algorithms.php';
require_once __DIR__.'/src/lib/Crossings.php';
require_once __DIR__.'/src/lib/Mutations.php';
require_once __DIR__.'/src/lib/Selections.php';

require_once __DIR__.'/src/index.php';

$app = new Silex\Application();
//$app['debug'] = true;
// $app['autoloader']->registerNamespaces(array(
// 	'ISA' => __DIR__.'/src',
// ));

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path'       => __DIR__.'/resources/views',
    'twig.class_path' => __DIR__.'/vendor/twig/lib',
));

$app->before(function () use ($app) {
	$app['twig']->addGlobal('base', $app['twig']->loadTemplate('base.html.twig'));
});


$app->get('/lab1', function () use ($app) {

	$laboratorium = new ISA\Laboratorium();
	$result = $laboratorium->lab1($app);	
	
	return $app['twig']->render(
		'lab1.html.twig', 
		$result
	);
});


$app->get('/lab2', function () use ($app) {

	$laboratorium = new ISA\Laboratorium();
	$result = $laboratorium->lab2($app);	
 
	return $app['twig']->render(
		'lab2.html.twig', 
		$result		
	);
});

$app->get('/lab3', function () use ($app) {

	$laboratorium = new ISA\Laboratorium();
	$result = $laboratorium->lab3($app);	
 
	return $app['twig']->render(
		'lab3.html.twig', 
		$result		
	);
});

$app->get('/lab4', function () use ($app) {

	return $app['twig']->render('lab4.html.twig');
});

$app->get('/lab5', function () use ($app) {

	$laboratorium = new ISA\Laboratorium();
	$result = $laboratorium->lab5($app);	
 
	return $app['twig']->render(
		'lab5.html.twig', 
		$result		
	);
});



$app->get('/', function () use ($app) {
    return $app['twig']->render('isa.html.twig', array());
});

$app->run();
?>
<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/lib/bootstrap.php';

$template_path = __DIR__ . '/templates';

$twig = new Twig_Environment(new Twig_Loader_Filesystem($template_path), array(
    'cache' => false
));

$systemcheck = new Systemcheck_Environment();
$tests       = $systemcheck->executeTestGroup('Shop4');
$platform    = new Systemcheck_Platform_Hosting();

header('Content-Type: text/html; charset=utf-8');

echo $twig->render('systemcheck.html.twig', array(
    'passed'   => $systemcheck->getIsPassed(),
    'tests'    => $tests,
    'platform' => $platform
));

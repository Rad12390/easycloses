<?php

use Symfony\Component\HttpFoundation\Request;

umask(0002);

// Use APC for autoloading to improve performance.
// Change 'sf2' to a unique prefix in order to prevent cache key conflicts
// with other applications also using APC.
/*
  $loader = new ApcClassLoader('sf2', $loader);
  $loader->register(true);
 */

/** @var \Composer\Autoload\ClassLoader $loader */
$loader = require __DIR__.'/../app/autoload.php';
//require_once __DIR__.'/../app/AppCache.php';

$kernel   = new AppKernel('prod', false);
$kernel->loadClassCache();
//$kernel = new AppCache($kernel);
$request  = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);

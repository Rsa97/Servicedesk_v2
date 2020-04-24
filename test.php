<?php
header("Content-Type: text/html;charset=utf-8");
require_once('vendor/Autoloader.php');

try {
    $pair = \Backend\API\Auth::auth([
        'login' => 'rakhmatulin_s',
        'password' => 'Smeag0rl'
    ]);
    var_dump($pair);

    var_dump(\Backend\API\Auth::validate(['tok' => $pair['tok']]));

    $newPair = \Backend\API\Auth::refresh(['ref' => $pair['ref']]);
    var_dump($newPair);

    $newPair = \Backend\API\Auth::refresh(['ref' => $pair['ref']]);
    var_dump($newPair);
} catch (\Exception $e) {
    print $e->getCode() . ': ' . $e->getMessage();
}

<?php

require_once('vendor/Autoloader.php');

try {
    $test = \Backend\ORM\Contract::getDescription();
    var_dump($test);
} catch (\Exception $e) {
    print $e->getMessage();
}

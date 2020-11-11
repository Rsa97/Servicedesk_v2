<?php

require_once('../../Autoloader.php');

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Content-Type: application/json; charset=utf-8');

function getClassesList() : array
{
    $list = [];
    $dir = opendir('../ORM');
    while ($module = readdir($dir)) {
        $class = explode('.', $module)[0];
        $fullClass = '\\Backend\\API\\' . $class;
        if (class_exists($fullClass) && 'Backend\\API\\Entity' == get_parent_class($fullClass)) {
            $list[] = $class;
        }
    }
    return $list;
}

if ('list' === $_GET['class']) {
    echo json_encode(getClassesList(), JSON_UNESCAPED_UNICODE);
    exit;
}

if (!in_array($_GET['class'], getClassesList())) {
    exit;
}

$class = '\\Backend\\API\\' . $_GET['class'];
echo json_encode($class::getDescription(), JSON_UNESCAPED_UNICODE);

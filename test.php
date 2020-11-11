<?php
header("Content-Type: text/html;charset=utf-8");
require_once('vendor/Autoloader.php');

$request = \Backend\API\PlannedRequest::getById(4);
var_dump($request);
var_dump($request->division->users);
var_dump($request->division->contract->users);

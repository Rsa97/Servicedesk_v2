<?php
namespace Backend\EntryPoint;

require_once('../../Autoloader.php');

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

if (explode(';', $_SERVER['CONTENT_TYPE'] ?? '')[0] !== 'application/json' || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    \Backend\Common\JsonRPC::error(null, -32600, 'Invalid Request');
}

try {
    $request = ['id' => null];
    $request = \Backend\Common\JsonRPC::parse(file_get_contents('php://input'));

    if (!preg_match('/^\w+::\w+$/', $request['method'])) {
        throw new \Exception('Method Not Found', -32601);
    }
    list($class, $method) = explode('::', '\\Backend\\API\\' . $request['method']);
    if (!class_exists($class) || !in_array($method, get_class_methods($class))) {
        throw new \Exception('Method Not Found', -32601);
    }

    $payload = [];
    if ($request['method'] !== 'Auth::auth' && $request['method'] !== 'Auth::refresh') {
        $payload = \Backend\API\Auth::validate($request['params']);
    }
    $result= $class::$method($request['params'], $payload);
    \Backend\Common\JsonRPC::response($request['id'], $result);
} catch (\PDOException $e) {
    \Backend\Common\JsonRPC::error($request['id'], -32200, 'Database Error ' . $e->getCode() . ': ' . $e->getMessage());
} catch (\Exception $e) {
    \Backend\Common\JsonRPC::error($request['id'], $e->getCode(), $e->getMessage());
}

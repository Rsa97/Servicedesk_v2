<?php
namespace Backend\Common;

class JsonRPC
{
    public static function error($id, int $code, string $message)
    {
        header('Content-Type: application/json;charset=utf-8');
        echo json_encode(
            [
                'jsonrpc' => '2.0',
                'id' => $id,
                'error' => [
                    'code' => $code,
                    'message' => $message
                ]
            ],
            JSON_UNESCAPED_UNICODE
        );
    }

    public static function response($id, $result)
    {
        header('Content-Type: application/json;charset=utf-8');
        echo json_encode(
            [
                'jsonrpc' => '2.0',
                'id' => $id,
                'result' => $result
            ],
            JSON_UNESCAPED_UNICODE
        );
    }

    public static function parse($rawRequest)
    {
        $request = json_decode($rawRequest, true);
        if ($request === null) {
            throw new \Exception('Parse Error', -32600);
        }
        if ((($request['jsonrpc'] ?? '') !== '2.0')
            || !array_key_exists('id', $request)
            || !array_key_exists('method', $request)
            || !array_key_exists('params', $request)) {
            throw new \Exception('Invalid Request', -32700);
        }
        return $request;
    }
}

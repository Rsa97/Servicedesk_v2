<?php
namespace Backend\API;

class Auth
{
    private static function tokenPair(\Backend\API\User $user) : array
    {
        $payload = [
            'user' => $user->id,
            'rights' => $user->rights,
            'name' => $user->shortName,
            'partner' => $user->partnerId
        ];
        $token = new \Backend\Common\JWT($payload);
        $refreshToken = new \Backend\Common\JWT(['user' => $user->id], \Backend\Common\JWT::TYPE_REFRESH);
        $refreshToken->store($user->id);
        return [
            'jwt' => $token->string,
            'ref' => $refreshToken->string
        ];
    }

    public static function auth(array $params) : array
    {
        $login = trim($params['login'] ?? '');
        $password = trim($params['password'] ?? '');
        if ($login === '' || $password === '') {
            throw new \Exception('Incorrect login or password', -36001);
        }
        $user = \Backend\API\User::getByLogin($login);
        if (null == $user) {
            throw new \Exception('Incorrect login or password', -36001);
        } else {
            if (!$user->checkAuth($password)) {
                throw new \Exception('Incorrect login or password', -36001);
            }
        }
        return self::tokenPair($user);
    }

    public static function refresh($params) : array
    {
        $refreshToken = \Backend\Common\JWT::parse($params['ref'] ?? '');
        if ($refreshToken == null) {
            throw new \Exception('Refresh token invalid or expired', -36002);
        }
        switch ($refreshToken->state()) {
            case \Backend\Common\JWT::NOT_EXISTS:
            case \Backend\Common\JWT::EXPIRED:
                throw new \Exception('Refresh token invalid or expired', -36002);
                break;
            case \Backend\Common\JWT::USED:
                \Backend\Common\JWT::block($refreshToken->payload['user']);
                throw new \Exception('Refresh token already used', -36003);
        }
        $refreshToken->markUsed();
        $user = \Backend\API\User::getById($refreshToken->payload['user']);
        if ($user === null || $user->disabled) {
            throw new \Exception('Incorrect login or password', -36001);
        }
        return self::tokenPair($user);
    }

    public static function validate(array $params) : array
    {
        $token = \Backend\Common\JWT::parse($params['jwt'] ?? '');
        if ($token === null || $token->isExpired()) {
            throw new \Exception('Token invalid or expired', -36004);
        }
        return $token->payload;
    }

    public static function checkAuth() : string
    {
        return 'ok';
    }
}

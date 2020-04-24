<?php
namespace Backend\Common;

class JWT
{
    public const SIGN_HS256 = 'HS256';
    public const SIGN_HS384 = 'HS384';
    public const SIGN_HS512 = 'HS512';
    public const SIGN_RS256 = 'RS256';
    public const SIGN_RS384 = 'RS384';
    public const SIGN_RS512 = 'RS512';

    private const SIGNS = [
        \Backend\Common\JWT::SIGN_HS256 => ['sign' => 'signHMAC', 'verify' => 'verifyHMAC', 'algo' => 'sha256'],
        \Backend\Common\JWT::SIGN_HS384 => ['sign' => 'signHMAC', 'verify' => 'verifyHMAC', 'algo' => 'sha384'],
        \Backend\Common\JWT::SIGN_HS512 => ['sign' => 'signHMAC', 'verify' => 'verifyHMAC', 'algo' => 'sha512'],
        \Backend\Common\JWT::SIGN_RS256 => ['sign' => 'signRSA', 'verify' => 'verifyRSA', 'algo' => 'sha256'],
        \Backend\Common\JWT::SIGN_RS384 => ['sign' => 'signRSA', 'verify' => 'verifyRSA', 'algo' => 'sha384'],
        \Backend\Common\JWT::SIGN_RS512 => ['sign' => 'signRSA', 'verify' => 'verifyRSA', 'algo' => 'sha512']
    ];

    public const TYPE_INFINITE = 'inf';
    public const TYPE_MAIN = 'tok';
    public const TYPE_REFRESH = 'ref';
    public const TYPES = [
        \Backend\Common\JWT::TYPE_INFINITE, \Backend\Common\JWT::TYPE_MAIN, \Backend\Common\JWT::TYPE_REFRESH
    ];

    public const OK = 0;
    public const NOT_EXISTS = 1;
    public const USED = 2;
    public const EXPIRED= 3;

    private $header;
    private $payload;

    private static function signHMAC(string $data, string $algo) : string
    {
        return hash_hmac($algo, $data, \Backend\Config\JWT::HMAC_KEY, true);
    }

    private static function verifyHMAC(string $data, string $sign, string $algo) : bool
    {
        return hash_hmac($algo, $data, \Backend\Config\JWT::HMAC_KEY, true) === $sign;
    }

    private static function signRSA(string $data, string $algo) : string
    {
        $sign = '';
        openssl_sign($data, $sign, \Backend\Config\JWT::RSA_PRIVATE_KEY, $algo);
        return $sign;
    }

    private static function verifyRSA(string $data, string $sign, string $algo) : string
    {
        openssl_verify($data, $sign, \Backend\Config\JWT::RSA_PUBLIC_KEY, $algo);
        return $sign;
    }

    private static function base64UrlEncode(string $data) : string
    {
        $b64 = base64_encode($data);
        if ($b64 === false) {
            return false;
        }
        return rtrim(strtr($b64, '+/', '-_'), '=');
    }

    private static function base64UrlDecode(string $data) : string
    {
        return base64_decode(strtr($data, '-_', '+/'));
    }

    public function __construct(
        array $payload,
        string $type = \Backend\Common\JWT::TYPE_MAIN,
        string $signMethod = \Backend\Common\JWT::SIGN_RS512
    ) {
        assert(in_array($type, self::TYPES));
        assert(in_array($signMethod, array_keys(self::SIGNS)));
        $this->header = [
            'alg' => $signMethod,
            'typ' => 'JWT',
            'tgt' => $type,
            'rnd' => bin2hex(random_bytes(8))
        ];
        switch ($type) {
            case self::TYPE_INFINITE:
                break;
            case self::TYPE_REFRESH:
                $this->header['exp'] = time() + \Backend\Config\JWT::REFRESH_TOKEN_VALIDITY;
                break;
            default:
                $this->header['exp'] = time() + \Backend\Config\JWT::TOKEN_VALIDITY;
        }
        $this->payload = $payload;
    }

    public static function parse(string $jwt) : ?\Backend\Common\JWT
    {
        $parts = explode('.', $jwt);
        if (count($parts) !== 3) {
            return null;
        }
        list($hdrb64, $pldb64, $signb64) = $parts;
        $header = json_decode(self::base64UrlDecode($hdrb64), true);
        if (($header['typ'] ?? '') !== 'JWT' || !in_array($header['alg'] ?? '', array_keys(self::SIGNS))) {
            return null;
        }
        $signer = self::SIGNS[$header['alg']];
        if (!self::{$signer['verify']}($hdrb64 . '.' . $pldb64, self::base64UrlDecode($signb64), $signer['algo'])) {
            return null;
        }
        $token = new self(json_decode(self::base64UrlDecode($pldb64), true), $header['tgt'], $header['alg']);
        $token->header = $header;
        return $token;
    }

    public function store(string $userId) : void
    {
        if ($this->header['tgt'] !== self::TYPE_REFRESH) {
            return;
        }
        $db = DB::get();
        $sql = 'INSERT INTO `jwt` (`user_guid`, `refresh_token`, `exp`, `used`) '
            .   'VALUES (UNHEX(:userId), :token, FROM_UNIXTIME(:expired), 0)';
        $req = $db->prepare($sql);
        $req->execute([
            'userId' => $userId,
            'token' => $this->string,
            'expired' => $this->header['exp']
        ]);
    }

    public function __get(string $name)
    {
        switch ($name) {
            case 'header':
                return $this->header;
            case 'payload':
                return $this->payload;
            case 'string':
                $hdrb64 = self::base64UrlEncode(json_encode($this->header, JSON_UNESCAPED_UNICODE));
                $pldb64 = self::base64UrlEncode(json_encode($this->payload, JSON_UNESCAPED_UNICODE));
                $data = $hdrb64 . '.' . $pldb64;
                $signer = self::SIGNS[$this->header['alg']];
                $sign = self::base64UrlEncode(self::{$signer['sign']}($data, $signer['algo']));
                return $data . '.' . $sign;
            default:
                return null;
        }
    }

    public static function block(string $userId) : void
    {
        $db = DB::get();
        $sql = 'DELETE FROM `jwt` WHERE `user_guid` = UNHEX(:userId)';
        $req = $db->prepare($sql);
        $req->execute([
            'userId' => $userId
        ]);
    }

    public function isExpired() : bool
    {
        return (array_key_exists('exp', $this->header) && $this->header['exp'] < time());
    }

    public function state() : int
    {
        $db = DB::get();
        $sql = 'SELECT `used` FROM `jwt` WHERE `refresh_token` = :token';
        $req = $db->prepare($sql);
        $req->execute(['token' => $this->string]);
        if ($row = $req->fetch(\PDO::FETCH_ASSOC)) {
            if ($row['used'] == 1) {
                return \Backend\Common\JWT::USED;
            }
        } else {
            return \Backend\Common\JWT::NOT_EXISTS;
        }
        if ($this->isExpired()) {
            return \Backend\Common\JWT::EXPIRED;
        }
        return \Backend\Common\JWT::OK;
    }

    public function markUsed() : void
    {
        $db = DB::get();
        $sql = 'UPDATE `jwt` SET `used` = 1 WHERE `refresh_token` = :token';
        $req = $db->prepare($sql);
        $req->execute(['token' => $this->string]);
    }
}

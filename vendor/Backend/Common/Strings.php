<?php
namespace Backend\Common;

class Strings
{
    public static function preformat(string $text) : string
    {
        $str = preg_replace('/ +/u', ' ', $text);
        $str = preg_replace('/[\x0D\x0A]+/u', "\n", $str);
        return $str;
    }

    public static function gudiWithDashes(string $guid) : string
    {
        if (preg_match(
            '/^([0-9a-f]{8})([0-9a-f]{4})([0-9a-f]{4})([0-9a-f]{4})([0-9a-f]{12})$/',
            mb_strtolower($guid),
            $parts
        )) {
            return "{$parts[1]}-{$parts[2]}-{$parts[3]}-{$parts[4]}-{$parts[5]}";
        }
        return $guid;
    }
}

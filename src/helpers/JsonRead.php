<?php
namespace YavlPhp\Helpers;

abstract class JsonRead {
    public static function from(string $path){
        $content = file_get_contents($path);
        return json_decode($content, true);
    }
}
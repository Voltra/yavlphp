<?php
namespace YavlPhp\Helpers;


class Regex{
    protected $re;

    public function __construct(string $re){
        $this->re = $re;
    }

    public function appliesTo(string $subject) : bool{
        return preg_match($this->re, $subject) === 1;
    }

    public function extractGroupsFrom(string $subject) : array{
        $matches = [];
        preg_match($this->re, $subject, $matches);
        return $matches;
    }
}
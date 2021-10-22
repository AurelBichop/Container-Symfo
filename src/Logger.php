<?php

namespace App;

class Logger{
    public function log(string $message)
    {
        var_dump("LOGGER : $message");
    }
}
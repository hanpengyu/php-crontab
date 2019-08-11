<?php

class Worker
{
    public function __construct()
    {
        global $argv;
        $action = isset($argv[1]) ? $argv[1] : 'run';
        if (method_exists($this, $action . 'Action')) {
            $actionName = $action . 'Action';
            call_user_func([$this, $action.'Action']);
        }
    }

    public function runAction()
    {
        echo 'this is default action.' . PHP_EOL;
    }

    public function echoAction()
    {
        echo 'this is echo Action.' . PHP_EOL;
    }
}

$worker = new Worker();
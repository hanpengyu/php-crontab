<?php

class Worker
{
    const LOG_FILE = '/tmp/worker/worker.log';

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
        $log = 'this is default action.' . PHP_EOL;
        echo $log;
        file_put_contents(self::LOG_FILE, $log, FILE_APPEND);
    }

    public function echoAction()
    {
        $log = 'this is echo Action.' . PHP_EOL;
        echo $log;
        file_put_contents(self::LOG_FILE, $log, FILE_APPEND);
    }
}

$worker = new Worker();
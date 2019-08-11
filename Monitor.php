<?php

class Monitor
{
    const PHP_BIN = '/usr/bin/php';
    const BASE_PATH = '/home/vagrant/code/sjb/crontab/';

    public $crontabList;

    public function __construct($config)
    {
        $this->crontabList = $config;
        $this->run();
    }

    public function run()
    {
        if (empty($this->crontabList)) {
            exit();
        }

        foreach ($this->crontabList as $crontab) {

            // todo 需要判断是否到了执行的时候

            $command = $this->getCommandUrl($crontab['controllerAction']);
            if (Tool::isRun($command)) {
                echo 'pids is already ' . implode(',', $pids) . PHP_EOL;
                exit();
            }
            shell_exec($command);
        }
    }

    private function getCommandUrl($action)
    {
        list($fileName, $actionName) = explode(',', $action);
        return sprintf('%s %s %s %s', self::PHP_BIN, self::BASE_PATH, $fileName, $actionName);
    }
}

$config = require './config/crontab.php';
$Monitor = new Monitor($config);
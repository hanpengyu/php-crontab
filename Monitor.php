<?php

require './Tool.php';

class Monitor
{
    const PHP_BIN = '/usr/bin/php';
    const BASE_PATH = '/home/vagrant/code/sjb/crontab/';

    public $crontabList;

    public function __construct($config)
    {
        // todo 本身也要保持只有一个进程
        $this->crontabList = $config;
        $this->run();
    }

    public function run()
    {
        if (empty($this->crontabList)) {
            exit();
        }

        foreach ($this->crontabList as $crontab) {
            $command = $this->getCommandUrl($crontab['controllerAction']);

            // 检查某时间($time)是否符合某个corntab时间计划($str_cron)
            $check = Tool::checkCrontab($crontab['crontabTime']);
            if (!$check) {
                $log = [$crontab['crontabTime'] . ' '.$command, 'crontab 不执行'];
                $log = Tool::log($log);
                echo $log;
                continue;
            }

            if (Tool::isRun($command)) {
                $str = 'pids is already ' . implode(',', $pids);
                $log = Tool::log($str);
                echo $log;
                continue;
            }
            $shellResult = shell_exec($command);
            echo $shellResult;
        }
    }

    private function getCommandUrl($action)
    {
        list($fileName, $actionName) = explode(',', $action);
        return sprintf('%s %s%s %s', self::PHP_BIN, self::BASE_PATH, $fileName, $actionName);
    }
}

$config = require './config/crontab.php';
$Monitor = new Monitor($config);
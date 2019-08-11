<?php

class Tool
{
    // 脚本是否运行
    public static function isRun($command)
    {
        $pids = [];
        $shell = "ps axu | grep -v grep | grep '{$command}' | awk '{print$2}'";
        $shellResult = shell_exec($shell);
        if ($shellResult) {
            $list = explode("\n", $shellResult);
            $list = array_filter($list);
            foreach ($list as $pid) {
                $pids[] = trim($pid);
            }
        }
        return $pids;
    }

    public static function checkCrontab()
    {
        return false;
    }

    public static function log($logList)
    {
        $logString = '[' . date('Y-m-d H:i:s') . ']';
        if (is_array($logList)) {
            foreach ($logList as $log) {
                $logString .= '[' . $log . ']';
            }
        } else {
            $logString .= '[' . $log . ']';
        }
        $logString = $logString . PHP_EOL;
        file_put_contents('/tmp/monitor.log', $logString, FILE_APPEND);
        return $logString;
    }
}
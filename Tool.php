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
}
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

    /**
     * 检查某时间($time)是否符合某个corntab时间计划($crontabString)
     * @param $crontabString crontab字符串
     * @return bool
     */
    public static function checkCrontab($strCron)
    {
        $time = time();
        $formatTime = explode('-', date('i-G-j-n-w', $time));
        return self::formatTimeCheck($formatTime, $strCron);
    }

    public static function formatTimeCheck($formatTime, $strCron)
    {
        $formatCrontab = self::format_crontab($strCron);
        if (!is_array($formatCrontab)) {
            return $formatCrontab;
        }
        return self::formatCheck($formatTime, $formatCrontab);
    }

    public static function formatCrontab($strCron)
    {
        // 格式检查
        $strCron = trim($strCron);
        $preg = '#^((\*(/\d+)?|((\d+(-\d+)?)(?3)?)(,(?4))*))( (?2)){4}$#';
        if (!preg_match($preg, $strCron)) {
            return '格式错误';
        }

        try {
            $arrCron = [];
            $parts = explode(' ', $strCron);
            $arrCron[0] = self::parseCronPart($parts[0], 0, 59);    // 分
            $arrCron[1] = self::parseCronPart($parts[1], 0, 59);    // 时
            $arrCron[2] = self::parseCronPart($parts[2], 1, 31);    // 日
            $arrCron[3] = self::parseCronPart($parts[3], 1, 12);    // 月
            $arrCron[4] = self::parseCronPart($parts[4], 0, 6);     // 周 0：周日
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return $arrCron;
    }

    private static function parseCronPart($part, $f_min, $f_max)
    {
        $list = [];

        // 处理","，列表
        if (false !== strpos($part, ',')) {
            $arr = explode(',', $part);
            foreach ($arr as $v) {
                $tmp = self::parseCronPart($v, $f_min, $f_max);
                $list = array_merge($list, $tmp);
            }
            return $list;
        }

        // 处理"/"， 间隔
        $tmp = explode('/', $part);
        $part = $tmp[0];
        $step = isset($tmp[1]) ? $tmp[1] : 1;

        //处理"-" -- 范围
        if (false !== strpos($part, '-')) {
            list($min, $max) = explode('-', $part);
            if ($min > $max) {
                throw new Exception('使用"-"设置范围时，左不能大于右');
            }
        } elseif ('*' == $part) {
            $min = $f_min;
            $max = $f_max;
        } else {//数字
            $min = $max = $part;
        }

        //空数组表示可以任意值
        if ($min == $f_min && $max == $f_max && $step == 1) {
            return $list;
        }

        //越界判断
        if ($min < $f_min || $max > $f_max) {
            throw new Exception('数值越界。应该：分0-59，时0-59，日1-31，月1-12，周0-6');
        }

        return $max - $min > $step ? range($min, $max, $step) : array((int)$min);
    }

    public static function formatCheck($formatTime, $formatCron)
    {
        return (!$formatCron[0] || in_array($formatTime[0], $formatCron[0]))
            && (!$formatCron[1] || in_array($formatTime[1], $formatCron[1]))
            && (!$formatCron[2] || in_array($formatTime[2], $formatCron[2]))
            && (!$formatCron[3] || in_array($formatTime[3], $formatCron[3]))
            && (!$formatCron[4] || in_array($formatTime[4], $formatCron[4]));
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
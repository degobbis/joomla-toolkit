<?php

class Modules_JoomlaToolkit_CallSbinWrapper
{
    /**
     * @param string $phar
     * @param array $args
     * @return string
     * @throws pm_Exception
     */
    public static function callPhar($phar, array $args = [])
    {
        // TODO: use correct PHP version and user
        $result = pm_ApiCli::callSbin($phar, $args);
        return static::_processResult($result);
    }

    public static function callPhp($file, array $args = [])
    {
        // TODO: use correct PHP version and user
        $result = pm_ApiCli::callSbin('php', array_merge([$file], $args));
        return static::_processResult($result);
    }

    private static function _processResult($result)
    {
        if (0 != $result['code']) {
            throw new Modules_JoomlaToolkit_Exception_UtilityException($result['stdout'] . $result['stderr']);
        }
        return $result['stdout'];
    }
}
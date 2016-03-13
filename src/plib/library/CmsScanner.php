<?php

class Modules_JoomlaToolkit_CmsScanner
{
    const NAME_JOOMLA = 'Joomla';

    /**
     * @param string$vhost
     */
    public static function scanVhost($vhost)
    {
        $fileManager = new pm_ServerFileManager();
        try {
            $resultFile = tempnam(pm_Context::getVarDir(), 'result_');
            Modules_JoomlaToolkit_CallSbinWrapper::callPhar('cmsscanner.phar', [
                'cmsscanner:detect',
                '--report=' . $resultFile,
                '--versions',
                static::getAbsoluteVhostPath($vhost),
            ]);
            $resultJson = $fileManager->fileGetContents($resultFile);
            return json_decode($resultJson, true);
        } finally {
            $fileManager->removeFile($resultFile);
        }
    }

    public static function getAbsoluteVhostPath($vhost)
    {
        // TODO: support configurable vhosts directory
        return '/var/www/vhosts/' . $vhost;
    }
}
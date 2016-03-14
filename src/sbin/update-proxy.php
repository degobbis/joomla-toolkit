#!/opt/plesk/php/5.6/bin/php
<?php
$output = null;
$returnCode = null;
$command = '/usr/local/psa/admin/bin/php ';
for ($i = 1; $i < count($argv); $i++) {
    $command .= $argv[$i] . ' ';
}
exec($command, $output, $returnCode);
echo implode("\n", $output);
exit($returnCode);
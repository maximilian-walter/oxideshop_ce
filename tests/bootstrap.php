<?php

use \OxidEsales\Eshop\Core\Registry;
use \OxidEsales\Eshop\Core\ConfigFile;

define('INSTALLATION_ROOT_PATH', __DIR__ . DIRECTORY_SEPARATOR . '..');
define('OX_BASE_PATH', INSTALLATION_ROOT_PATH . DIRECTORY_SEPARATOR . 'source' . DIRECTORY_SEPARATOR);
define('VENDOR_PATH', INSTALLATION_ROOT_PATH . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR);
require VENDOR_PATH . "autoload.php";
require OX_BASE_PATH . "oxfunctions.php";
require OX_BASE_PATH . "overridablefunctions.php";

doTerribleConfigurationStuff();

function doTerribleConfigurationStuff()
{
    $configFile = new ConfigFile(__DIR__ . DIRECTORY_SEPARATOR . ".." .
        DIRECTORY_SEPARATOR . "source" . DIRECTORY_SEPARATOR . "config.inc.php");
    Registry::set(ConfigFile::class, $configFile);

}

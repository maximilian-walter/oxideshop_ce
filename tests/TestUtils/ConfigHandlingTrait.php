<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\TestUtils;

use OxidEsales\Eshop\Core\Config;
use OxidEsales\Eshop\Core\Registry;

trait ConfigHandlingTrait
{
    private $originalConfiguration = null;

    public function clearConfigCache()
    {
        Registry::set(Config::class, null);
    }

    public function backupConfig()
    {
        $this->originalConfiguration = clone Registry::getConfig();

    }

    public function restoreConfig()
    {
        Registry::set(\OxidEsales\Eshop\Core\Config::class, $this->originalConfiguration);
    }

    public function setAdminMode(bool $isAdmin): void
    {
        Registry::getConfig()->setAdminMode($isAdmin);
    }
}
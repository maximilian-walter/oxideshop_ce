<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace Integration\Internal\Framework\Module\Install\Service;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\DataObject\OxidEshopPackage;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service\ModuleInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Bridge\ModuleActivationBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\State\ModuleStateServiceInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use PHPUnit\Framework\TestCase;

class ModuleInstallerTest extends TestCase
{
    use ContainerTrait;

    public function testUninstall(): void
    {
        $moduleId= 'myTestModule';
        $package = new OxidEshopPackage($moduleId, __DIR__ . '/Fixtures/' . $moduleId);

        $this->installModule($moduleId);
        $this->activateTestModule($moduleId);

        $moduleInstaller = $this->get(ModuleInstallerInterface::class);

        $moduleInstaller->uninstall($package);

        $this->assertFalse(
            $moduleInstaller->isInstalled($package)
        );

        $this->assertFalse(
            $this->get(ModuleStateServiceInterface::class)->isActive($moduleId, 1)
        );
    }

    /**
     * @param string $moduleId
     */
    private function installModule(string $moduleId): void
    {
        $installService = $this->get(ModuleInstallerInterface::class);
        $package = new OxidEshopPackage($moduleId, __DIR__ . '/Fixtures/' . $moduleId);
        $package->setTargetDirectory('oeTest/' . $moduleId);
        $installService->install($package);
    }

    /**
     * @param string $moduleId
     */
    private function activateTestModule(string $moduleId): void
    {
        $package = new OxidEshopPackage($moduleId, __DIR__ . '/Fixtures/' . $moduleId);
        $package->setTargetDirectory('oeTest/' . $moduleId);
        $this->get(ModuleInstallerInterface::class)
            ->install($package);
        $this
            ->get(ModuleActivationBridgeInterface::class)
            ->activate($moduleId, Registry::getConfig()->getShopId());
    }

}

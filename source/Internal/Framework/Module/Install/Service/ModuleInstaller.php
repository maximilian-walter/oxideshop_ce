<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ShopConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\DataObject\OxidEshopPackage;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Dao\ModuleConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service\ModuleActivationServiceInterface;

class ModuleInstaller implements ModuleInstallerInterface
{
    /**
     * @var BootstrapModuleInstaller
     */
    private $bootstrapModuleInstaller;

    /**
     * @var ModuleActivationServiceInterface
     */
    private $moduleActivationService;

    /**
     * @var ModuleConfigurationDaoInterface
     */
    private $moduleConfigurationDao;

    /**
     * @var ShopConfigurationDaoInterface
     */
    private $shopConfigurationDao;

    /**
     * ModuleInstaller constructor.
     *
     * @param BootstrapModuleInstaller         $bootstrapModuleInstaller
     * @param ModuleActivationServiceInterface $moduleActivationService
     * @param ModuleConfigurationDaoInterface  $moduleConfigurationDao
     * @param ShopConfigurationDaoInterface    $shopConfigurationDao
     */
    public function __construct(
        BootstrapModuleInstaller $bootstrapModuleInstaller,
        ModuleActivationServiceInterface $moduleActivationService,
        ModuleConfigurationDaoInterface $moduleConfigurationDao,
        ShopConfigurationDaoInterface $shopConfigurationDao
    ) {
        $this->bootstrapModuleInstaller = $bootstrapModuleInstaller;
        $this->moduleActivationService = $moduleActivationService;
        $this->moduleConfigurationDao = $moduleConfigurationDao;
        $this->shopConfigurationDao = $shopConfigurationDao;
    }

    /**
     * @param OxidEshopPackage $package
     */
    public function install(OxidEshopPackage $package): void
    {
        $this->bootstrapModuleInstaller->install($package);
    }

    /**
     * @param OxidEshopPackage $package
     *
     * @throws \OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Exception\ModuleSetupException
     */
    public function uninstall(OxidEshopPackage $package): void
    {
        $moduleConfiguration = $this->moduleConfigurationDao->get($package->getPackagePath());
        $this->deactivateModule($moduleConfiguration->getId());
        $this->bootstrapModuleInstaller->uninstall($package);
    }

    /**
     * @param OxidEshopPackage $package
     *
     * @return bool
     */
    public function isInstalled(OxidEshopPackage $package): bool
    {
        return $this->bootstrapModuleInstaller->isInstalled($package);
    }

    /**
     * @param string $moduleId
     *
     * @throws \OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Exception\ModuleSetupException
     */
    private function deactivateModule(string $moduleId): void
    {
        foreach ($this->shopConfigurationDao->getAll() as $shopId => $shopConfiguration) {
            if ($shopConfiguration->hasModuleConfiguration($moduleId)) {
                $this->moduleActivationService->deactivate($moduleId, $shopId);
            }
        }

    }
}

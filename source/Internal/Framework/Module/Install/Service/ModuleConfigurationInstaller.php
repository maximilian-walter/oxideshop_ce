<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Dao\ModuleConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ProjectConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ProjectConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ShopConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Service\ModuleConfigurationMergingServiceInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Exception\InvalidMetaDataException;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use Webmozart\PathUtil\Path;

class ModuleConfigurationInstaller implements ModuleConfigurationInstallerInterface
{
    /**
     * @var ProjectConfigurationDaoInterface
     */
    private $projectConfigurationDao;

    /**
     * @var ContextInterface
     */
    private $context;

    /**
     * @var ModuleConfigurationMergingServiceInterface
     */
    private $moduleConfigurationMergingService;

    /**
     * @var ModuleConfigurationDaoInterface
     */
    private $moduleConfigurationDao;

    /**
     * ModuleConfigurationInstaller constructor.
     *
     * @param ProjectConfigurationDaoInterface           $projectConfigurationDao
     * @param ModuleConfigurationMergingServiceInterface $moduleConfigurationMergingService
     * @param BasicContextInterface                      $context
     * @param ModuleConfigurationDaoInterface            $moduleConfigurationDao
     */
    public function __construct(
        ProjectConfigurationDaoInterface $projectConfigurationDao,
        ModuleConfigurationMergingServiceInterface $moduleConfigurationMergingService,
        BasicContextInterface $context,
        ModuleConfigurationDaoInterface $moduleConfigurationDao
    ) {
        $this->projectConfigurationDao = $projectConfigurationDao;
        $this->context = $context;
        $this->moduleConfigurationMergingService = $moduleConfigurationMergingService;
        $this->moduleConfigurationDao = $moduleConfigurationDao;
    }


    /**
     * @param string $moduleSourcePath
     * @param string $moduleTargetPath
     *
     * @throws InvalidMetaDataException
     */
    public function install(string $moduleSourcePath, string $moduleTargetPath): void
    {
        $moduleConfiguration = $this->moduleConfigurationDao->get($moduleSourcePath);
        $moduleConfiguration->setPath($this->getModuleRelativePath($moduleTargetPath));

        $projectConfiguration = $this->projectConfigurationDao->getConfiguration();
        $projectConfiguration = $this->addModuleConfigurationToAllShops($moduleConfiguration, $projectConfiguration);

        $this->projectConfigurationDao->save($projectConfiguration);
    }

    public function uninstall(string $moduleSourcePath): void
    {
        // TODO: Implement uninstall() method.
    }

    /**
     * @param string $moduleFullPath
     *
     * @return bool
     * @throws InvalidMetaDataException
     */
    public function isInstalled(string $moduleFullPath): bool
    {
        $moduleConfiguration = $this->moduleConfigurationDao->get($moduleFullPath);
        $projectConfiguration = $this->projectConfigurationDao->getConfiguration();

        foreach ($projectConfiguration->getShopConfigurations() as $shopConfiguration) {
            /** @var $shopConfiguration ShopConfiguration */
            if ($shopConfiguration->hasModuleConfiguration($moduleConfiguration->getId())) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param ModuleConfiguration  $moduleConfiguration
     * @param ProjectConfiguration $projectConfiguration
     *
     * @return ProjectConfiguration
     */
    private function addModuleConfigurationToAllShops(
        ModuleConfiguration $moduleConfiguration,
        ProjectConfiguration $projectConfiguration
    ): ProjectConfiguration {

        foreach ($projectConfiguration->getShopConfigurations() as $shopConfiguration) {
            $this->moduleConfigurationMergingService->merge($shopConfiguration, $moduleConfiguration);
        }

        return $projectConfiguration;
    }

    /**
     * @param string $moduleTargetPath
     * @return string
     */
    private function getModuleRelativePath(string $moduleTargetPath): string
    {
        return Path::isRelative($moduleTargetPath)
            ? $moduleTargetPath
            : Path::makeRelative($moduleTargetPath, $this->context->getModulesPath());
    }
}

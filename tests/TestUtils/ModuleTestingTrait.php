<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\TestUtils;

use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Bridge\ModuleConfigurationDaoBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ShopConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\DataObject\OxidEshopPackage;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service\ModuleInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Bridge\ModuleActivationBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\Utils\ModuleSettingsRestorer;
use Psr\Container\ContainerInterface;
use Webmozart\PathUtil\Path;

trait ModuleTestingTrait
{
    /** @var ModuleSettingsRestorer */
    private $moduleSettingsRestorer = null;
    /** @var ContainerInterface */
    private $originalShopConfiguration = null;

    public function backupModuleSetup()
    {
        if ($this->moduleSettingsRestorer !== null) {
            throw new \Exception("Stored module settings have not been restored!");
        }
        $this->moduleSettingsRestorer = new ModuleSettingsRestorer();
        $this->moduleSettingsRestorer->backupModuleSettings();
        $container = ContainerFactory::getInstance()->getContainer();
        $container->get(ShopConfigurationDaoInterface::class)->deleteAll();
        $container
            ->get('oxid_esales.module.install.service.launched_shop_project_configuration_generator')
            ->generate();
        $this->originalShopConfiguration = clone $container->get(ShopConfigurationDaoInterface::class)->get(1);
    }

    public function installModule(string $id, $fixturePath = null)
    {
        if ($fixturePath === null) {
            $fixturePath = Path::canonicalize(Path::join(__DIR__, '../Integration/Core/Module/Fixtures/'));
        }
        $package = new OxidEshopPackage($id, Path::join($fixturePath, $id));
        $package->setTargetDirectory(Path::join('oeTest', $id));

        $container = ContainerFactory::getInstance()->getContainer();
        $container->get(ModuleInstallerInterface::class)->install($package);
    }

    public function activateModule(string $id)
    {
        $container = ContainerFactory::getInstance()->getContainer();
        $container->get(ModuleActivationBridgeInterface::class)->activate($id, 1);
    }

    public function getModuleConfiguration(string $moduleId)
    {
        $container = ContainerFactory::getInstance()->getContainer();
        return $container->get(ModuleConfigurationDaoBridgeInterface::class)
            ->get($moduleId);
    }

    public function restoreModuleSetup()
    {
        $container = ContainerFactory::getInstance()->getContainer();
        $fileSystem = $container->get('oxid_esales.symfony.file_system');
        $fileSystem->remove(Path::join($container->get(ContextInterface::class)->getModulesPath(), 'oeTest'));
        $this->moduleSettingsRestorer->restoreModuleSettings();
        // Restore original cache
        $container->get(ShopConfigurationDaoInterface::class)->save($this->originalShopConfiguration, 1);

        $this->moduleSettingsRestorer = null;
        $this->originalConfiguration = null;
        $this->originalShopConfiguration = null;
    }
}
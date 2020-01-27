<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Utils;

use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Config\Dao\ShopConfigurationSettingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Config\DataObject\ShopConfigurationSetting;
use OxidEsales\EshopCommunity\Internal\Framework\Dao\EntryDoesNotExistDaoException;
use Psr\Container\ContainerInterface;

class ModuleSettingsRestorer
{
    /**
     * @var ShopConfigurationSettingDaoInterface
     */
    private $shopConfigurationSettingDao;

    private $settings = [];

    private $shopId = 1;

    private $configVars = [ShopConfigurationSetting::ACTIVE_MODULES,
        ShopConfigurationSetting::MODULE_CLASS_EXTENSIONS,
        ShopConfigurationSetting::MODULE_CLASS_EXTENSIONS_CHAIN,
        ShopConfigurationSetting::MODULE_CONTROLLERS,
        ShopConfigurationSetting::MODULE_PATHS,
        ShopConfigurationSetting::MODULE_SMARTY_PLUGIN_DIRECTORIES,
        ShopConfigurationSetting::MODULE_VERSIONS,
        ShopConfigurationSetting::MODULE_TEMPLATES,
        ShopConfigurationSetting::MODULE_EVENTS,
        ShopConfigurationSetting::MODULE_CLASSES_WITHOUT_NAMESPACES];

    public function backupModuleSettings(int $shopId = 1)
    {
        $this->shopId = $shopId;

        /** @var ContainerInterface */
        $container = ContainerFactory::getInstance()->getContainer();

        $this->shopConfigurationSettingDao = $container->get(ShopConfigurationSettingDaoInterface::class);
        $this->settings = $this->getSettings($shopId);

    }

    public function restoreModuleSettings()
    {
        $currentSettings = $this->getSettings($this->shopId);
        foreach($this->settings as $setting => $conf) {
            if ($currentSettings[$setting] !== $conf) {
                $this->restoreSetting($conf, $currentSettings[$setting]);
            }
        }
    }

    private function restoreSetting($originalConf, $currentConf)
    {
        if ($originalConf === null) {
            $this->shopConfigurationSettingDao->delete($currentConf);
        }
        else {
            $this->shopConfigurationSettingDao->save($originalConf);
        }
    }

    private function getSettings(int $shopId): array
    {

        $settings = [];
        foreach($this->configVars as $configVar) {
            try {
                $settings[$configVar] = $this->shopConfigurationSettingDao->get($configVar, $shopId);
            } catch (EntryDoesNotExistDaoException $e) {
                $settings[$configVar] = null;
            }
        }
        return $settings;
    }
}
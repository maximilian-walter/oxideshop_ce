<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\TestUtils;

use OxidEsales\Eshop\Core\Language;
use OxidEsales\Eshop\Core\Registry;

trait LanguageHandlingTrait
{
    private $originalLanguage;

    public function backupLanguage()
    {
        $this->originalLanguage = clone Registry::getLang();
    }

    public function restoreLanguage()
    {
        Registry::set(Language::class, $this->originalLanguage);
    }
    /**
     * Sets language
     *
     * @param int $languageId
     */
    public function setLanguage($languageId)
    {
        $oxLang = Registry::getLang();
        $oxLang->setBaseLanguage($languageId);
        $oxLang->setTplLanguage($languageId);
    }

    /**
     * Returns currently set language
     *
     * @return string
     */
    public function getLanguage()
    {
        return Registry::getLang()->getBaseLanguage();
    }

    /**
     * Sets template language
     *
     * @param int $languageId
     */
    public function setTplLanguage($languageId)
    {
        Registry::getLang()->setTplLanguage($languageId);
    }

    /**
     * Returns template language
     *
     * @return string
     */
    public function getTplLanguage()
    {
        return Registry::getLang()->getTplLanguage();
    }

}
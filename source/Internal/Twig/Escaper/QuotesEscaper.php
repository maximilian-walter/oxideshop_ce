<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Twig\Escaper;

use Twig\Environment;

/**
 * Class QuotesEscaper
 *
 * Escape unescaped single quotes
 *
 * @author Tomasz Kowalewski (t.kowalewski@createit.pl)
 */
class QuotesEscaper implements EscaperInterface
{

    /**
     * @return string
     */
    public function getStrategy(): string
    {
        return 'quotes';
    }

    /**
     * Escape unescaped single quotes
     *
     * @param Environment $environment
     * @param string      $string
     * @param string      $charset
     *
     * @return string
     */
    public function escape(Environment $environment, $string, $charset): string
    {
        return preg_replace("%(?<!\\\\)'%", "\\'", $string);
    }
}
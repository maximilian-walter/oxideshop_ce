<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Acceptance\Frontend;

use OxidEsales\Eshop\Core\Exception\CSRFTokenException;
use OxidEsales\EshopCommunity\Tests\Acceptance\FrontendTestCase;

/**
 * Test csrf token matching.
 */
class CSRFFrontendTest extends FrontendTestCase
{
    public function testAddToBasketWithoutCSRFToken(): void
    {
        $this->openShop();
        $this->loginInFrontend('example_test@oxid-esales.dev', 'useruser');

        $this->assertBasketIsEmpty();
        $this->addToBasketWithoutCSRFToken();

        $this->assertLoggedException(CSRFTokenException::class, 'EXCEPTION_NON_MATCHING_CSRF_TOKEN');
        $this->assertTextPresent('%EXCEPTION_NON_MATCHING_CSRF_TOKEN%');
    }

    public function testAddToBasketWithCSRFToken(): void
    {
        $this->openShop();
        $this->loginInFrontend('example_test@oxid-esales.dev', 'useruser');

        $this->assertBasketIsEmpty();
        $this->openArticle(1000);
        $this->clickAndWait('toBasket');
        $this->assertBasketIsNotEmpty();
    }

    public function testGuestAddToBasket(): void
    {
        $this->assertBasketIsEmpty();
        $this->addToBasket(1000);
        $this->assertBasketIsNotEmpty();
    }

    private function assertBasketIsEmpty(): void
    {
        $this->open($this->getTestConfig()->getShopUrl() . 'en/cart/');
        $this->assertEquals('%YOU_ARE_HERE%: / %PAGE_TITLE_BASKET%', $this->getText('breadCrumb'));
        $this->assertTextPresent('%BASKET_EMPTY%');
    }

    private function assertBasketIsNotEmpty(): void
    {
        $this->open($this->getTestConfig()->getShopUrl() . 'en/cart/');
        $this->assertEquals('%YOU_ARE_HERE%: / %PAGE_TITLE_BASKET%', $this->getText('breadCrumb'));
        $this->assertTextNotPresent('%BASKET_EMPTY%');
    }

    private function addToBasketWithoutCSRFToken(): void
    {
        $testConfig = $this->getTestConfig();
        $url = $testConfig->getShopUrl() . 'index.php?';
        $data = [
            'actcontrol' => 'start',
            'lang'       => '1',
            'cl'         => 'start',
            'fnc'        => 'tobasket',
            'aid'        => 'dc5ffdf380e15674b56dd562a7cb6aec',
            'anid'       => 'dc5ffdf380e15674b56dd562a7cb6aec',
            'am'         => 1
        ];
        $query = http_build_query($data);

        $this->open($url . $query);
    }
}
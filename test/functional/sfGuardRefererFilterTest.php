<?php
namespace Test\sfDoctrineGuardPlugin\Functional;

use sfConfig;


/**
 * sfGuardRefererFilter
 */
abstract class sfGuardRefererFilterTest extends \myFunctionalTestCase
{
    protected
        $refererCookieName,
        $refererTargetCookieName;

    /**
     * SetUp
     */
    public function setUp()
    {
        parent::setUp();

        $this->refererCookieName = sfConfig::get('app_sf_guard_plugin_referer_cookie_name', 'RefererCookieName');
        $this->refererTargetCookieName = sfConfig::get('app_sf_guard_plugin_referer_target_cookie_name', 'RefererTargetCookieName');
    }


    /**
     * Referer is empty
     */
    public function testRefererEmpty()
    {
        $params = array(
            'var1' => 1,
            'var2' => 2,
        );
        $this->browser
            ->get($this->generateUrl('homepage', $params))
            ->with('response')->setsCookie($this->refererCookieName, 'null')
            ->with('response')->setsCookie($this->refererTargetCookieName, $this->generateUrl('homepage', $params, true));
    }


    /**
     * Referer is NOT empty
     */
    public function testRefererNotEmpty()
    {
        $this->browser->setHttpHeader('referer', $ref = 'http://some.site.ru/');
        $this->browser
            ->get($this->generateUrl('homepage'))
            ->with('response')->setsCookie($this->refererCookieName, $ref)
            ->with('response')->setsCookie($this->refererTargetCookieName, $this->generateUrl('homepage', array(), true));
    }


    /**
     * Abort if has cookie
     */
    public function testAbortIfHasCookie()
    {
        $this->browser->setCookie($this->refererCookieName, $ref = 'http://some.site.ru/', time()+200);
        $this->browser->setHttpHeader('referer', 'http://another.site.ru/');
        $this->browser
            ->get($this->generateUrl('homepage'))
            ->with('response')->noCookie($this->refererCookieName)
            ->with('response')->noCookie($this->refererTargetCookieName);
    }


    /**
     * Save referer even 401
     */
    public function testSaveRefererEven401()
    {
        $this->markTestSkipped();
    }


    /**
     * Do not save referer if authneticated
     */
    public function testDoNotSaveRefererIfAuthneticated()
    {
        $this->markTestSkipped();
    }

}

<?php

/**
 * Processes the "referer" cookie.
 *
 * This filter should be added to the application filters.yml file **above**
 * the security filter:
 *
 *    referer:
 *      class: sfGuardRefererFilter
 *    security: ~
 *
 * @package    symfony
 * @subpackage plugin
 */
class sfGuardRefererFilter extends sfFilter
{
    /**
     * Executes the filter chain.
     *
     * @param sfFilterChain $filterChain
     */
    public function execute($filterChain)
    {
        $cookieName = sfConfig::get('app_sf_guard_plugin_referer_cookie_name');
        $targetName = sfConfig::get('app_sf_guard_plugin_referer_target_cookie_name');
        $ttl        = sfConfig::get('app_sf_guard_plugin_referer_cookie_ttl');

        $request = $this->context->getRequest();

        // If no cookie
        if (!$request->getCookie($cookieName)) {
            $time = time() + $ttl;
            $referer = $request->getReferer() ?: 'null';

            $response = $this->context->getResponse();
            $response->setCookie($cookieName, $referer, $time);
            $response->setCookie($targetName, $request->getUri(), $time);
        }

        $filterChain->execute();
    }
}

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
    const NULL = 'null';

    protected
        $cookieReferer,
        $cookieTarget,
        $cookieExpires;


    /**
     * Конструктор
     */
    public function __construct($context, $parameters = array())
    {
        parent::__construct($context, $parameters);

        $this->cookieReferer = sfConfig::get('app_sf_guard_plugin_referer_cookie_name');
        $this->cookieTarget  = sfConfig::get('app_sf_guard_plugin_referer_target_cookie_name');
        $this->cookieExpires = time() + sfConfig::get('app_sf_guard_plugin_referer_cookie_ttl');

        $context->getEventDispatcher()->connect('sfGuard.register_success', array($this, 'listenToRegisterEvent'));
        $context->getEventDispatcher()->connect('sfGuard.signin_success', array($this, 'listenToSigninEvent'));
    }


    /**
     * Executes the filter chain.
     *
     * @param sfFilterChain $filterChain
     */
    public function execute($filterChain)
    {
        if ($this->isFirstCall()) {
            $request = $this->context->getRequest();

            // If no cookie
            if (!$request->getCookie($this->cookieReferer)) {
                $referer = $request->getReferer() ? base64_encode($request->getReferer()) : self::NULL;

                $response = $this->context->getResponse();
                $response->setCookie($this->cookieReferer, $referer, $this->cookieExpires);
                $response->setCookie($this->cookieTarget, base64_encode($request->getUri()), $this->cookieExpires);
            }
        }

        $filterChain->execute();
    }


    /**
     * Сохранить реферер в профиль пользователя
     */
    public function listenToRegisterEvent(sfEvent $event)
    {
        $profile = $event->getSubject()->getProfile();
        $request = $this->getContext()->getRequest();

        if ($referer = $request->getCookie($this->cookieReferer)) {

            // Не сохранять null-строку
            if (self::NULL != $referer) {
                $profile->setReferer($this->_decodeValue($referer));
            }

            $target = $this->_decodeValue($request->getCookie($this->cookieTarget));
            $profile->setRefererTarget($target);
        }
    }


    /**
     * При авторизации пользователя очищать referer-cooke
     */
    public function listenToSigninEvent(sfEvent $event)
    {
        $response = $this->context->getResponse();

        $response->setCookie($this->cookieReferer, self::NULL, $this->cookieExpires);
        $response->setCookie($this->cookieTarget, null, 1);
    }


    /**
     * Расшифровать строку
     *
     * @param  string $value
     * @return string
     */
    private function _decodeValue($value)
    {
        $result = base64_decode($value, true);
        return $result ?: $value;
    }

}

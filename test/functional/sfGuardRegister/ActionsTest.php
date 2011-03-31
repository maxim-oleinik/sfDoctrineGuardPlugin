<?php
namespace Test\sfDoctrineGuardPlugin\Functional\sfGuardRegister;

use sfGuardUser, sfConfig;


/**
 * Регистрация
 */
abstract class ActionsTest extends \myFunctionalTestCase
{
    protected
        $routeShow      = array('sf_guard_register', 'sfGuardRegister', 'index'),
        $routeSubmit    = array('sf_guard_register', 'sfGuardRegister', 'index'),
        $selectorForm   = '#sf_guard_register_form',
        $selectorSubmit = '#sf_guard_register_form_submit',
        $rememberMe = false,
        $saveReferer = false;


    /**
     * Create register form
     *
     * @return sfFormDoctrine
     */
    public function createRegisterForm()
    {
        return new \sfGuardRegisterForm();
    }


    /**
     * Данные для успешной регистрации
     *
     * @return array
     */
    public function getValidInput()
    {
        return array(
            'first_name'     => 'Имя',
            'last_name'      => 'Фамилия',
            'email_address'  => 'some@email.test',
            'username'       => 'login',
            'password'       => 'password',
            'password_again' => 'password',
        );
    }


    public function cleanInput(array $input)
    {
        return $input;
    }


    /**
     * Редирект с регистрации если пользователь авторизован
     */
    public function testRedirectFromRegisterIfUserIsAuthneticated()
    {
        $this->assertNotNull($redirectUrl = sfConfig::get('app_sf_guard_plugin_success_signin_url'));

        $this->authenticateUser();
        $this->browser
            ->getAndCheck($this->routeShow[1], $this->routeShow[2], $this->generateUrl($this->routeShow[0]), 302)
            ->with('response')->checkRedirect(302, $this->generateUrl($redirectUrl));
    }


    /**
     * Регистрация
     */
    public function testAutoRegister()
    {
        $form = $this->createRegisterForm();

        // Показать форму
        $this->browser
            ->getAndCheck($this->routeShow[1], $this->routeShow[2], $this->generateUrl($this->routeShow[0]), 200)
            ->with('response')->begin()
                ->isValid(true)
                ->checkForm($form)
            ->end();

        // Показать ошибки валидации
        $this->browser
            ->click($this->selectorSubmit)
            ->with('request')->checkModuleAction($this->routeSubmit[1], $this->routeSubmit[2])
            ->with('response')->begin()
                ->isStatusCode(400)
                ->checkElement("{$this->selectorForm} .error_list")
                ->isValid(true)
            ->end()
            ->with('form')->begin()
                ->isInstanceOf(get_class($form))
                ->hasErrors(true)
            ->end();

        // Регистрация
        $this->browser
            ->click("{$this->selectorForm}", array($form->getName() => $submitData = $this->getValidInput()))
            ->with('request')->checkModuleAction($this->routeSubmit[1], $this->routeSubmit[2])
            ->with('form')->begin()
                ->isInstanceOf(get_class($form))
                ->hasErrors(false)
            ->end()
            ->with('response')->checkRedirect(302, $this->generateUrl(sfConfig::get('app_sf_guard_plugin_success_signin_url')))
            ->with('user')->isAuthenticated(true);

        // Создан пользователь
        $userData = array_intersect_key($submitData, $form->getObject()->toArray(false));
        unset($userData['password']);
        $this->browser
            ->with('model')->check('sfGuardUser', $this->cleanInput($userData), 1, $found);

        $this->postRegisterChecks($submitData, $found[0]);

        // RememberMe
        $this->browser->newSession();
        $this->browser
            ->get($this->generateUrl($this->routeShow[0]))
            ->with('user')->isAuthenticated($this->rememberMe);
    }


    /**
     * Дополнительные проверки после регистрации
     *
     * @param array       $submitData
     * @param sfGuardUser $createdUser
     */
    public function postRegisterChecks(array $submitData, sfGuardUser $createdUser)
    {
    }


    /**
     * Сохранить реферер
     */
    public function testSaveReferer()
    {
        $user = $this->_registerUserWithReferer($referer = 'http://referer.site.ru/', $target = 'http://some.site.ru/');

        $this->assertEquals($referer, $user->getProfile()->getReferer(), 'Referer');
        $this->assertEquals($target, $user->getProfile()->getRefererTarget(), 'Referer target');
    }


    /**
     * Не сохранять 'null' как реферер
     */
    public function testDoNotSaveNullReferer()
    {
        $user = $this->_registerUserWithReferer($referer = 'null', $target = 'http://some.site.ru/');

        $this->assertNull($user->getProfile()->getReferer(), 'Referer');
        $this->assertEquals($target, $user->getProfile()->getRefererTarget(), 'Referer target');
    }


    /**
     * Установить куку с реферером и зарегистрироваь пользователя
     *
     * @param  string $referer
     * @param  string $target
     * @return sfGuardUser
     */
    protected function _registerUserWithReferer($referer, $target)
    {
        if (!$this->saveReferer) {
            $this->markTestSkipped();
        }

        $form = $this->createRegisterForm();

        $refererCookieName = sfConfig::get('app_sf_guard_plugin_referer_cookie_name', 'RefererCookieName');
        $refererTargetCookieName = sfConfig::get('app_sf_guard_plugin_referer_target_cookie_name', 'RefererTargetCookieName');

        $this->browser->setCookie($refererCookieName, $referer, time()+200);
        $this->browser->setCookie($refererTargetCookieName, $target, time()+200);

        $this->browser
            ->post($this->generateUrl($this->routeSubmit[0]), array($form->getName() => $this->getValidInput()))
            ->with('form')->hasErrors(false)
            // После авторизации кука очищается
            ->with('user')->isAuthenticated(true)
            ->with('response')->setsCookie($refererCookieName, 'null')
            ->with('response')->setsCookie($refererTargetCookieName, null);

        return \Doctrine_Core::getTable('sfGuardUser')->findAll()->getFirst();
    }

}

<?php
namespace Test\sfDoctrineGuardPlugin\Functional\sfGuardRegister;

use sfGuardUser, sfConfig;


/**
 * Регистрация
 */
abstract class ActionsTest extends \myFunctionalTestCase
{
    protected
        $module = 'sfGuardRegister',
        $action = 'index',
        $route  = 'sf_guard_register',
        $selectorForm   = '#sf_guard_register_form',
        $selectorSubmit = '#sf_guard_register_form_submit',
        $rememberMe = false;


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


    /**
     * Редирект с регистрации если пользователь авторизован
     */
    public function testRedirectFromRegisterIfUserIsAuthneticated()
    {
        $this->assertNotNull($redirectUrl = sfConfig::get('app_sf_guard_plugin_success_signin_url'));

        $this->authenticateUser();
        $this->browser
            ->getAndCheck($this->module, $this->action, $this->generateUrl($this->route), 302)
            ->with('response')->checkRedirect(302, $this->generateUrl($redirectUrl));
    }


    /**
     * Регистрация
     */
    public function testRegister()
    {
        $form = $this->createRegisterForm();

        // Показать форму
        $this->browser
            ->getAndCheck($this->module, $this->action, $this->generateUrl($this->route), 200)
            ->with('response')->begin()
                ->isValid(true)
                ->checkForm($form)
            ->end();

        // Показать ошибки валидации
        $this->browser
            ->click($this->selectorSubmit)
            ->with('request')->checkModuleAction($this->module, $this->action)
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
            ->with('request')->checkModuleAction($this->module, $this->action)
            ->with('form')->begin()
                ->isInstanceOf(get_class($form))
                ->hasErrors(false)
            ->end()
            ->with('response')->checkRedirect(302, $this->generateUrl(sfConfig::get('app_sf_guard_plugin_success_signin_url')))
            ->with('user')->isAuthenticated(true);

        // Создан пользователь и профиль
        $userData = array_intersect_key($submitData, $form->getObject()->toArray(false));
        unset($userData['password']);
        $this->browser
            ->with('model')->check('sfGuardUser', $userData, 1, $found);

        $this->postRegisterChecks($submitData, $found[0]);

        // RememberMe
        $this->browser->newSession();
        $this->browser
            ->get($this->generateUrl($this->route))
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

}

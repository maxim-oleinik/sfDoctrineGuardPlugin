<?php

class BasesfGuardRegisterAction extends sfAction
{
    protected $checkAuth = true;

    /**
     * Create register form
     *
     * @return sfFormDoctrine
     */
    public function createRegisterForm()
    {
        return new sfGuardRegisterForm();
    }


    /**
     * Register
     */
    public function execute($request)
    {
        if ($this->checkAuth && $this->getUser()->isAuthenticated()) {
            $this->getUser()->setFlash('notice', 'You are already registered and signed in!');
            $this->redirect(sfConfig::get('app_sf_guard_plugin_success_signin_url', 'homepage'));
        }

        $this->form = $this->createRegisterForm();

        if ($request->isMethod('post')) {
            $this->form->bind($request->getParameter($this->form->getName()));

            if ($this->form->isValid()) {
                $user = $this->form->save();
                $this->dispatcher->notify(new sfEvent($user, 'sfGuard.register_success'));

                $this->getUser()->signIn($user, sfConfig::get('app_sf_guard_plugin_register_remember_me', false));
                return $this->successCallback();
            }

            $this->getResponse()->setStatusCode(400);
        }

        return $this->showFormCallback();
    }


    public function showFormCallback()
    {
    }


    public function successCallback()
    {
        $this->redirect(sfConfig::get('app_sf_guard_plugin_success_signin_url', 'homepage'));
    }
}

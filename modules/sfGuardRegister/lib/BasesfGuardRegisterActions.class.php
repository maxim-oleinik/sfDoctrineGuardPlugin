<?php

class BasesfGuardRegisterActions extends sfActions
{
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
  public function executeIndex(sfWebRequest $request)
  {
    if ($this->getUser()->isAuthenticated())
    {
      $this->getUser()->setFlash('notice', 'You are already registered and signed in!');
      $this->redirect(sfConfig::get('app_sf_guard_plugin_success_signin_url', 'homepage'));
    }

    $this->form = $this->createRegisterForm();

    if ($request->isMethod('post'))
    {
      $this->form->bind($request->getParameter($this->form->getName()));
      if ($this->form->isValid())
      {
        $user = $this->form->save();
        $this->getUser()->signIn($user);

        $this->redirect(sfConfig::get('app_sf_guard_plugin_success_signin_url', 'homepage'));
      }

      $this->getResponse()->setStatusCode(400);
    }
  }
}

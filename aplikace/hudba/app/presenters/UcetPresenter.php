<?php

use Nette\Forms\Form;


/**
 * Sign in/out presenters.
 */
class UcetPresenter extends BasePresenter
{

  /** @persistent */
  public $backlink = '';


  /**
   * Sign-in form factory.
   * @return Nette\Application\UI\Form
   */
  protected function createComponentPrihlaseniForm()
  {
    $form = new Nette\Application\UI\Form;
    
    $form->addText('login', 'Jméno:')
      ->setRequired('Prosím zadejte vaše uživatelské jméno.');

    $form->addPassword('heslo', 'Heslo:')
      ->setRequired('Prosím zadejte vaše heslo.');

    $form->addSubmit('send', 'Přihlásit');

    $form->onSuccess[] = $this->prihlaseniFormSucceeded;

    return Bs3Form::transform($form);
  }


  public function prihlaseniFormSucceeded($form)
  {
    $values = $form->getValues();

    try {
      $this->getUser()->login($values->login, $values->heslo);
      $this->restoreRequest($this->backlink);
      $this->redirect('Sluzby:');
    } catch (Nette\Security\AuthenticationException $e) {
      $form->addError($e->getMessage());
    }
  }

  public function actionOdhlaseni()
  {
    $this->getUser()->logout(TRUE);
    $this->flashMessage('Odhlášení bylo úspěšné.', 'success');
    $this->redirect('prihlaseni');
  }
}

<?php

use Nette\Application\Responses\FileResponse,
    Nette\Forms\Form,
    Nette\Mail\Message,
    Nette\Mail\SendmailMailer;

class HalfplaybackPresenter extends BasePresenter
{

	/** @var Vydavatelstvi @inject*/
	public $vydavatelstvi;
	/** @var Halfplayback @inject*/
	public $halfplayback;

  /**
   * @return Nette\Application\UI\Form
   */
  protected function createComponentSkladbaForm()
  {
    $form = new \Nette\Application\UI\Form;

    $form->addText('nazev', 'Název:')
      ->setRequired('Prosím zadejte název skladby.')
      ->addRule(Form::MAX_LENGTH, 'Název skladby musí mít maximálně %d znaků', 100);

    $form->addTextArea('popis', 'Popis:')
      ->addRule(Form::MAX_LENGTH, 'Popis musí mít maximálně %d znaků', 1000);

    $form->addText('cena', 'Cena:')
      ->setRequired('Prosím zadejte cenu skladby.')
      ->addRule(Form::INTEGER, 'Částka musí být číslo')
      ->addRule(Form::RANGE, 'Částka musí být od %d do %d Kč', array(1, 1000))
      ->setType('number');

    $form->addSelect('soubor_id', 'Demo:', $this->vydavatelstvi->demoSkladby())
      ->setPrompt('Zvolte demo mp3');

    $form->addSubmit('send', 'Uložit');

    $form->onSuccess[] = $this->skladbaFormSucceeded;

    return \Bs3Form::transform($form);
  }

  /**
   * @return Nette\Application\UI\Form
   */
  protected function createComponentNakupForm()
  {
    $form = new \Nette\Application\UI\Form;

    $form->addHidden('id');

    $form->addTextArea('adresa', 'Dodací adresa:')
      ->setRequired('Prosím zadejte dodací adresu.')
      ->addRule(Form::MAX_LENGTH, 'Dodací adresa musí mít maximálně %d znaků', 300);

    $form->addText('email', 'E-mail:')
      ->addRule(Form::MAX_LENGTH, 'E-mail musí mít maximálně %d znaků', 100)
      ->addCondition(Form::FILLED)
      ->addRule(Form::EMAIL, 'Zadejte platnou e-mailovou adresu');

    $form->addText('tel', 'Telefon:')
      ->addRule(Form::MAX_LENGTH, 'Telefonní číslo musí mít maximálně %d znaků', 20);

    $form['email']->addConditionOn($form['tel'], ~Form::FILLED)
      ->setRequired('Prosím zadejte kontaktní telefon nebo e-mail.');
    $form['tel']->addConditionOn($form['email'], ~Form::FILLED)
      ->setRequired('Prosím zadejte kontaktní telefon nebo e-mail.');

    $form->addText('pozn', 'Poznámka:')
      ->addRule(Form::MAX_LENGTH, 'Poznámka musí mít maximálně %d znaků', 300);

    $form->addSubmit('send', 'Odeslat objednávku');

    $form->onSuccess[] = $this->nakupFormSucceeded;

    return \Bs3Form::transform($form);
  }

  public function skladbaFormSucceeded($form)
  {
    $values = $form->getValues();
    $skladbaId = $this->getParameter('id');

    if($skladbaId) { //editace
      try {
        $this->halfplayback->update($skladbaId, $values);
      } catch (\Exception $e) {
        $this->flashMessage('Skladbu se nepodařilo uložit.', 'danger');
      }
    }
    else { //nova skladba
      try {
        $skladba = $this->halfplayback->insert($values);
      } catch (\Exception $e) {
        $this->flashMessage('Skladbu se nepodařilo uložit.', 'danger');
      }
    }

    $this->flashMessage('Skladba byla uloženy.' , 'success');
    $this->redirect('Halfplayback:default');
  }


  public function nakupFormSucceeded($form)
  {
    $values = $form->getValues();

    $skladba = $this->halfplayback->findById($values['id']);
    if (!$skladba) {
      $this->error('Požadovaná skladba neexistuje.');
    }

    $text = "Název: " . $skladba->nazev . "\n";
    $text .= "Cena: " . $skladba->cena . " Kč\n\n";
    $text .= "Adresa:\n" . $values['adresa'] . "\n";
    $text .= "Telefon: " . $values['tel'] . "\n";
    $text .= "Email: " . $values['email'] . "\n";
    $text .= "Poznámka:\n" . $values['pozn'] . "\n";


    $params = $this->context->parameters['hudba'];

    $mail = new Message;
    $mail->setFrom('Lubomír Piskoř <' . $params['adminMail'] . '>')
        ->addTo($params['adminMail'])
        ->addTo($params['hpbackMail'])
        ->setSubject('Halfplayback - objednávka skladby')
        ->setBody($text);
    $mailer = new SendmailMailer;
    $mailer->send($mail);


    $this->flashMessage('Objednávka byla odeslána.' , 'success');
    $this->redirect('Halfplayback:default');
  }

	public function renderDefault()
	{
    $skladby = $this->halfplayback->findAll();
    $this->template->skladby = $skladby;
	}

	public function renderDetail($id)
	{
    $skladba = $this->halfplayback->findById($id);
    if (!$skladba) {
      $this->error('Požadovaná skladba neexistuje.');
    }
    $this->template->skladba = $skladba;
    $this['nakupForm']->setDefaults(array('id'=>$id));

    if($this->user->isInRole('admin')) {
      $this['skladbaForm']->setDefaults($skladba);
    }
	}

	public function actionSmazat($id)
	{
    if (!$this->user->isInRole('admin')) {
      $this->flashMessage('Pro vstup na požadovanou stránku se musíte přihlásit.');
      $this->redirect('Ucet:prihlaseni', array('backlink' => $this->storeRequest()));
    }

    $skladba = $this->halfplayback->findById($id);
    if (!$skladba) {
      $this->error('Požadovaná skladba neexistuje.');
    }

    $this->halfplayback->smazat($id);
    $this->flashMessage('Skladba byla smazána.' , 'success');
    $this->redirect('Halfplayback:default');
	}

	public function actionPridat()
	{
    if (!$this->user->isInRole('admin')) {
      $this->flashMessage('Pro vstup na požadovanou stránku se musíte přihlásit.');
      $this->redirect('Ucet:prihlaseni', array('backlink' => $this->storeRequest()));
    }
	}
}

<?php

use Nette\Forms\Form,
    Nette\Mail\Message,
    Nette\Mail\SendmailMailer;

/**
 * Homepage presenter.
 */
class InfoPresenter extends BasePresenter
{

	/** @var Dvd @inject*/
	public $dvd;

	/** @var Skladba @inject*/
	public $skladby;

  public function startup()
  {
    parent::startup();
    $this->template->cisloUctu = $this->context->parameters['midi']['cisloUctu'];
  }

  /**
   * @return Nette\Application\UI\Form
   */
  protected function createComponentNakupForm()
  {
    $form = new \Nette\Application\UI\Form;

    $form->addHidden('id');

    $form->addText('pocet', 'Počet kusů:')
      ->setRequired('Prosím zadejte počet kusů.')
      ->addRule(Form::INTEGER, 'Počet kusů musí být číslo')
      ->addRule(Form::RANGE, 'Počet kusů musí být od %d do %d Kč', array(1, 10))
      ->setType('number');

    $form->addTextArea('adresa', 'Dodací adresa:')
      ->setRequired('Prosím zadejte dodací adresu.')
      ->addRule(Form::MAX_LENGTH, 'Název musí mít maximálně %d znaků', 300);

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

  public function nakupFormSucceeded($form)
  {
    $values = $form->getValues();

    $dvd = $this->dvd->findById($values['id']);
    if (!$dvd) {
      $this->error('Požadované dvd neexistuje.');
    }

    $text = "Název: " . $dvd->nazev . "\n";
    $text .= "Cena za kus: " . $dvd->cena . " Kč\n\n";
    $text .= "Počet kusů: " . $values['pocet'] . "\n";
    $text .= "Adresa:\n" . $values['adresa'] . "\n";
    $text .= "Telefon: " . $values['tel'] . "\n";
    $text .= "Email: " . $values['email'] . "\n";
    $text .= "Poznámka:\n" . $values['pozn'] . "\n";


    $params = $this->context->parameters['hudba'];

    $mail = new Message;
    $mail->setFrom('Lubomír Piskoř <' . $params['adminMail'] . '>')
        ->addTo($params['adminMail'])
        ->setSubject('Karaoke na DVD - objednávka')
        ->setBody($text);
    $mailer = new SendmailMailer;
    $mailer->send($mail);


    $this->flashMessage('Objednávka byla odeslána.' , 'success');
    $this->redirect('Info:karaokeNaDvd');
  }

	public function renderDefault()
	{
    $this->template->oblibene = $this->skladby->oblibene();
    $this->template->novinky = $this->skladby->novinky();
    $this->template->pocet = $this->skladby->pocetSkladeb();
    $this->template->formaty = $this->skladby->seznamFormatu('plneVerze');
	}

	public function renderKaraokeNaDvd()
	{
    $this->template->seznamDvd = $this->dvd->findAll();
	}

	public function renderVyrobaNaZakazku()
	{
    $this->template->formaty = $this->skladby->seznamFormatu('plneVerze');
	}

  public function renderObjednavkaDvd($id)
  {
    $dvd = $this->dvd->findById($id);
    if (!$dvd) {
      $this->error('Požadované dvd neexistuje.');
    }
    $this->template->dvd = $dvd;
    $this['nakupForm']->setDefaults(array('id'=>$id));
  }
}

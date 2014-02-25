<?php

use Nette\Forms\Form,
    Nette\Image,
    Nette\Mail\Message,
    Nette\Mail\SendmailMailer;

/**
 * Homepage presenter.
 */
class VydavatelstviPresenter extends BasePresenter
{

	/** @var Vydavatelstvi @inject*/
	public $vydavatelstvi;

  /**
   * @return Nette\Application\UI\Form
   */
  protected function createComponentCdForm()
  {
    $form = new \Nette\Application\UI\Form;

    $form->addText('autor', 'Interpret:')
      ->setRequired('Prosím zadejte interpreta.')
      ->addRule(Form::MAX_LENGTH, 'Interpret musí mít maximálně %d znaků', 100);

    $form->addText('nazev', 'Název:')
      ->setRequired('Prosím zadejte název alba.')
      ->addRule(Form::MAX_LENGTH, 'Název alba musí mít maximálně %d znaků', 100);

    $form->addTextArea('popis', 'Popis:')
      ->addRule(Form::MAX_LENGTH, 'Popis musí mít maximálně %d znaků', 1000);

    $form->addText('cena', 'Cena:')
      ->setRequired('Prosím zadejte cenu skladby.')
      ->addRule(Form::INTEGER, 'Částka musí být číslo')
      ->addRule(Form::RANGE, 'Částka musí být od %d do %d Kč', array(1, 1000))
      ->setType('number');

    $form->addUpload('foto', 'Foto:');

    $form->addSubmit('send', 'Uložit');

    $form->onSuccess[] = $this->cdFormSucceeded;

    return \Bs3Form::transform($form);
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
      ->setRequired('Prosím zadejte váš e-mail.')
      ->addRule(Form::EMAIL, 'Zadejte platnou e-mailovou adresu')
      ->addRule(Form::MAX_LENGTH, 'E-mail musí mít maximálně %d znaků', 100);

    $form->addText('tel', 'Telefon:')
      ->setRequired('Prosím zadejte vaše telefonní číslo.')
      ->addRule(Form::MAX_LENGTH, 'Telefonní číslo musí mít maximálně %d znaků', 20);

    $form->addText('pozn', 'Poznámka:')
      ->addRule(Form::MAX_LENGTH, 'E-mail musí mít maximálně %d znaků', 300);

    $form->addSubmit('send', 'Odeslat');

    $form->onSuccess[] = $this->nakupFormSucceeded;

    return \Bs3Form::transform($form);
  }

  public function cdFormSucceeded($form)
  {
    $values = $form->getValues();
    $cdId = $this->getParameter('id');
    $uploads = $this->context->getService('httpRequest')->getFiles();
    unset($values['foto']);

    if($cdId) { //editace
      try {
        $this->vydavatelstvi->update($cdId, $values);
      } catch (\Exception $e) {
        $this->flashMessage('Album se nepodařilo uložit.', 'danger');
      }
    }
    else { //nova skladba
      try {
        $cd = $this->vydavatelstvi->insert($values);
        $cdId = $cd->id;
      } catch (\Exception $e) {
        $this->flashMessage('Album se nepodařilo uložit.', 'danger');
      }
    }

    //presun uploadovanych souboru z tmp adresare do ciloveho umisteni
    $destDir = $this->context->parameters['wwwDir'] . '/img/vydavatelstvi';
    $nazev = '';
    foreach ($uploads as $soubor) {
      if($soubor && $soubor->isOk) {
        $ext = pathinfo($soubor->getName(), PATHINFO_EXTENSION );
        $nazev = 'cd-' . $cdId . '.' . $ext;
        $soubor->move($destDir . '/' . $nazev);
        $image = Image::fromFile($destDir . '/' . $nazev);
        $image->resize(1024, 1024);
        $image->save($destDir . '/' . $nazev);
        $image->resize(150, 150);
        $image->save($destDir . '/thumb-' . $nazev);
      }
    }

    if($nazev) {
      try {
        $this->vydavatelstvi->update($cdId, array('foto'=>$nazev));
      } catch (\Exception $e) {
        $this->flashMessage('Nepodařilo se uložit foto.', 'danger');
      }
    }

    $this->flashMessage('Album bylo uloženo.' , 'success');
    $this->redirect('Vydavatelstvi:default');
  }


  public function nakupFormSucceeded($form)
  {
    $values = $form->getValues();

    $cd = $this->vydavatelstvi->findById($values['id']);
    if (!$cd) {
      $this->error('Požadované album neexistuje.');
    }

    $text = "Název alba: " . $cd->nazev . "\n";
    $text .= "Interpret: " . $cd->autor . "\n";
    $text .= "Cena za kus: " . $cd->cena . " Kč\n\n";
    $text .= "Počet kusů: " . $values['pocet'] . "\n";
    $text .= "Adresa:\n" . $values['adresa'] . "\n";
    $text .= "Telefon: " . $values['tel'] . "\n";
    $text .= "Email: " . $values['email'] . "\n";
    $text .= "Poznámka:\n" . $values['pozn'] . "\n";


    $params = $this->context->parameters['hudba'];

    $mail = new Message;
    $mail->setFrom('Lubomír Piskoř <' . $params['adminMail'] . '>')
        ->addTo($params['adminMail'])
        ->setSubject('Hudební vydavatelství - objednávka CD')
        ->setBody($text);
    $mailer = new SendmailMailer;
    $mailer->send($mail);


    $this->flashMessage('Objednávka byla odeslána.' , 'success');
    $this->redirect('Vydavatelstvi:default');
  }

	public function renderDefault()
	{
    $alba = $this->vydavatelstvi->findAll();
    $this->template->alba = $alba;
	}

  public function renderNakup($id)
  {
    $cd = $this->vydavatelstvi->findById($id);
    if (!$cd) {
      $this->error('Požadované album neexistuje.');
    }
    $this->template->cd = $cd;
    $this['nakupForm']->setDefaults(array('id'=>$id));
  }

	public function renderDetail($id)
	{
    if (!$this->user->isInRole('admin')) {
      $this->flashMessage('Pro vstup na požadovanou stránku se musíte přihlásit.');
      $this->redirect('Ucet:prihlaseni', array('backlink' => $this->storeRequest()));
    }

    $cd = $this->vydavatelstvi->findById($id);
    if (!$cd) {
      $this->error('Požadované album neexistuje.');
    }
    $this->template->cd = $cd;

    $this['cdForm']->setDefaults($cd);
	}

	public function actionSmazat($id)
	{
    if (!$this->user->isInRole('admin')) {
      $this->flashMessage('Pro vstup na požadovanou stránku se musíte přihlásit.');
      $this->redirect('Ucet:prihlaseni', array('backlink' => $this->storeRequest()));
    }

    $cd = $this->vydavatelstvi->findById($id);
    if (!$cd) {
      $this->error('Požadované album neexistuje.');
    }

    if($cd->foto) {
      $destDir = $this->context->parameters['wwwDir'] . '/img/vydavatelstvi';
      if(file_exists($destDir . '/' . $cd->foto)) unlink($destDir . '/' . $cd->foto);
      if(file_exists($destDir . '/thumb-' . $cd->foto)) unlink($destDir . '/thumb-' . $cd->foto);
    }

    $this->vydavatelstvi->smazat($id);
    $this->flashMessage('Album bylo smazáno.' , 'success');
    $this->redirect('Vydavatelstvi:default');
	}

	public function actionPridat()
	{
    if (!$this->user->isInRole('admin')) {
      $this->flashMessage('Pro vstup na požadovanou stránku se musíte přihlásit.');
      $this->redirect('Ucet:prihlaseni', array('backlink' => $this->storeRequest()));
    }
	}
}

<?php

use Nette\Application\Responses\FileResponse,
    Nette\Forms\Form;

/**
 * Skladba presenter.
 */
class SkladbaPresenter extends BasePresenter
{

	/** @var Skladba @inject*/
	public $skladby;
	/** @var Uzivatel @inject*/
	public $uzivatele;

  /**
   * @return Nette\Application\UI\Form
   */
  protected function createComponentSkladbaForm()
  {
    $form = new Nette\Application\UI\Form;

    $form->addGroup('Skladba');
    $form->addText('nazev', 'Název:')
      ->setRequired('Prosím zadejte název skladby.')
      ->addRule(Form::MAX_LENGTH, 'Název musí mít maximálně %d znaků', 100);

    $form->addText('autor', 'Interpret:')
      ->setRequired('Prosím zadejte interpreta skladby.')
      ->addRule(Form::MAX_LENGTH, 'Interpret musí mít maximálně %d znaků', 100);

    $form->addText('cena', 'Cena:')
      ->setRequired('Prosím zadejte cenu skladby.')
      ->addRule(Form::INTEGER, 'Částka musí být číslo')
      ->addRule(Form::RANGE, 'Částka musí být od %d do %d Kč', array(1, 100))
      ->setType('number');

    $form->addText('poznamka', 'Poznámka:')
      ->addRule(Form::MAX_LENGTH, 'Poznámka musí mít maximálně %d znaků', 100);

    $form->addSelect('zanr_id', 'Žánr:', $this->skladby->seznamZanru())
      ->setRequired('Prosím zadejte žánr.')
      ->setPrompt('Zvolte žánr');

    $form->addSelect('verze', 'Verze:', array('MIDI' => 'MIDI', 'Karaoke' => 'Karaoke'))
      ->setRequired('Prosím zadejte verzi.')
      ->setPrompt('Zvolte verzi');

    $form->addUpload('format1', 'text');
    $form->addUpload('format2', 'mid');
/*
    $form->addGroup('Ukázky');
    foreach($this->skladby->seznamFormatu('demo') as $formatId => $formatNazev) {
      $form->addUpload('format' . $formatId, $formatNazev);
    }

    $form->addGroup('Plné verze');
    foreach($this->skladby->seznamFormatu('plna') as $formatId => $formatNazev) {
      $form->addUpload('format' . $formatId, $formatNazev);
    }
*/
    $form->addSubmit('send', 'Uložit');

    $form->onSuccess[] = $this->skladbaFormSucceeded;
    $form->addProtection('Vypršel časový limit, odešlete formulář znovu.');

    return Bs3Form::transform($form);
  }


  public function skladbaFormSucceeded($form)
  {
    $values = $form->getValues();
    $skladbaId = $this->getParameter('id');
    $soubory = $this->context->getService('httpRequest')->getFiles();

    //odstraneni upload polozek z pole s informacemi pro ulozeni
    foreach($values as $key => $val) {
      if($val instanceof Nette\Http\FileUpload) unset($values[$key]);
    }

    foreach ($soubory as $soubor) {
      if($soubor && $soubor->isOk) {
        $soubor->move($this->context->parameters['appDir'] . '/../data/' . $soubor->getSanitizedName());
      }
    }

    if($skladbaId) {
      $this->skladby->update($skladbaId, $values);
      $this->flashMessage('Údaje byly uloženy.', 'success');
      //$this->redirect('Skladba:detail', $skladbaId);
    }
    else {
      
      //$skladba = $this->skladby->insert($values);
      //$this->flashMessage('Skladba byla přidána.', 'success');
      //$this->redirect('Skladba:detail', $skladba->id);
    }
  }

	public function renderDefault($mode = null)
	{
    $this->template->skladby = $this->skladby->findAll();
    $this->template->adminMode = $this->user->isInRole('admin') && $mode;
	}

  public function renderDetail($id)
  {
    $skladba = $this->skladby->findById($id);
    if (!$skladba) {
      $this->error('Požadovaná skladba neexistuje.');
    }
    $this->template->skladba = $skladba;
    $this->template->formaty = $this->skladby->formatySkladby($id);
    $this->template->maZakoupeno = $this->uzivatele->maZakoupeno($this->user->id, $id);

    if($this->user->isInRole('admin')) {
      $this['skladbaForm']->setDefaults($skladba->toArray());
    }

  }

	public function actionNakup($id)
	{
    if (!$this->user->isLoggedIn()) {
      $this->flashMessage('Pro vstup na požadovanou stránku se musíte přihlásit.');
      $this->redirect('Ucet:prihlaseni', array('backlink' => $this->storeRequest()));
    }

    if($this->uzivatele->maZakoupeno($this->user->id, $id)){
      $this->redirect('Skladba:detail', $id);
    }

    $skladba = $this->skladby->findById($id);
    if (!$skladba) {
      $this->error('Požadovaná skladba neexistuje.');
    }
    $this->template->skladba = $skladba;

    try {
      $this->uzivatele->koupitSkladbu($this->user->id, $skladba);
    } catch (\Exception $e) {
      $this->flashMessage($e->getMessage(), 'warning');
      $this->redirect('Skladba:detail', $id);
    }
	}


	public function actionPridat()
	{
    if (!$this->user->isInRole('admin')) {
      $this->flashMessage('Pro vstup na požadovanou stránku se musíte přihlásit.');
      $this->redirect('Ucet:prihlaseni', array('backlink' => $this->storeRequest()));
    }
	}

	public function actionDownload($id)
	{
    $this->sendResponse(new FileResponse($this->context->parameters['appDir'] . '/../data/skladba.mid'));
	}
}

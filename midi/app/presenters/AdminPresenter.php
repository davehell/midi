<?php

use Nette\Utils\Validators,
    Nette\Forms\Form,
    Nette\Application\Responses\TextResponse;

/**
 * Admin presenter.
 */
class AdminPresenter extends BasePresenter
{

	/** @var Skladba @inject*/
	public $skladby;
	/** @var Uzivatel @inject*/
	public $uzivatele;

  public function startup()
  {
    parent::startup();
    if (!$this->user->isInRole('admin')) {
      $this->flashMessage('Pro vstup na požadovanou stránku se musíte přihlásit.');
      $this->redirect('Ucet:prihlaseni', array('backlink' => $this->storeRequest()));
    }
  }

  /**
   * @return Nette\Application\UI\Form
   */
  protected function createComponentKreditForm()
  {
    $form = new Nette\Application\UI\Form;

    $form->addText('castka', 'Částka (Kč):')
      ->setRequired('Prosím zadejte částku, kterou chcete odebrat.')
      ->addRule(Form::INTEGER, 'Částka musí být číslo')
      ->addRule(Form::RANGE, 'Částka musí být od %d do %d Kč', array(1, 1000))
      ->setType('number');

    $form->addHidden('uzivId');

    $form->addSubmit('send', 'Odebrat kredit');

    $form->onSuccess[] = $this->kreditFormSucceeded;

    return Bs3Form::transform($form);
  }

  public function kreditFormSucceeded($form)
  {
    $values = $form->getValues();

    $uziv = $this->uzivatele->findById($values->uzivId);
    if (!$uziv) {
      $this->error('Požadovaný uživatel neexistuje.');
    }

    try {
      $row = $this->uzivatele->odebratKredit($uziv, $values->castka);
      $this->flashMessage('Kredit ve výši ' . $values->castka . ' Kč byl odebrán.', 'success');
      $this->redirect('Admin:zakaznikDetail', $uziv->id);

    } catch (\Exception $e) {
      //$form->addError($e->getMessage());
      $this->flashMessage($e->getMessage(), 'danger');
    }
  }

	public function renderDefault()
	{
    $this->template->cekajiciNaDobiti = $this->uzivatele->cekajiciNaDobiti();
	}

	public function actionPripsatKredit($id)
	{
    $trans = $this->uzivatele->cekajiciNaDobiti()->get($id);
    if (!$trans) {
      $this->error('Požadovaná transakce neexistuje.');
    }
    $this->uzivatele->pripsatKredit($trans);
    $this->flashMessage('Kredit ve výši ' . $trans->castka . ' Kč byl připsán.', 'success');
    $this->redirect('default');
	}

	public function actionZrusitDobiti($id)
	{
    $trans = $this->uzivatele->cekajiciNaDobiti()->get($id);
    if (!$trans) {
      $this->error('Požadovaná transakce neexistuje.');
    }

    $this->uzivatele->zrusitPozadavekNaNabiti($id);
    $this->flashMessage('Požadavek na nabití částky ' . $trans->castka . ' Kč byl odebrán.', 'success');
    $this->redirect('default');
	}

	public function actionOdebratKredit($uzivId, $castka = 0)
	{
    $uziv = $this->uzivatele->findById($uzivId);
    if (!$uziv) {
      $this->error('Požadovaný uživatel neexistuje.');
    }

    if(!Validators::is($castka, 'int:0..1000')) {
      $this->flashMessage('Chybně zadaná částka: ' . $castka, 'warning');
      $this->redirect('default');
    }

    if($uziv->kredit - $castka < 0) {
      $this->flashMessage('Zadána příliš vysoká částka - kredit by byl záporný', 'warning');
      $this->redirect('default');
    }
    $this->uzivatele->odebratKredit($uziv, $castka);
    $this->flashMessage('Kredit ve výši ' . $castka . ' Kč byl odebrán.', 'success');
    $this->redirect('default');
	}

	public function renderZakaznici()
	{
    $this->template->zakaznici = $this->uzivatele->findAllDetails();
	}

	public function renderZakaznikDetail($id)
	{
    $uzivatel = $this->uzivatele->findById($id);
    if (!$uzivatel) {
      $this->error('Požadovaný uživatel neexistuje.');
    }
    $this->template->uzivatel = $uzivatel;
    $this->template->sumaNakupu = $this->uzivatele->sumaNakupu($id);
    $this->template->historie = $this->uzivatele->historieDobijeni($id);
    $this->template->nakupy = $this->uzivatele->zakoupeneSkladby($id);
    $this['kreditForm']->setDefaults(array('uzivId' => $id));
	}

	public function renderStahovani()
	{
    $this->template->nakupy = $this->skladby->prehledStahovani();
	}

	public function actionStahovaniDownload()
	{
    $nakupy = $this->skladby->prehledStahovani();
    $eol = "\r\n";
    $content = 'název;interpret;počet stažení' . $eol;
    foreach ($nakupy as $nakup) {
        $content .= $nakup->skladba->nazev . ';' . $nakup->skladba->autor . ';' . $nakup->pocet . $eol;
    }

    $httpResponse = $this->presenter->getHttpResponse();
    $httpResponse->setContentType('text/plain');
    $httpResponse->setHeader('Content-Disposition', 'attachment; filename="stahovani.csv"');
    $httpResponse->setHeader('Content-Length', strlen($content));
    $this->presenter->sendResponse(new TextResponse($content));
	}
}

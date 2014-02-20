<?php

namespace HudbaModule;

use Nette\Forms\Form,
    Nette\Image;

/**
 * Homepage presenter.
 */
class BazarPresenter extends \BasePresenter
{

	/** @var Bazar @inject*/
	public $bazar;

  /**
   * @return Nette\Application\UI\Form
   */
  protected function createComponentInzeratForm()
  {
    $form = new \Nette\Application\UI\Form;

    $form->addTextArea('text', 'Text:')
      ->setRequired('Prosím zadejte text inzerátu.')
      ->addRule(Form::MAX_LENGTH, 'Název musí mít maximálně %d znaků', 1000);

    $form->addText('email', 'E-mail:')
      ->setRequired('Prosím zadejte váš e-mail.')
      ->addRule(Form::EMAIL, 'Zadejte platnou e-mailovou adresu')
      ->addRule(Form::MAX_LENGTH, 'E-mail musí mít maximálně %d znaků', 100);

    $form->addText('tel', 'Telefon:')
      ->setRequired('Prosím zadejte vaše telefonní číslo.')
      ->addRule(Form::MAX_LENGTH, 'Telefonní číslo musí mít maximálně %d znaků', 20);

    $form->addUpload('foto1', 'Foto č. 1');
      //->addRule(Form::IMAGE, 'Foto musí být JPEG, PNG nebo GIF.');
    $form->addUpload('foto2', 'Foto č. 2');
    $form->addUpload('foto3', 'Foto č. 3');

    $form->addSubmit('send', 'Uložit');

    $form->onSuccess[] = $this->inzeratFormSucceeded;

    return \Bs3Form::transform($form);
  }

  public function inzeratFormSucceeded($form)
  {
    $values = $form->getValues();

    $inzeratId = $this->getParameter('id');
    $uploads = $this->context->getService('httpRequest')->getFiles();
    unset($values['foto1']);
    unset($values['foto2']);
    unset($values['foto3']);

    if($inzeratId) { //editace
      try {
        $this->bazar->update($inzeratId, $values);
      } catch (\Exception $e) {
        $this->flashMessage('Inzerát se nepodařilo uložit.', 'danger');
      }
    }
    else { //nova skladba
      try {
        $inzerat = $this->bazar->insert($values);
        $inzeratId = $inzerat->id;
      } catch (\Exception $e) {
        $this->flashMessage('Inzerát se nepodařilo uložit.', 'danger');
      }
    }

    //presun uploadovanych souboru z tmp adresare do ciloveho umisteni
    $destDir = $this->context->parameters['wwwDir'] . '/img/bazar';
    $i = 0;
    $fotky = array();
    foreach ($uploads as $soubor) {
      if($soubor && $soubor->isOk) {
        $i++;
        $ext = pathinfo($soubor->getName(), PATHINFO_EXTENSION );
        $nazev = 'inzerat-' . $inzeratId . '-' . $i . '.' . $ext;
        $soubor->move($destDir . '/' . $nazev);
        $image = Image::fromFile($destDir . '/' . $nazev);
        $image->resize(1024, 1024);
        $image->save($destDir . '/' . $nazev);
        $image->resize(150, 150);
        $image->save($destDir . '/thumb-' . $nazev);
        $fotky['foto' . $i] = $nazev;
      }
    }

    if(count($fotky)) {
      try {
        $this->bazar->update($inzeratId, $fotky);
      } catch (\Exception $e) {
        $this->flashMessage('Nepodařilo se uložit fotky.', 'danger');
      }
    }

    $this->flashMessage('Inzerát byl uložen.' , 'success');
    $this->redirect('Bazar:default');
  }

	public function renderDefault()
	{
    $pocetInzeratu = $this->bazar->findAll()->count();
    $vp = new \VisualPaginator($this, 'vp');
    $paginator = $vp->getPaginator();
    $paginator->itemsPerPage = 50;
    $paginator->itemCount = $pocetInzeratu;

    $inzeraty = $this->bazar->findAll($paginator->getLength(), $paginator->getOffset());

    $this->template->inzeraty = $inzeraty;
	}

	public function renderDetail($id)
	{
    $inzerat = $this->bazar->findById($id);
    if (!$inzerat) {
      $this->error('Požadovaný inzerát neexistuje.');
    }
    $this->template->inzerat = $inzerat;

    $this['inzeratForm']->setDefaults($inzerat);
	}

	public function actionSmazatFoto($inzeratId, $fotoId)
	{
    $inzerat = $this->bazar->findById($inzeratId);
    if (!$inzerat) {
      $this->error('Požadovaný inzerát neexistuje.');
    }
    $destDir = $this->context->parameters['wwwDir'] . '/img/bazar';
    if($fotoId == 1) $soubor = $inzerat->foto1;
    else if($fotoId == 1) $soubor = $inzerat->foto2;
    else $soubor = $inzerat->foto3;
    $soubory = array($destDir . '/' . $soubor, $destDir . '/thumb-' . $soubor);
    $this->bazar->smazatFoto($inzeratId, $fotoId, $soubory);
    $this->redirect('Bazar:detail', $inzeratId);
	}

	public function actionSmazat($inzeratId)
	{
    $inzerat = $this->bazar->findById($inzeratId);
    if (!$inzerat) {
      $this->error('Požadovaný inzerát neexistuje.');
    }

    $destDir = $this->context->parameters['wwwDir'] . '/img/bazar';
    $soubory = array($inzerat->foto1, $inzerat->foto2, $inzerat->foto3);
    foreach ($soubory as $soubor) {
      if(!$soubor) continue;
      if(file_exists($destDir . '/' . $soubor)) unlink($destDir . '/' . $soubor);
      if(file_exists($destDir . '/thumb-' . $soubor)) unlink($destDir . '/thumb-' . $soubor);
    }

    $this->bazar->smazat($inzeratId);
    $this->flashMessage('Inzerát byl smazán.' , 'success');
    $this->redirect('Bazar:default');
	}
  
}

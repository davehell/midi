<?php

namespace HudbaModule;

use Nette\Forms\Form,
    Nette\Mail\Message,
    Nette\Mail\SendmailMailer;

/**
 * Homepage presenter.
 */
class AgenturaPresenter extends \BasePresenter
{

	/** @var Agentura @inject*/
	public $agentura;

  /**
   * @return Nette\Application\UI\Form
   */
  protected function createComponentPoptavkaForm()
  {
    $form = new \Nette\Application\UI\Form;

    $form->addText('nazev', 'Název:')
      ->setRequired('Prosím zadejte název kapely.')
      ->addRule(Form::MAX_LENGTH, 'Název kapely musí mít maximálně %d znaků', 100);

    $form->addTextArea('popis', 'Popis:')
      ->setRequired('Prosím zadejte popis kapely.')
      ->addRule(Form::MAX_LENGTH, 'Kontaktní údaje musí mít maximálně %d znaků', 1000);

    $form->addTextArea('kontakt', 'Kontaktní údaje:')
      ->setRequired('Prosím zadejte kontaktní údaje inzerátu.')
      ->addRule(Form::MAX_LENGTH, 'Kontaktní údaje musí mít maximálně %d znaků', 200);


    $form->addText('www', 'Webové stránky:')
      ->addRule(Form::MAX_LENGTH, 'Webové stránky musí mít maximálně %d znaků', 200);

    $form->addRadioList('zastupovat', 'Požadujete zastupování:', array('1'=>'ano','0'=>'ne'));


    $form->addSubmit('send', 'Odeslat');

    $form->onSuccess[] = $this->poptavkaFormSucceeded;

    return \Bs3Form::transform($form);
  }

  public function poptavkaFormSucceeded($form)
  {
    $values = $form->getValues();

    $text = "Název:\n" . $values['nazev'] . "\n";
    $text .= "Popis:\n" . $values['popis'] . "\n";
    $text .= "Kontakt:\n" . $values['kontakt'] . "\n";
    $text .= "Web:\n" . $values['www'] . "\n";
    $text .= "Zastupovat:\n";
    $text .= $values['popis'] == "1" ? "ano" : "ne" . "\n";

    $params = $this->context->parameters['hudba'];

    $mail = new Message;
    $mail->setFrom('Lubomír Piskoř <' . $params['adminMail'] . '>')
        ->addTo($params['adminMail'])
        ->setSubject('Hudební agentura - poptávka zveřejnění kapely')
        ->setBody($text);
    $mailer = new SendmailMailer;
    $mailer->send($mail);


    $this->flashMessage('Objednávka byla odeslána.' , 'success');
    $this->redirect('Agentura:default');
  }

  public function kapelaFormSucceeded($form)
  {
    $values = $form->getValues();

    $kapelaId = $this->getParameter('id');


    if($kapelaId) { //editace
//       try {
//         $this->skladby->update($skladbaId, $values);
//       } catch (\Exception $e) {
//         $this->flashMessage('Skladbu se nepodařilo uložit.', 'danger');
//       }
    }
    else { //nova skladba
      try {
        $kapela = $this->agentura->insert($values);
        $kapelaId = $kapela->id;
      } catch (\Exception $e) {
        $this->flashMessage('Objednávku se nepodařilo uložit.', 'danger');
      }
    }

    $this->flashMessage('Objednávka byla odeslána.' , 'success');
    $this->redirect('Agentura:default');
  }

	public function renderDefault()
	{
    $kapely = $this->agentura->findAll();
    $this->template->kapely = $kapely;
	}

}

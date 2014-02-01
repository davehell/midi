<?php

use Nette\Mail\Message,
    Nette\Mail\SendmailMailer;
/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{

  public function sendMail($templateName, $adresat, $info)
  {
    $params = $this->context->parameters['midi'];

    $template = $this->createTemplate();
    $template->setFile($this->context->parameters['appDir'] . '/templates/Email/' . $templateName);
    $template->registerFilter(new Nette\Latte\Engine);
    $template->registerHelperLoader('Nette\Templating\Helpers::loader');

    $template->info = $info;
    $template->params = $params;

    $mail = new Message;
    $mail->setFrom('Lubomír Piskoř <' . $params['adminMail'] . '>')
        ->addTo('david.hellebrand@seznam.cz')
        ->setHtmlBody($template);
    $mailer = new SendmailMailer;
    $mailer->send($mail);
  }

}

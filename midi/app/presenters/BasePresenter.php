<?php

use Nette\Mail\Message,
    Nette\Mail\SendmailMailer;
/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{

  public function sendMail($template, $adresat, $info)
  {
    $adminMail = $this->context->parameters['midi']['adminMail'];

    $template = $this->createTemplate();
    $template->setFile($this->context->parameters['appDir'] . '/templates/Email/nakup.latte');
    $template->registerFilter(new Nette\Latte\Engine);
    $template->registerHelperLoader('Nette\Templating\Helpers::loader');

    $template->info = $info;
    $template->adminMail = $adminMail;

    $mail = new Message;
    $mail->setFrom('Lubomír Piskoř <' . $adminMail . '>')
        ->addTo('david.hellebrand@seznam.cz')
        ->setHtmlBody($template);
    $mailer = new SendmailMailer;
    $mailer->send($mail);
  }

}

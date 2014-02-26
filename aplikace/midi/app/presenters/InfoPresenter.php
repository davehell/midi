<?php


/**
 * Homepage presenter.
 */
class InfoPresenter extends BasePresenter
{

	/** @var Skladba @inject*/
	public $skladby;

  public function startup()
  {
    parent::startup();
    $this->template->cisloUctu = $this->context->parameters['midi']['cisloUctu'];
  }

	public function renderDefault()
	{
    $this->template->oblibene = $this->skladby->oblibene();
    $this->template->novinky = $this->skladby->novinky();
    $this->template->pocet = $this->skladby->pocetSkladeb();
    $this->template->formaty = $this->skladby->seznamFormatu('plneVerze');
	}

	public function renderVyrobaNaZakazku()
	{
    $this->template->formaty = $this->skladby->seznamFormatu('plneVerze');
	}
}

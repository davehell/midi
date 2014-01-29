<?php


/**
 * Homepage presenter.
 */
class HomepagePresenter extends BasePresenter
{

	/** @var Skladba @inject*/
	public $skladby;

	public function renderDefault()
	{
    $this->template->oblibene = $this->skladby->oblibene();
    $this->template->novinky = $this->skladby->novinky();
    $this->template->pocet = $this->skladby->pocetSkladeb();
    $this->template->formaty = $this->skladby->seznamFormatu('plneVerze');
	}

}

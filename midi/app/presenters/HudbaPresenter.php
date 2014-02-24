<?php


/**
 * Homepage presenter.
 */
class HudbaPresenter extends BasePresenter
{
  protected function beforeRender()
  {
    parent::beforeRender();
    $this->setLayout('layoutHudba');
  }

	public function renderDefault()
	{
	}

}

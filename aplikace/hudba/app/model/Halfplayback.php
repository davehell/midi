<?php

class Halfplayback extends Nette\Object
{
	/** @var Nette\Database\Context */
	private $database;


	public function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
	}


	/** @return Nette\Database\Table\Selection */
	public function findAll()
	{
		return $this->database->table('hudba_hpback')->order('nazev ASC');
	}

	/** @return Nette\Database\Table\ActiveRow */
	public function findById($id)
	{
		return $this->findAll()->get($id);
	}

	public function update($id, $values)
	{
		$this->database->table('hudba_hpback')->wherePrimary($id)->update($values);
	}

  /** @return Nette\Database\Table\ActiveRow */
	public function insert($skladba)
	{
    return $this->database->table('hudba_hpback')->insert($skladba);
	}

	public function smazat($id)
	{
    $this->database->table('hudba_hpback')->wherePrimary($id)->delete();
	}
}

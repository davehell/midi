<?php

class Vydavatelstvi extends Nette\Object
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
		return $this->database->table('hudba_cd')->order('nazev ASC');
	}

	/** @return Nette\Database\Table\ActiveRow */
	public function findById($id)
	{
		return $this->findAll()->get($id);
	}

	public function update($cdId, $values)
	{
		$this->database->table('hudba_cd')->wherePrimary($cdId)->update($values);
	}

  /** @return Nette\Database\Table\ActiveRow */
	public function insert($cd)
	{
    return $this->database->table('hudba_cd')->insert($cd);
	}
}

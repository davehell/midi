<?php


class Bazar extends Nette\Object
{
	/** @var Nette\Database\Context */
	private $database;


	public function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
	}


	/** @return Nette\Database\Table\Selection */
	public function findAll($limit = null, $offset = null)
	{
		return $this->database->table('hudba_bazar')->order('datum DESC')->limit($limit, $offset);
	}

	/** @return Nette\Database\Table\ActiveRow */
	public function findById($id)
	{
		return $this->findAll()->get($id);
	}

// 	public function pocetInzeratu()
// 	{
//     return $this->findAll()->count();
// 	}

	public function update($inzerateId, $values)
	{
		$this->database->table('hudba_bazar')->wherePrimary($inzerateId)->update($values);
	}

  /** @return Nette\Database\Table\ActiveRow */
	public function insert($inzerat)
	{
    $inzerat['datum'] = new Nette\Database\SqlLiteral('NOW()');
    return $this->database->table('hudba_bazar')->insert($inzerat);
	}
}

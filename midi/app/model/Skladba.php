<?php


class Skladba extends Nette\Object
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
		return $this->database->table('skladba');
	}

	/** @return Nette\Database\Table\ActiveRow */
	public function findById($id)
	{
		return $this->findAll()->get($id);
	}


	/** @return Nette\Database\Table\Selection */
	public function formatySkladby($id)
	{
		return $this->database->table('soubor')->where('skladba_id', $id);
	}

  /** @return array */
	public function seznamZanru()
	{
    return $this->database->table('zanr')->fetchPairs('id', 'nazev');
	}

  /** @return array */
	public function seznamFormatu($demo = null)
	{
    return $this->database->table('format')->where('demo', $demo == 'demo' ? 1 : 0)->fetchPairs('id', 'nazev');
	}


	/** @return Nette\Database\Table\Selection */
	public function prehledStahovani()
	{
    return $this->database->table('nakup')->select('*, count(*) AS pocet')->group('skladba_id');
	}

	public function update($skladbaId, $values)
	{
		$this->database->table('skladba')->wherePrimary($skladbaId)->update($values);
	}

  /** @return Nette\Database\Table\ActiveRow */
	public function insert($skladba)
	{
    $skladba['datum_pridani'] = new Nette\Database\SqlLiteral('NOW()');
    return $this->database->table('skladba')->insert($skladba);
	}

  /** @return Nette\Database\Table\ActiveRow */
	public function ulozitSoubory($soubory, $adresar)
	{
    foreach ($soubory as $soubor) {
      $this->database->table('soubor')->where('skladba_id', $soubor['skladba_id'])->where('format_id', $soubor['format_id'])->delete();
      if(file_exists($adresar . '/' . $soubor['nazev'])) unlink($adresar . '/' . $soubor['nazev']);
    }
    return $this->database->table('soubor')->insert($soubory);
	}
}

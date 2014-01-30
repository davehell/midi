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
	public function findAll($limit = null, $offset = null)
	{
		$skladby = $this->database->table('skladba')->limit($limit, $offset);
    return $skladby;
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
	public function prehledStahovani($od, $do)
	{
    if(!$od || !$do) return null;
    return $this->database->table('nakup')->select('skladba_id, cena, count(*) AS pocet')->where('datum >= ?', $od)->where('datum <= ?', $do)->group('skladba_id');
	}

	/** @return Nette\Database\Table\Selection */
	public function oblibene()
	{
    $od = '2007-01-01';
    $do = date('Y-m-d');
    return $this->prehledStahovani($od, $do)->order('pocet DESC')->limit(10);
	}

	/** @return Nette\Database\Table\Selection */
	public function novinky()
	{
    return $this->findAll()->order('datum_pridani DESC')->limit(10);
	}

	public function pocetSkladeb()
	{
    return $this->database->table('skladba')->count();
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
    }
    return $this->database->table('soubor')->insert($soubory);
	}

	public function nazevSouboru($id)
	{
    return $this->database->table('soubor')->get($id);
	}

  /** @return null */
	public function exportNazvuSkladeb($soubor)
	{
    $skladby = $this->database->table('skladba')->fetchPairs('id', 'nazev');
    $eol = "\r\n";
    $handle = fopen('safe://' . $soubor, 'w');
    fwrite($handle, '[' . $eol);
    foreach ($skladby as $id => $nazev) {
      // {"id":"1","value":"typeahead.js"}
      fwrite($handle, '{"id":' . $id . ',"value":"' . $nazev . '"},' . $eol);
    }
    fwrite($handle, '{}' . $eol);
    fwrite($handle, ']' . $eol);
    fclose($handle);
	}
}

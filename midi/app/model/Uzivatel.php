<?php
use Nette\Utils\Strings;

class Uzivatel extends Nette\Object
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
		return $this->database->table('uzivatel')->select('id, login, email, posledni_prihlaseni, datum_registrace, kredit');
	}

	/** @return Nette\Database\Table\ActiveRow */
	public function findById($id)
	{
		return $this->findAll()->get($id);
	}

	/** @return array */
	public function findAllDetails()
	{
    return $this->database->query('SELECT uzivatel.id AS id, login, email, posledni_prihlaseni, datum_registrace, kredit, sum(cena) AS nakoupeno FROM uzivatel LEFT JOIN nakup ON uzivatel.id = nakup.uzivatel_id GROUP BY uzivatel.id')->fetchAll();
	}

  /** @return string */
	public function findSalt($id)
	{
		return $this->database->table('uzivatel')->select('salt')->get($id)->offsetGet('salt');
	}

	public function update($id, $values)
	{
    if($values->offsetExists('heslo')) {
      $values->heslo = UserManager::generateHash($values->heslo, $this->findSalt($id));
    }
		return $this->database->table('uzivatel')->wherePrimary($id)->update($values);
	}

	public function casPoslPrihlaseni($uziv, $cas = NULL)
	{
    if($cas === NULL) $cas = new Nette\Database\SqlLiteral('NOW()');
		$this->database->table('uzivatel')
      ->where('id', $uziv)
      ->update(array('posledni_prihlaseni' => $cas));
	}

	/** @return Nette\Database\Table\Selection */
	public function zakoupeneSkladby($uziv)
	{
		return $this->database->table('nakup')->where('uzivatel_id', $uziv);
	}

  /** @return integer */
	public function sumaNakupu($uziv)
	{
    $suma = $this->zakoupeneSkladby($uziv)->sum('cena');
    return $suma ?: 0;
	}

  /** @return boolean */
	public function maZakoupeno($uziv, $skladba)
	{
    return $this->zakoupeneSkladby($uziv)->where('skladba_id', $skladba)->count() ? true : false;
	}

  /** @return Nette\Database\Table\ActiveRow */
	public function pridatPozadavekNaNabiti($uzivId, $castka)
	{
    $values = array(
      'uzivatel_id' => $uzivId,
      'castka' => $castka,
      'vs' => strtotime("now"),
      'datum' => new Nette\Database\SqlLiteral('NOW()')
    );
    return $this->database->table('dobijeni')->insert($values);
	}

  /** @return integer */
	public function zrusitPozadavekNaNabiti($transId)
	{
    return $this->database->table('dobijeni')->wherePrimary($transId)->delete();
	}

  /** @return Nette\Database\Table\ActiveRow */
	public function pripsatKredit($trans)
	{
    $this->zmenaVyseKreditu($trans->uzivatel, $trans->castka);

    $values = array(
      'vyrizeno' => new Nette\Database\SqlLiteral('NOW()')
    );
    return $this->database->table('dobijeni')->wherePrimary($trans->id)->update($values);
	}

  /** @return Nette\Database\Table\ActiveRow */
	public function odebratKredit($uziv, $castka)
	{
    if($uziv->kredit - $castka < 0) {
      throw new \Exception('Zadána příliš vysoká částka - kredit by byl záporný');
    }

    $this->zmenaVyseKreditu($uziv, $castka * (-1));

    $arr = array(
      'uzivatel_id' => $uziv->id,
      'castka' => $castka * (-1),
      'vs' => 'vratka',
      'datum' => new Nette\Database\SqlLiteral('NOW()'),
      'vyrizeno' => new Nette\Database\SqlLiteral('NOW()')
    );
    return $this->database->table('dobijeni')->insert($arr);
	}

	private function zmenaVyseKreditu($uziv, $castka)
	{
    $values = array(
      'kredit' => $uziv->kredit + $castka
    );
    $this->database->table('uzivatel')->wherePrimary($uziv->id)->update($values);
	}

	/** @return Nette\Database\Table\Selection */
	public function historieDobijeni($uziv)
	{
		return $this->database->table('dobijeni')->select('castka, vs, datum, vyrizeno')->where('uzivatel_id', $uziv);
	}

  /** @return Nette\Database\Table\ActiveRow */
	public function posledniDobiti($uziv)
	{
		return $this->historieDobijeni($uziv)->order('id DESC')->fetch();
	}

	public function koupitSkladbu($uzivId, $skladba)
	{
    $uziv = $this->findById($uzivId);
    if($skladba->cena > $uziv->kredit) {
      throw new \Exception('Nemáte dostatečný kredit pro nákup skladby.');
    }

    $arr = array(
      'uzivatel_id' => $uzivId,
      'skladba_id' => $skladba->id,
      'cena' => $skladba->cena,
      'datum' => new Nette\Database\SqlLiteral('NOW()')
    );
    $this->database->table('nakup')->insert($arr);

		$this->database->table('uzivatel')
      ->where('id', $uzivId)
      ->update(array('kredit' => $uziv->kredit - $skladba->cena));

		$this->database->table('skladba')
      ->where('id', $skladba->id)
      ->update(array('pocet_stazeni' => $skladba->pocet_stazeni + 1));
	}

	/** @return Nette\Database\Table\Selection */
	public function cekajiciNaDobiti()
	{
		return $this->database->table('dobijeni')->where('vyrizeno', null);
	}

	/**
	 * Adds new user.
	 * @param  string
	 * @param  string
	 * @param  string
	 * @return void
	 */
	public function registrace($username, $password, $email)
	{
    $salt = Strings::random(20);
		$this->database->table('uzivatel')->insert(array(
			'login' => $username,
      'salt' => $salt,
			'heslo' => UserManager::generateHash($password, $salt),
      'email' => $email,
      'datum_registrace' => new Nette\Database\SqlLiteral('NOW()')
		));
	}
}

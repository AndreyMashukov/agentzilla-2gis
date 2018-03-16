<?php

/**
 * PHP version 7.1
 *
 * @package Agentzilla\Gis
 */

namespace Agentzilla\Gis;

/**
 * Class for address slicing
 *
 * @author  Andrey Mashukov <a.mashukoff@gmail.com>
 * @version SVN: $Date: 2018-02-03 20:22:06 +0000 (Sat, 03 Feb 2018) $ $Revision: 3 $
 * @link    $HeadURL: https://svn.agentzilla.ru/2gis/trunk/src/AddressSlicer.php $
 */

class AddressSlicer
    {

	/**
	 * City
	 *
	 * @var string
	 */
	public $city;

	/**
	 * Street
	 *
	 * @var string
	 */
	public $street;

	/**
	 * House
	 *
	 * @var string
	 */
	public $house;

	/**
	 * Number of house
	 *
	 * @var string
	 */
	public $numberhouse = null;

	/**
	 * Building
	 *
	 * @var string
	 */
	public $building = null;

	/**
	 * Multihouse
	 *
	 * @var bool
	 */
	public $multihouse = false;

	/**
	 * Hash
	 *
	 * @var string
	 */
	public $hash = null;

	/**
	 * Address
	 *
	 * @var string
	 */
	public $address = "";

	/**
	 * Construct class
	 *
	 * @param string $address Address to slice
	 *
	 * @return void
	 */

	public function __construct(string $address)
	    {
		$sliced = $this->_slice($address);

		$this->city   = $sliced["city"];
		$this->street = $sliced["street"];
		$this->house  = $sliced["house"];

		$this->_prepare();
		if ($this->valid() === true)
		    {
			$this->hash    = sha1(mb_strtolower($this->city . $this->street . $this->house));
			$this->address = $this->street . ", " . $this->house;
		    } //end if

	    } //end __construct()


	/**
	 * Prepare house parameters
	 *
	 * @return void
	 */

	private function _prepare()
	    {
		if ($this->house !== null)
		    {
			$this->house = $this->_replacer($this->house);

			if (preg_match("/^[0-9]+[-]{1}[0-9]+$/ui", $this->house) > 0)
			    {
				$this->multihouse = true;
			    } //end if

			if (preg_match("/(?P<number>[-0-9]+)(\/|к|с)+(?P<building>[-0-9]+)/ui", $this->house, $params) > 0)
			    {
				$this->numberhouse = $params["number"];
				$this->building    = $params["building"];
			    }
			else if (preg_match("/(?P<number>[-0-9]+)?(\/|к|с)?+(?P<building>[-0-9]+)?/ui", $this->house, $params) > 0)
			    {
				if (isset($params["number"]) === true)
				    {
					$this->numberhouse = $params["number"];
				    } //end if

				if (isset($params["building"]) === true)
				    {
					$this->building = $params["building"];
				    } //end if

			    } //end if

		    }
	    } //end _prepare()


	/**
	 * Replace strings
	 *
	 * @param string $string String to replace
	 *
	 * @return string String with replaces
	 */

	private function _replacer(string $string):string
	    {
		$string = preg_replace("/ст(роение|р\.?)?/ui", "с", $string);
		$string = preg_replace("/(корп(\.|ус)?)/ui", "к", $string);
		$string = preg_replace("/(\s+|\")/ui", "", $string);

		return $string;
	    } //end _replacer()


	/**
	 * Check valid address
	 *
	 * @return bool valid status
	 */

	public function valid()
	    {
		if ($this->city !== null && $this->street !== null && $this->house !== null)
		    {
			return true;
		    }
		else
		    {
			return false;
		    }

	    } //end valid()


	/**
	 * Slice address
	 *
	 * @param string $address Address to slice
	 *
	 * @return array Sliced data
	 */

	private function _slice(string $address):array
	    {
		$sliced = array(
			   "city"   => null,
			   "street" => null,
			   "house"  => null,
			  );

		if (preg_match("/(?P<city>[- А-Яа-я0-9]+),\s{0,2}(ул(ица)?)?[.]?\s{0,2}([-A-Za-z0-9А-Яа-я\"' ,]+\s+жилой\s+комплекс[., '\"]+)?(?P<street>([-а-яёА-Я№#()\" ]+)?[0-9]{0,3}[-]?\s?[ая]{0,2}\s?(([-А-Яа-яё]{0,1}[.]?\s?)?([-а-яёА-Я0-9№#()\" ]+)?([-А-Яёа-я]{0,1}[.]?\s?)?([0-9]+\s?[-А-Яёа-я]+)?[-А-Яёа-я()\"]{2,25}\s?)+((пр(осп|оезд|оспект|-кт)?|б(ульвар|-р)?)[.]?)?)(пр(оезд|оспект)?[.]?)?(ул(ица)?[.]?)?[,]?(.*[\"].*[\"])?(\s{0,3}д(ом)?[.]?)?\s{0,3}(?P<house>[0-9]+\s?[А-Яёа-я\"]{0,3}\s?((\/|корп\.?|корпус|[корпустение. ]+|-)?\s?[0-9А-Яёа-я\"]{0,6}(к|[корпустение. ]+|-)?[0-9]{0,3})?)/ui", $address, $data) > 0)
		    {
			foreach ($sliced as $key => $search)
			    {
				if (isset($data[$key]) === true)
				    {
					$sliced[$key] = trim($data[$key]);
				    }
			    }
		    }

		return $sliced;
	    } //end _slice()


    } //end class

?>

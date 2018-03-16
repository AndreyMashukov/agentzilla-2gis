<?php

/**
 * PHP version 7.1
 *
 * @package Agentzilla\Gis
 */

namespace Agentzilla\Gis;

use \Agentzilla\Gis\AddressSlicer;
use \AdService\Comparison;
use \DOMDocument;
use \DOMXPath;
use \Logics\Foundation\HTTP\HTTPclient;
use \Logics\Foundation\SQL\MySQLdatabase;
use \SimpleXMLElement;

/**
 * Class for getting address info from 2gis
 *
 * @author  Andrey Mashukov <a.mashukoff@gmail.com>
 * @version SVN: $Date: 2018-02-03 20:22:06 +0000 (Sat, 03 Feb 2018) $ $Revision: 3 $
 * @link    $HeadURL: https://svn.agentzilla.ru/2gis/trunk/src/GisInfo.php $
 */

class GisInfo
    {

	/**
	 * Location information
	 *
	 * @var array
	 */
	public $info = null;

	/**
	 * Floors on location
	 *
	 * @var int
	 */
	public $floors = null;

	/**
	 * City
	 *
	 * @var string
	 */
	private $_city;

	/**
	 * District
	 *
	 * @var string
	 */
	public $district = null;

	/**
	 * Database connection
	 *
	 * @var MySQL
	 */
	private $_db = null;

	/**
	 * Object location
	 *
	 * @var mixed null or location array
	 */
	public $location = null;

	/**
	 * Construct class
	 *
	 * @param string $city    City
	 * @param string $address Address to get information
	 *
	 * @return void
	 */

	public function __construct(string $city, string $address)
	    {
		$this->_city = $city;
		$this->info  = $this->_getInformation($address);

		if ($this->info !== null)
		    {
			$this->floors   = $this->info["floors"];
			$this->district = $this->info["district"];
			$this->location = $this->info["location"];
		    } //end if

	    } //end __construct()


	/**
	 * Define district by last defined locations
	 *
	 * @return void
	 */

	public function defineDistrict()
	    {
		if ($this->location !== null)
		    {
			if ($this->_db !== null)
			    {
				$lat1  = $this->location["lat"] - 0.002;
				$lat2  = $this->location["lat"] + 0.002;
				$lang1 = $this->location["lang"] - 0.002;
				$lang2 = $this->location["lang"] + 0.002;

				$result = $this->_db->exec("SELECT * FROM `districts_locations` WHERE " .
				    "`lat` >= " . $this->_db->sqlText($lat1) . " AND " .
				    "`lat` <= " . $this->_db->sqlText($lat2) . " AND " .
				    "`lang` >= " . $this->_db->sqlText($lang1) . " AND " .
				    "`lang` <= " . $this->_db->sqlText($lang2) . " AND" .
				    "`city` = " . $this->_db->sqlText(sha1($this->_city))
				);

				if ($result->getNumRows() > 0)
				    {
					$districts = [];
					while ($row = $result->getRow())
					    {
						$d             = 6371000 * acos(sin(deg2rad($this->location["lat"])) * sin(deg2rad($row["lat"])) + cos(deg2rad($this->location["lat"])) * cos(deg2rad($row["lat"])) * cos(deg2rad($this->location["lang"]) - deg2rad($row["lang"])));
						$districts[$d] = $row["district"];
					    }

					ksort($districts);

					$this->district = array_shift($districts);
				    }

			    } //end if

		    } //end if

	    } //end defineDistrict()


	/**
	 * Set database connection
	 *
	 * @param MySQLdatabase $db Database connection
	 *
	 * @return void
	 */

	public function setConnection(MySQLdatabase $db)
	    {
		$this->_db = $db;
	    } //end setConnection()


	/**
	 * Write location data to database
	 *
	 * @return void
	 */

	public function write()
	    {
		if ($this->_db !== null && $this->location !== null)
		    {
			$sig    = sha1($this->location["lat"] . $this->location["lang"]);
			$result = $this->_db->exec("SELECT `id` FROM `districts_locations` WHERE `sig` = " . $this->_db->sqlText($sig));
			if ((int) $result->getNumRows() === 0)
			    {
				$this->_db->exec("INSERT INTO `districts_locations` SET " .
				    "`lat` = " . $this->_db->sqlText($this->location["lat"]) . ", " .
				    "`lang` = " . $this->_db->sqlText($this->location["lang"]) . ", " .
				    "`district` = " . $this->_db->sqlText($this->district) . ", " .
				    "`sig` = " . $this->_db->sqlText($sig) . ", " .
				    "`city` = " . $this->_db->sqlText(sha1($this->_city))
				);
			    } //end if

		    } //end if

	    } //end write()


	/**
	 * Set location
	 *
	 * @param array $location Coordinates of location
	 *
	 * @return void
	 */

	public function setLocation(array $loc)
	    {
		if ($loc["lat"] !== false && $loc["lang"] !== false)
		    {
			$this->location = ["lat" => $loc["lat"], "lang" => $loc["lang"]];
		    } //end if

	    } //end setLocation()


	/**
	 * Get information
	 *
	 * @param string $address Address of object
	 *
	 * @return mixed null or array Information
	 */

	private function _getInformation(string $address)
	    {
		$info  = [
		    "floors"   => null,
		    "district" => null,
		    "location" => null,
		];

		$config = new SimpleXMLElement(file_get_contents(__DIR__ . "/config/config.xml"));

		$hosts = [];

		foreach ($config->Hosts->Host as $host)
		    {
			$hosts[(string) $host["city"]] = (string) $host;
		    } //end foreach

		if (isset($hosts[$this->_city]) === true)
		    {
			$host = $hosts[$this->_city];
			$http = new HTTPclient($host . "/search/" . urlencode($address));
			do
			    {
				$html = $http->get();
			    }
			while($http->lastcode() === 0);
			$xpath  = $this->_getXPath($html);
			$list   = $xpath->query("//a[@class='link miniCard__headerTitleLink']");

			if ($list->{"length"} > 0)
			    {
				$obj_address   = $list[0]->textContent;
				$obj_link      = trim($list[0]->getAttribute("href"));

				$doubleaddress = $this->_checkDoubleAddress($obj_address);

				if ($doubleaddress === false)
				    {
					$info = $this->_getAddressInfo($address, $obj_address, $obj_link);
				    }
				else
				    {
					foreach ($doubleaddress as $obj_address)
					    {
						$info = $this->_getAddressInfo($address, $obj_address, $obj_link);
						if ($info["floors"] !== null)
						    {
							break;
						    } //end if

					    } //end foreach

				    } //end if

			    } //end if

		    } //end if

		return $info;
	    } //end _getInformation()


	/**
	 * Get address information
	 *
	 * @param string $firstaddress  First address
	 * @param stirng $secondaddress Second address
	 * @param string $link          Link to object
	 *
	 * @return mixed null or array info
	 */

	private function _getAddressInfo(string $firstaddress, string $secondaddress, string $link)
	    {
		$second = $this->_city . ", " . $secondaddress;

		$a = new AddressSlicer($second);
		$b = new AddressSlicer($firstaddress);

		if ($a->valid() === false)
		    {
			$address = $this->_getAddress($link);
			if ($address !== null)
			    {
				$a = new AddressSlicer($address);
			    } //end if

		    } //end if

		if ($a->valid() === true && $b->valid() === true)
		    {
			$comparison = new Comparison($a->street, $b->street);
			if (
			    $comparison->percent >= 20 && mb_strtoupper($b->numberhouse) === mb_strtoupper($a->numberhouse) && mb_strtoupper($a->building) === mb_strtoupper($b->building) && $b->city === $a->city ||
			    $comparison->match === true && mb_strtoupper($b->numberhouse) === mb_strtoupper($a->numberhouse) && mb_strtoupper($a->building) === mb_strtoupper($b->building)  && $b->city === $a->city
			)
			    {
				return $this->_getLocInfo($link);
			    }
			else if ($comparison->percent >= 20 && $b->city === $a->city && $a->multihouse === true || $comparison->match === true && $b->city === $a->city && $a->multihouse === true )
			    {
				$houses = explode("-", $a->house);
				if (count($houses) > 1)
				    {
					$from  = preg_replace("/\D/ui", "", $houses[0]);
					$to    = preg_replace("/\D/ui", "", $houses[1]);
					$house = preg_replace("/\D/ui", "", $b->house);
					if ($from <= $house && $house <= $to)
					    {
						return $this->_getLocInfo($link);
					    } //end if

				    } //end if

			    } //end if

		    } //end if

		return null;
	    } //end _getAddressInfo()


	/**
	 * Check double address
	 *
	 * @param string $address Address to check
	 *
	 * @return mixed False or addresses array
	 */

	private function _checkDoubleAddress(string $address)
	    {
		if (preg_match("/(?P<first>.*)\s{1}[\/]{1}\s{1}(?P<second>.*)/ui", $address, $params) > 0)
		    {
			if (strlen($params["first"]) >= 8 && strlen($params["second"]) >= 8)
			    {
				return [
				    trim($params["first"]),
				    trim($params["second"]),
				];
			    }
			else
			    {
				return false;
			    } //end if

		    }
		else
		    {
			return false;
		    } //end if

	    } //end _checkDoubleAddress()


	/**
	 * Get location info
	 *
	 * @param string $link Link to get floors
	 *
	 * @return array info
	 */

	private function _getLocInfo(string $link)
	    {
		$info = ["floors" => null, "district" => null, "location" => null];

		$http = new HTTPclient("https://2gis.ru" . $link);
		do
		    {
			$htmlobject = $http->get();
		    }
		while ($http->lastcode() === 0);

		$xpath = $this->_getXPath($htmlobject);
		$list  = $xpath->query("//div[@class='cardFeatures__item']/div[@class='_purpose_shortbuildinginfo _dot cardFeaturesItem' or @class='_purpose_building cardFeaturesItem']");
		if ($list->{"length"} > 0)
		    {
			$result = preg_replace("/\D/ui", "", $list[0]->textContent);
			if ($result > 0)
			    {
				$info["floors"] = $result;
			    } //end if

		    } //end if

		$list = $xpath->query("//div[@data-module='cardFeaturesItem']");
		if ($list->{"length"} > 0)
		    {
			$result = $list[0]->textContent;
			$expl   = explode(",", $result);

			if (is_numeric($expl[0]) === false)
			    {
				$info["district"] = $expl[0];
			    } //end if

		    } //end if 

		$list = $xpath->query("//meta[@name='twitter:image:src']/@content");
		if ($list->{"length"} > 0)
		    {
			$result = urldecode($list[0]->textContent);
			if (preg_match("/center=(?P<lang>[0-9]+\.[0-9]+),(?P<lat>[0-9]+\.[0-9]+)&title=/ui", $result, $params) > 0)
			    {
				$info["location"] = ["lat" => $params["lat"], "lang" => $params["lang"]];
			    } //end if

		    } //end if 

		return $info;
	    } //end _getLocInfo()


	/**
	 * Get address
	 *
	 * @param string $link Link to get floors
	 *
	 * @return string Address or null
	 */

	private function _getAddress(string $link)
	    {
		$address = null;

		$http = new HTTPclient("https://2gis.ru" . $link);
		do
		    {
			$htmlobject = $http->get();
		    }
		while ($http->lastcode() === 0);

		$xpath = $this->_getXPath($htmlobject);
		$list  = $xpath->query("//a[@class='card__addressLink _undashed']");
		if ($list->{"length"} > 0)
		    {
			$address = trim(preg_replace("/г\. " . $this->_city . ",/ui", "", $list[0]->textContent));
		    } //end if

		return $address;
	    } //end _getAddress()


	/**
	 * Get XPath
	 *
	 * @param string $html Html to load
	 *
	 * @return DOMXPath
	 */

	private function _getXPath(string $html):DOMXPath
	    {
		$doc = new DOMDocument();
		@$doc->loadHTML($html);
		unset($html);
		$xpath = new DOMXPath($doc);

		return $xpath;
	    } //end _getXPath()


    } //end class


?>

<?php

/**
 * PHP version 7.1
 *
 * @package Agentzilla\Gis
 */

namespace Tests;

use \Agentzilla\Gis\GisInfo;
use \PHPUnit\Framework\TestCase;
use \Logics\Foundation\SQL\SQL;

/**
 * Gis information test
 *
 * @author  Andrey Mashukov <a.mashukoff@gmail.com>
 * @version SVN: $Date: 2018-02-03 20:22:06 +0000 (Sat, 03 Feb 2018) $ $Revision: 3 $
 * @link    $HeadURL: https://svn.agentzilla.ru/2gis/trunk/tests/GisInfoTest.php $
 *
 * @runTestsInSeparateProcesses
 */

class GisInfoTest extends TestCase
    {

	/**
	 * Prepare data for testing
	 *
	 * @return void
	 */

	public function setUp()
	    {
		$db = SQL::get("MySQL");
		$db->exec("
		    CREATE TABLE IF NOT EXISTS `districts_locations` (
		      `id` int(10) NOT NULL AUTO_INCREMENT,
		      `city` char(40) COLLATE utf8mb4_unicode_ci NOT NULL,
		      `district` char(255) COLLATE utf8mb4_unicode_ci NOT NULL,
		      `lat` double NOT NULL,
		      `lang` double NOT NULL,
		      `sig` char(40) COLLATE utf8mb4_unicode_ci NOT NULL,
		      PRIMARY KEY (`id`)
		    ) ENGINE=InnoDB;");
	    } //end setUp()


	/**
	 * Tear down test data
	 *
	 * @return void
	 */

	public function tearDown()
	    {
		$db = SQL::get("MySQL");
		$db->exec("DROP TABLE `districts_locations`");
	    } //end tearDown()


	/**
	 * Should return object information
	 *
	 * @return void
	 */

	public function testShouldReturnObjectInformation()
	    {
		$addresses = [
		    "Иркутск, Александра Невского, 15" => array(
						      "city"     => "Иркутск",
						      "floors"   => "8",
						      "district" => "Октябрьский округ",
						     ),
		    "Иркутск, Култукская, 107Б" => array(
						      "city"     => "Иркутск",
						      "floors"   => "2",
						      "district" => "Правобережный округ",
						     ),
		    "Иркутск, Александра Невского, 6" => array(
						      "city"     => "Иркутск",
						      "floors"   => "12",
						      "district" => "Октябрьский округ",
						     ),
		    "Иркутск, А. Невского, 6" => array(
						      "city"     => "Иркутск",
						      "floors"   => "12",
						      "district" => "Октябрьский округ",
						     ),
		    "Сочи, Островского 67" => array(
						      "city"     => "Сочи",
						      "floors"   => "14",
						      "district" => "Центральный район",
						     ),
		    "Сочи, Просвещения 147/1" => array(
						      "city"     => "Сочи",
						      "floors"   => "10",
						      "district" => "Адлер",
						     ),
		    "Москва, Николоямская, 39/43к1" => array(
						      "city"     => "Москва",
						      "floors"   => "8",
						      "district" => "Таганский район",
						     ),
		    "Екатеринбург, Братская, 27/1" => array(
						      "city"     => "Екатеринбург",
						      "floors"   => "26",
						      "district" => "Вторчермет",
						     ),
		    "Москва, маршала захарова, 13" => array(
						      "city"     => "Москва",
						      "floors"   => "17",
						      "district" => "Орехово-Борисово Северное район",
						     ),
		    "Красноярск, им Героя Советского Союза В.В.Вильского ул, 28" => array(
						      "city"     => "Красноярск",
						      "floors"   => "16",
						      "district" => "Октябрьский район",
						     ),
		    "Москва, Алтуфьевское шоссе, 93" => array(
						      "city"     => "Москва",
						      "floors"   => "12",
						      "district" => "Лианозово район",
						     ),
		    "Москва, Вернадского пр-кт, 92" => array(
						      "city"     => "Москва",
						      "floors"   => "22",
						      "district" => "Тропарево-Никулино район",
						     ),
		    "Москва, Маросейка ул, 13с3" => array(
						      "city"     => "Москва",
						      "floors"   => "7",
						      "district" => "Басманный район",
						     ),
		    "Калининград, А.Суворова, 23В" => array(
						      "city"     => "Калининград",
						      "floors"   => "8",
						      "district" => "Московский район",
						     ),
		    "Москва, Дмитровское шоссе, 52к1" => array(
						      "city"     => "Москва",
						      "floors"   => "7",
						      "district" => "Тимирязевский район",
						     ),
		    "Москва, Крылатские холмы, 37" => array(
						      "city"     => "Москва",
						      "floors"   => "34",
						      "district" => "Крылатское район",
						     ),
		    "Калининград, Беланова, 35" => array(
						      "city"     => "Калининград",
						      "floors"   => "5",
						      "district" => "Чкаловск",
						     ),
		    "Санкт-Петербург, Товарищеский пр-кт, 2к1" => array(
						      "city"     => "Санкт-Петербург",
						      "floors"   => "9",
						      "district" => "МО №57 \"Правобережный\"",
						     ),
		    "Москва, Ленинский проспект, 79" => array(
						      "city"     => "Москва",
						      "floors"   => "9",
						      "district" => "Гагаринский район",
						     ),
		    "Москва, Панфёрова, 1" => array(
						      "city"     => "Москва",
						      "floors"   => "9",
						      "district" => "Гагаринский район",
						     ),
		    "Москва, Балтийская улица, 4" => array(
						      "city"     => "Москва",
						      "floors"   => "8",
						      "district" => "Аэропорт район",
						     ),
		];

		foreach ($addresses as $address => $data)
		    {
			$gis = new GisInfo($data["city"], $address);
			$this->assertEquals($data["floors"], $gis->floors);
			$this->assertEquals($data["district"], $gis->district);
		    }

	    } //end testShouldReturnObjectInformation()


	/**
	 * Should use MySQL database connection
	 *
	 * @return void
	 */

	public function testShouldUseMysqlDatabaseConnection()
	    {
		$gis = new GisInfo("Иркутск", "Иркутск, Депутатская, 87/2");

		$sql = SQL::get("MySQL");
		$gis->setConnection($sql);
		$this->assertTrue(true);
	    } //end testShouldUseMysqlDatabaseConnection()


	/**
	 * Should allow to set default location
	 *
	 * @return void
	 */

	public function testShouldAllowToSetDefaultLocation()
	    {
		$gis = new GisInfo("Иркутск", "Иркутск, Депутатская, 87/2");

		$gis->setLocation(["lat" => false, "lang" => 123.23435]);
		$this->assertEquals(["lang" => 104.342051, "lat" => 52.265052], $gis->location);

		$gis->setLocation(["lat" => 55.1234, "lang" => 123.23435]);
		$this->assertEquals(["lat" => 55.1234, "lang" => 123.23435], $gis->location);
	    } //end testShouldAllowToSetDefaultLocation()


	/**
	 * Should return object location
	 *
	 * @return void
	 */

	public function testShouldReturnObjectLocation()
	    {
		$object = array(
			   "city"     => "Иркутск",
			   "address"  => "Иркутск, Депутатская, 87/2",
			   "floors"   => 9,
			   "district" => "Октябрьский округ",
			   "location" => ["lang" => 104.342051, "lat" => 52.265052],
			  );

		$gis = new GisInfo($object["city"], $object["address"]);
		$this->assertEquals($object["floors"], $gis->floors);
		$this->assertEquals($object["district"], $gis->district);
		$this->assertEquals($object["location"], $gis->location);
	    } //end testShouldReturnObjectLocation()


	/**
	 * Should write location district with coordinates to database
	 *
	 * @return void
	 */

	public function testShouldWriteLocationDistrictWithCoordinatesToDatabase()
	    {
		$gis = new GisInfo("Иркутск", "Иркутск, Депутатская, 87/2");

		$sql = SQL::get("MySQL");
		$gis->setConnection($sql);
		$gis->write();
		$result = $sql->exec("SELECT * FROM `districts_locations`");
		$this->assertEquals(1, $result->getNumRows());
		while ($row = $result->getRow())
		    {
			$this->assertEquals("Октябрьский округ", $row["district"]);
			$this->assertEquals(104.342051, $row["lang"]);
			$this->assertEquals(52.265052, $row["lat"]);
		    } //end while

	    } //end testShouldWriteLocationDistrictWithCoordinatesToDatabase()


	/**
	 * Should define district by near object locations
	 *
	 * @return void
	 */

	public function testShouldDefineDistrictByNearObjectLocations()
	    {
		$sql = SQL::get("MySQL");
		$result = $sql->exec("SELECT * FROM `districts_locations`");
		$this->assertEquals(0, $result->getNumRows());

		$gis = new GisInfo("Иркутск", "Иркутск, Депутатская, 87/2");
		$gis->setConnection($sql);
		$gis->write();
		$result = $sql->exec("SELECT * FROM `districts_locations`");
		$this->assertEquals(1, $result->getNumRows());

		$gis = new GisInfo("Иркутск", "Иркутск, Депутатская, 87/5");
		$gis->setConnection($sql);
		$gis->write();

		$gis = new GisInfo("Иркутск", "Иркутск, Депутатская, 81");
		$gis->setConnection($sql);
		$gis->write();

		$gis = new GisInfo("Иркутск", "Иркутск, Ширямова, 32/2");
		$gis->setConnection($sql);
		$gis->write();

		$gis = new GisInfo("Иркутск", "Иркутск, Ширямова, 32/3");
		$gis->setConnection($sql);
		$gis->write();

		$result = $sql->exec("SELECT * FROM `districts_locations`");
		$this->assertEquals(5, $result->getNumRows());

		$gis = new GisInfo("Иркутск", "Иркутск, Депутатская, 89а");
		$this->assertEquals("Октябрьский округ", $gis->district);
		$this->assertEquals(["lang" => 104.341264, "lat" => 52.264578], $gis->location);
		$gis->setConnection($sql);
		$gis->defineDistrict();

		$gis = new GisInfo("Иркутск", "Иркутск, Ширямова, 32");
		$this->assertEquals("Октябрьский округ", $gis->district);
		$this->assertEquals(["lang" => 104.342378, "lat" => 52.263668], $gis->location);
		$gis->setConnection($sql);
		$gis->defineDistrict();
		$this->assertEquals("Октябрьский округ", $gis->district);
	    } //end testShouldDefineDistrictByNearObjectLocations()


    } //end class

?>

<?php

/**
 * PHP version 7.1
 *
 * @package Agentzilla\Gis
 */

namespace Tests;

use \Agentzilla\Gis\AddressSlicer;
use \PHPUnit\Framework\TestCase;

/**
 * Address slicer test
 *
 * @author  Andrey Mashukov <a.mashukoff@gmail.com>
 * @version SVN: $Date: 2018-02-03 20:22:06 +0000 (Sat, 03 Feb 2018) $ $Revision: 3 $
 * @link    $HeadURL: https://svn.agentzilla.ru/2gis/trunk/tests/AddressSlicerTest.php $
 */

class AddressSlicerTest extends TestCase
    {

	/**
	 * Should slice address to city, street and house
	 *
	 * @return void
	 */

	public function testShouldSliceAddressToCityStreetAndHouse()
	    {
		$addresses = [
		    "Иркутск, Советский пр. 124/2" => array(
						      "city"   => "Иркутск",
						      "street" => "Советский пр.",
						      "house"  => "124/2",
						     ),
		    "Москва, Каланчевская улица, 29" => array(
						      "city"   => "Москва",
						      "street" => "Каланчевская улица",
						      "house"  => "29",
						     ),
		    "г.Красноярск, ул. Академика Киренского, 2 \"и\"" => array(
						      "city"   => "Красноярск",
						      "street" => "Академика Киренского",
						      "house"  => "2и",
						     ),
		    "Иркутск, Звездный б-р., 124/2" => array(
						      "city"   => "Иркутск",
						      "street" => "Звездный б-р.",
						      "house"  => "124/2",
						     ),
		    "Иркутск, Звездная ул. 124/2" => array(
						      "city"   => "Иркутск",
						      "street" => "Звездная",
						      "house"  => "124/2",
						     ),
		    "Иркутск, 1-советская, 124/2" => array(
						      "city"   => "Иркутск",
						      "street" => "1-советская",
						      "house"  => "124/2",
						     ),
		    "Москва, зеленый проспект, 77к2" => array(
						      "city"   => "Москва",
						      "street" => "зеленый проспект",
						      "house"  => "77к2",
						     ),
		    "Москва, Благодатная д  8" => array(
						      "city"   => "Москва",
						      "street" => "Благодатная",
						      "house"  => "8",
						     ),
		    "Москва, Кутузовский просп., 8" => array(
						      "city"   => "Москва",
						      "street" => "Кутузовский просп.",
						      "house"  => "8",
						     ),
		    "Москва, Хорошёвское шоссе, 76к5" => array(
						      "city"   => "Москва",
						      "street" => "Хорошёвское шоссе",
						      "house"  => "76к5",
						     ),
		    "Москва, Корона, жилой комплекс, Вернадского проспект, 92" => array(
						      "city"   => "Москва",
						      "street" => "Вернадского проспект",
						      "house"  => "92",
						     ),
		    "Москва, ул. Хошимина, дом.16." => array(
						      "city"   => "Москва",
						      "street" => "Хошимина",
						      "house"  => "16",
						     ),
		    "Москва, Маросейка ул, 13ст3" => array(
						      "city"   => "Москва",
						      "street" => "Маросейка ул",
						      "house"  => "13с3",
						     ),
		    "Москва, Маросейка ул, 13 корпус 3" => array(
						      "city"   => "Москва",
						      "street" => "Маросейка ул",
						      "house"  => "13к3",
						     ),
		    "Москва, Маросейка ул, 13 строение 3" => array(
						      "city"   => "Москва",
						      "street" => "Маросейка ул",
						      "house"  => "13с3",
						     ),
		    "Москва, Николоямская, 39/43к1" => array(
						      "city"   => "Москва",
						      "street" => "Николоямская",
						      "house"  => "39/43к1",
						     ),
		    "Москва, Николоямская, 39/43 к1" => array(
						      "city"   => "Москва",
						      "street" => "Николоямская",
						      "house"  => "39/43к1",
						     ),
		    "Москва, Маросейка ул, 13стр3" => array(
						      "city"   => "Москва",
						      "street" => "Маросейка ул",
						      "house"  => "13с3",
						     ),
		    "Москва, Маросейка ул, 14 корпус 3" => array(
						      "city"   => "Москва",
						      "street" => "Маросейка ул",
						      "house"  => "14к3",
						     ),
		    "Москва, Маросейка ул, 15 корп. 3" => array(
						      "city"   => "Москва",
						      "street" => "Маросейка ул",
						      "house"  => "15к3",
						     ),
		    "Иркутск, 1-советская, 124а / 2а" => array(
						      "city"   => "Иркутск",
						      "street" => "1-советская",
						      "house"  => "124а/2а",
						     ),
		    "Иркутск, 1-советская, 124 а / 2" => array(
						      "city"   => "Иркутск",
						      "street" => "1-советская",
						      "house"  => "124а/2",
						     ),
		    "Иркутск, 1-я советская, 124а/2г" => array(
						      "city"   => "Иркутск",
						      "street" => "1-я советская",
						      "house"  => "124а/2г",
						     ),
		    "Иркутск, 1-советская, 1а/2" => array(
						      "city"   => "Иркутск",
						      "street" => "1-советская",
						      "house"  => "1а/2",
						     ),
		    "Ростов на дону, 12-советская, 12е" => array(
						      "city"   => "Ростов на дону",
						      "street" => "12-советская",
						      "house"  => "12е",
						     ),
		    "Ростов на дону, 12 советская, 12е (Эталон)" => array(
						      "city"   => "Ростов на дону",
						      "street" => "12 советская",
						      "house"  => "12е",
						     ),
		    "Нижневартовск, 12 советская 12е (Эталон)" => array(
						      "city"   => "Нижневартовск",
						      "street" => "12 советская",
						      "house"  => "12е",
						     ),
		    "Нижневартовск, 12 советская12е (Эталон)" => array(
						      "city"   => "Нижневартовск",
						      "street" => "12 советская",
						      "house"  => "12е",
						     ),
		    "Санкт-Петербург, 1-й дом ветеранов 12а" => array(
						      "city"   => "Санкт-Петербург",
						      "street" => "1-й дом ветеранов",
						      "house"  => "12а",
						     ),
		    "Санкт-Петербург, 1-й дом ветеранов 12а/34р" => array(
						      "city"   => "Санкт-Петербург",
						      "street" => "1-й дом ветеранов",
						      "house"  => "12а/34р",
						     ),
		    "Санкт-Петербург, Юнтоловский проспект пр-кт, 49Ак3" => array(
						      "city"   => "Санкт-Петербург",
						      "street" => "Юнтоловский проспект пр-кт",
						      "house"  => "49Ак3",
						     ),
		    "Санкт-Петербург, Юнтоловский проспект пр-кт, 49А к 3" => array(
						      "city"   => "Санкт-Петербург",
						      "street" => "Юнтоловский проспект пр-кт",
						      "house"  => "49Ак3",
						     ),
		    "Санкт-Петербург, ул 1-й дом ветеранов 12а/34р" => array(
						      "city"   => "Санкт-Петербург",
						      "street" => "1-й дом ветеранов",
						      "house"  => "12а/34р",
						     ),
		    "Красноярск, им газеты Красноярский Рабочий пр-кт, 175А" => array(
						      "city"   => "Красноярск",
						      "street" => "им газеты Красноярский Рабочий пр-кт",
						      "house"  => "175А",
						     ),
		    "Красноярск, им газеты Красноярский Рабочий пр-кт д.175А" => array(
						      "city"   => "Красноярск",
						      "street" => "им газеты Красноярский Рабочий пр-кт",
						      "house"  => "175А",
						     ),
		    "Красноярск, им газеты Красноярский Рабочий пр-кт д.175 А" => array(
						      "city"   => "Красноярск",
						      "street" => "им газеты Красноярский Рабочий пр-кт",
						      "house"  => "175А",
						     ),
		    "Красноярск, им газеты Красноярский Рабочий пр-кт д175 а/ 2А" => array(
						      "city"   => "Красноярск",
						      "street" => "им газеты Красноярский Рабочий пр-кт",
						      "house"  => "175а/2А",
						     ),
		    "Иркутск, А. Невского, 15" => array(
						      "city"   => "Иркутск",
						      "street" => "А. Невского",
						      "house"  => "15",
						     ),
		    "Иркутск, им. Невского, 15/2а" => array(
						      "city"   => "Иркутск",
						      "street" => "им. Невского",
						      "house"  => "15/2а",
						     ),
		    "Иркутск, им. Александра Невского, 15/2а" => array(
						      "city"   => "Иркутск",
						      "street" => "им. Александра Невского",
						      "house"  => "15/2а",
						     ),
		    "Иркутск, им. 100лет СССР, 15/2а" => array(
						      "city"   => "Иркутск",
						      "street" => "им. 100лет СССР",
						      "house"  => "15/2а",
						     ),
		    "Иркутск, им. 100 лет СССР, 15/2а" => array(
						      "city"   => "Иркутск",
						      "street" => "им. 100 лет СССР",
						      "house"  => "15/2а",
						     ),
		    "Иркутск, Юбилейный д.94, кв.49" => array(
						      "city"   => "Иркутск",
						      "street" => "Юбилейный",
						      "house"  => "94",
						     ),
		    "Сочи, ул им Героя Советского Союза Б.А.Микуцкого 49" => array(
						      "city"   => "Сочи",
						      "street" => "им Героя Советского Союза Б.А.Микуцкого",
						      "house"  => "49",
						     ),
		    "Сочи, ул им Героя Советского Союза Б А Микуцкого 49" => array(
						      "city"   => "Сочи",
						      "street" => "им Героя Советского Союза Б А Микуцкого",
						      "house"  => "49",
						     ),
		    "Сочи, ул им Героя Советского Союза Б А Микуцкого, остановка \"депо\" 49" => array(
						      "city"   => "Сочи",
						      "street" => "им Героя Советского Союза Б А Микуцкого",
						      "house"  => "49",
						     ),
		    "Сочи, ул им Героя Советского Союза Б А Микуцкого (жилой комплекс Олимп), 49" => array(
						      "city"   => "Сочи",
						      "street" => "им Героя Советского Союза Б А Микуцкого (жилой комплекс Олимп)",
						      "house"  => "49",
						     ),
		    "Сочи, ул им Героя Советского Союза Б А Микуцкого (жилой комплекс \"Олимп\"), 49" => array(
						      "city"   => "Сочи",
						      "street" => "им Героя Советского Союза Б А Микуцкого (жилой комплекс \"Олимп\")",
						      "house"  => "49",
						     ),
		    "Иркутск, (школа №23) А. Невского, 15" => array(
						      "city"   => "Иркутск",
						      "street" => "(школа №23) А. Невского",
						      "house"  => "15",
						     ),
		    "Иркутск, (школа #23) им. Невского, 15/2а" => array(
						      "city"   => "Иркутск",
						      "street" => "(школа #23) им. Невского",
						      "house"  => "15/2а",
						     ),
		    "Иркутск, А. (школа №23) Невского, 15" => array(
						      "city"   => "Иркутск",
						      "street" => "А. (школа №23) Невского",
						      "house"  => "15",
						     ),
		    "Сочи, ул им. Героя Советского Союза Б. А. Микуцкого 49" => array(
						      "city"   => "Сочи",
						      "street" => "им. Героя Советского Союза Б. А. Микуцкого",
						      "house"  => "49",
						     ),
		];

		foreach ($addresses as $address => $data)
		    {
			$slicer = new AddressSlicer($address);
			$this->assertEquals($data["city"], $slicer->city);
			$this->assertEquals($data["street"], $slicer->street);
			$this->assertEquals($data["house"], $slicer->house);
			$this->assertTrue($slicer->valid());
		    }

	    } //end testShouldSliceAddressToCityStreetAndHouse()


	/**
	 * Should slice to numberhouse, building and check multihouse
	 *
	 * @return void
	 */

	public function testShouldSliceToNumberhouseBuildingAndCheckMultihouse()
	    {
		$addresses = [
		    "Иркутск, Советский пр., 124/2" => array(
						      "city"        => "Иркутск",
						      "street"      => "Советский пр.",
						      "house"       => "124/2",
						      "numberhouse" => "124",
						      "building"    => "2",
						      "multihouse"  => false,
						     ),
		    "Иркутск, Советский пр., 124" => array(
						      "city"        => "Иркутск",
						      "street"      => "Советский пр.",
						      "house"       => "124",
						      "numberhouse" => "124",
						      "building"    => null,
						      "multihouse"  => false,
						     ),
		    "Иркутск, Советский пр., 124корпус2" => array(
						      "city"        => "Иркутск",
						      "street"      => "Советский пр.",
						      "house"       => "124к2",
						      "numberhouse" => "124",
						      "building"    => "2",
						      "multihouse"  => false,
						     ),
		    "Иркутск, Советский пр., 124 корпус 2" => array(
						      "city"        => "Иркутск",
						      "street"      => "Советский пр.",
						      "house"       => "124к2",
						      "numberhouse" => "124",
						      "building"    => "2",
						      "multihouse"  => false,
						     ),
		    "Иркутск, Советский пр., 124к2" => array(
						      "city"        => "Иркутск",
						      "street"      => "Советский пр.",
						      "house"       => "124к2",
						      "numberhouse" => "124",
						      "building"    => "2",
						      "multihouse"  => false,
						     ),
		    "Иркутск, Советский пр., 124с2" => array(
						      "city"        => "Иркутск",
						      "street"      => "Советский пр.",
						      "house"       => "124с2",
						      "numberhouse" => "124",
						      "building"    => "2",
						      "multihouse"  => false,
						     ),
		    "Иркутск, Советский пр., 124строение2" => array(
						      "city"        => "Иркутск",
						      "street"      => "Советский пр.",
						      "house"       => "124с2",
						      "numberhouse" => "124",
						      "building"    => "2",
						      "multihouse"  => false,
						     ),
		    "Иркутск, Советский пр., 124 строение 2" => array(
						      "city"        => "Иркутск",
						      "street"      => "Советский пр.",
						      "house"       => "124с2",
						      "numberhouse" => "124",
						      "building"    => "2",
						      "multihouse"  => false,
						     ),
		    "Иркутск, Советский пр., 124 стр. 2" => array(
						      "city"        => "Иркутск",
						      "street"      => "Советский пр.",
						      "house"       => "124с2",
						      "numberhouse" => "124",
						      "building"    => "2",
						      "multihouse"  => false,
						     ),
		    "Иркутск, Советский пр., 124-129" => array(
						      "city"        => "Иркутск",
						      "street"      => "Советский пр.",
						      "house"       => "124-129",
						      "numberhouse" => "124-129",
						      "building"    => null,
						      "multihouse"  => true,
						     ),
		];
		foreach ($addresses as $address => $data)
		    {
			$slicer = new AddressSlicer($address);
			$this->assertEquals($slicer->city, $data["city"]);
			$this->assertEquals($slicer->street, $data["street"]);
			$this->assertEquals($slicer->house, $data["house"]);
			$this->assertEquals($slicer->numberhouse, $data["numberhouse"]);
			$this->assertEquals($slicer->building, $data["building"]);
			$this->assertEquals($slicer->multihouse, $data["multihouse"]);
		    }

	    } //end testShouldSliceToNumberhouseBuildingAndCheckMultihouse()


    } //end class

?>

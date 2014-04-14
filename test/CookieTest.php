<?php

class CookieTest extends PHPUnit_Framework_TestCase
{
	public function testSetRandomCookieFile()
	{
		$http = new HttpClient();
		$http->setRandomCookieFile();
		$this->assertNotEmpty($http->getCookieFile());
	}
	
	public function testUseRandomCookieFile()
	{
		$http = HttpClient::from(array('useRandomCookieFile' => true));
		$this->assertNotEmpty($http->getCookieFile());
	}
}

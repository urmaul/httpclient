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
	
	public function testCookies()
	{
		$http = HttpClient::from(array('useRandomCookieFile' => true));
		
		$actual = $http->get('http://httpbin.org/cookies/set?k1=v1&k2=v2');
		
		$expected = json_decode('{"cookies": {"k1": "v1", "k2": "v2"}}', true);
		
		$this->assertEquals($expected, json_decode($actual, true));
	}
}
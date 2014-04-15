<?php

class InfoTest extends PHPUnit_Framework_TestCase
{
	public function statuses()
	{
		return array(
			array('http://httpbin.org/status/200', 200),
			array('http://httpbin.org/status/400', 400),
			array('http://httpbin.org/status/404', 404),
			array('http://httpbin.org/status/418', 418),
			array('http://httpbin.org/status/500', 500),
		);
	}
	
	/**
	 * @dataProvider statuses
	 */
	public function testStatusCode($url, $expected)
	{
		$http = new HttpClient();
		$http->get($url);
		$this->assertEquals($expected, $http->getHttpCode());
	}
	
	/**
	 * @dataProvider statuses
	 */
	public function testStatusCodeProperty($url, $expected)
	{
		$http = new HttpClient();
		$http->get($url);
		$this->assertEquals($expected, $http->httpCode);
	}
}

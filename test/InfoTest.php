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
		$this->assertEquals($expected, $http->httpCode);
	}

	public function effectiveUrls()
	{
		return array(
			array('http://httpbin.org/status/418', 'http://httpbin.org/status/418'),
			array('http://httpbin.org/redirect/1', 'http://httpbin.org/get'),
			array('http://httpbin.org/redirect/1', 'http://httpbin.org/redirect/1', array('curl' => array(CURLOPT_FOLLOWLOCATION => 0))),
		);
	}
	
	/**
	 * @dataProvider effectiveUrls
	 */
	public function testEffectiveUrl($url, $expected, $params = array())
	{
		$http = new HttpClient();
		$http->get($url, $params);
		$this->assertEquals($expected, $http->getEffectiveUrl());
		$this->assertEquals($expected, $http->effectiveUrl);
	}
}

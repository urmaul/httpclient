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
    
    public function testTempFileIsRemoved()
    {
        $http = HttpClient::from(array('useRandomCookieFile' => true));
        $file = $http->cookieFile . '';
        
        $actual = $http->get('http://httpbin.org/cookies/set?k1=v1&k2=v2');
        
        $this->assertTrue(file_exists($file));
        unset($http);
        clearstatcache(true, $file);
        $this->assertFalse(file_exists($file), $file . ' must be already deleted');
    }
    
    public function testSetCookiesString()
    {
        $cookies = array('k1' => 'v1', 'k2' => 'v2');
        
        $http = new HttpClient();

        $body = $http->get('http://httpbin.org/cookies', array('cookies' => 'k1=v1; k2=v2'));
        
        $expected = array('cookies' => $cookies);
        $this->assertEquals($expected, json_decode($body, true));
    }
    
    public function testSetCookies()
    {
        $cookies = array('k1' => 'v1', 'k2' => 'v2');
        
        $http = new HttpClient();

        $body = $http->get('http://httpbin.org/cookies', array('cookies' => $cookies));
        
        $expected = array('cookies' => $cookies);
        $this->assertEquals($expected, json_decode($body, true));
    }
    
    public function testGetCookies()
    {
        $http = HttpClient::from(array('useRandomCookieFile' => true));

        $body = $http->get('http://httpbin.org/cookies/set?k1=v1&k2=v2', array('header' => true));
        
        $expected = array('k1' => 'v1', 'k2' => 'v2');
        $this->assertEquals($expected, $http->cookies);
    }
}

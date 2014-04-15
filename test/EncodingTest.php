<?php

class EncodingTest extends PHPUnit_Framework_TestCase
{
    public function testGzip()
    {
        $http = HttpClient::from();
        $json = $http->get('http://httpbin.org/gzip');
        $response = json_decode($json);
        
        $this->assertNotNull($response);
        $this->assertTrue($response->gzipped);
    }
}

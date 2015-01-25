<?php

class ParamsTest extends PHPUnit_Framework_TestCase
{
    public function testHeader()
    {
        $http = new HttpClient();
        $page = $http->get('http://httpbin.org/redirect/1', array(
            'header' => true,
            'curl' => array(CURLOPT_FOLLOWLOCATION => 0),
        ));

        $this->assertNotSame(false, strpos($page, 'Location: /get'));
    }
}
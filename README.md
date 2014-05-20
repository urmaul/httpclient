# Php Http Client

It is a PHP wrapper for curl.

[![Build Status](https://travis-ci.org/urmaul/httpclient.svg)](https://travis-ci.org/urmaul/httpclient)

## How to use

### Methods

* **head($url, $params = array())** - performs http request to get responce headers.
* **get($url, $params = array())** - performs http GET request.
* **post($url, $post = array(), $params = array())** - performs http POST request.
* **download($url, $dest, $post = array(), $params = array())** - downloads file.
* **request($params)** - performs http request.
* **getInfo($opt = null)** - Returns information about the last transfer. [Details](http://www.php.net/manual/ru/function.curl-getinfo.php)

### Possible $params values

* **url** - *(string)* request url.
* **post** - *(string|array)* request POST data.
* **ref** - *(string)* "Referer" request header content.
* **header** - *(boolean, default = false)* whether to include response header into response.
* **nobody** - *(boolean, default = false)* whether to NOT include response body into response.
* **timeout** - *(integer, default = 15)* request timeout.
* **tofile** - *(string, default = null)* output file name. If defined - response will be saved to that file.
* **attempts_max** - *(integer, default = 1)* maximum number of attempts to perform HTTP request.
* **attempts_delay** - *(integer, default = 10)* delay between attempts, in seconds

### Readonly properties

* **$lastError** - (string) last request error.
* **$info** - (array) information about the last transfer.
* **$httpCode** - (integer) last received HTTP code.
* **$effectiveUrl** - (string) last effective url.

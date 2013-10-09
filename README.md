# Yii Http Client

Http client for yii

## How to attach

Add this to your "components" config:

```php
'httpClient' => array(
    'class' => 'ext.httpclient.HttpClient',
    'useRandomCookieFile' => true,
),
```

## How to use

### Methods

* **head($url, $params = array())** - performs http request to get responce headers.
* **get($url, $params = array())** - performs http GET request.
* **post($url, $post = array(), $params = array())** - performs http POST request.
* **request($params)** - performs http request.

## TODO

* Rewrite it all
* Rename $params

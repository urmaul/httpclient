<?php

/**
 * @property string $cookieFile path to file that stores cookies 
 * 
 * @property-read string $lastError last request error
 */
class HttpClient extends CApplicationComponent
{
    /**
     * @var string last request error
     */
    private $_lastError;

    protected $use_proxy = false;
    protected $proxies;
    protected $i_proxy = -1;
    
    public $useragent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; en)';
    
    public $useRandomCookieFile = false;
    public $randomCookieFilePrefix = 'yiihc';
    
    protected $_cookieFile = null;
    
    public $lastpageFile = null;

    protected $defaults = array(
        'url'  => '',
        'post' => null,
        'ref'  => '',
        
        'header' => false,
        'timeout' => 15,
        
        'tofile' => null,
        
        'attempts_max' => 1,
        'attempts_delay' => 10,
    );

    function init()
    {
        parent::init();
        
        if ( $this->useRandomCookieFile )
            $this->setRandomCookieFile();
    }
    
    public function get($url, $params = array())
    {
        $params['url'] = $url;
        return $this->request($params);
    }
    
    public function request($params)
    {
        $params = array_merge($this->defaults, $params);
        
        $ch = $this->createCurl($params);
        
        if( isset($params['tofile']) ) {
            $tofile = fopen($params['tofile'], 'wb');
            
            if ( !$tofile )
                throw new CException(__CLASS__ . " couldn't open file '{$params['tofile']}' for edit.");
            
            curl_setopt($ch, CURLOPT_FILE, $tofile);
        }

        if( $this->use_proxy )
            curl_setopt($ch, CURLOPT_PROXY, $this->proxy());

        
        // Debug code
        /*echo
            '<b>' . $params['url'] . '</b>' .
            '<pre>' . var_export($params['post'], true) . '</pre>';*/

        Yii::trace('Calling ' . $params['url'], __CLASS__);
        
        do {
            // Do http request
            $res = curl_exec($ch);
            
        } while (
            $res === FALSE && // 
            --$params['attempts_max'] != 0 &&
            sleep($params['attempts_delay']) !== FALSE
        );
        
        if ( is_string($res) )
            YII_DEBUG && Yii::trace('Got ' . strlen($res) . ' bytes', __CLASS__);
        else
            YII_DEBUG && Yii::trace('Got ' . var_export($res, true), __CLASS__);
        
        if ( isset($params['tofile']) ) {
            fclose($tofile);
        }
        
        // Saving last error
        $this->_lastError = curl_error($ch);
        
        curl_close($ch);
        
        // Saving response content into lastpageFile
        if ( $this->lastpageFile != null )
            file_put_contents($this->lastpageFile, $res);
        
        return $res;
    }
    
    /**
     * Creates multiple request
     * @param array $requests requests parameters [key] => [params array]
     * @param array $defaults default request paremeters
     * @return array http request results array [key] => [result string]
     * Requests array keys are used to differ results
     */
    public function multiRequest($requests, $defaults = array())
    {
        if ( empty($requests) )
            return array();
        
        $defaults = array_merge($this->defaults, $defaults);
        
        $mh = curl_multi_init();
        
        $handles = array();
        
        foreach ($requests as $key => $request) {
            $params = array_merge($defaults, $request);
            
            $ch = $this->createCurl($params);
            
            curl_multi_add_handle($mh, $ch);
            
            $handles[$key] = $ch;
        }

        $active = null;
        
        do {
            $mrc = curl_multi_exec($mh, $active);
        } while ($mrc == CURLM_CALL_MULTI_PERFORM);

        while ($active && $mrc == CURLM_OK) {
            if (curl_multi_select($mh) != -1) {
                do {
                    $mrc = curl_multi_exec($mh, $active);
                } while ($mrc == CURLM_CALL_MULTI_PERFORM);
            }
        }
        
        $results = array();
        foreach ($handles as $key => $ch) {
            $results[$key] = curl_multi_getcontent($ch);
            curl_multi_remove_handle($mh, $ch);
        }

        curl_multi_close($mh);
        
        return $results;
    }
    
    protected function createCurl($params)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,     $params['url']);
        curl_setopt($ch, CURLOPT_HEADER,  $params['header']);
        curl_setopt($ch, CURLOPT_TIMEOUT, $params['timeout']);
        curl_setopt($ch, CURLOPT_REFERER, $params['ref']);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->useragent);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_NOBODY, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        
        if( $params['post'] !== null ) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params['post']);
        }
        
        if( $this->cookieFile !== null ) {
            curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookieFile);
            curl_setopt($ch, CURLOPT_COOKIEJAR,  $this->cookieFile);
        }
        
        return $ch;
    }
    

    /**
     * @deprecated
     * @return type 
     */
    protected function proxy() {
        return $this->proxies[ ++$this->i_proxy % count($this->proxies) ];
    }
    
    # Getters #
    
    public function getCookieFile()
    {
        return $this->_cookieFile;
    }

    /**
     * Returns last request error
     * @return string 
     */
    public function getLastError() {
        return $this->_lastError;
    }
    
    # Setters #

    public function setCookieFile($fname, $clear = true)
    {
        $this->_cookieFile = $fname;
        
        if ( $clear )
            $this->clearCookieFile();
    }
    
    public function setRandomCookieFile()
    {
        $fileName = tempnam(sys_get_temp_dir(), $this->randomCookieFilePrefix);
        $this->setCookieFile($fileName, true);
    }

    /**
     * @deprecated
     * @param type $proxy 
     */
    public function set_proxy( $proxy )
    {
        $this->use_proxy = 1;
        $this->proxies = explode("\n", $proxy);
    }

    # Actions #
    
    /**
     * Creates and clears cookie file 
     */
    public function clearCookieFile ()
    {
        if ($this->cookieFile !== null)
            file_put_contents($this->cookieFile, '');
    }

}

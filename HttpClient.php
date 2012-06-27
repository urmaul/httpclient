<?php

/**
 * @property string $cookieFile path to file that stores cookies 
 */
class HttpClient extends CApplicationComponent
{
	
	private $error;

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
		
		'attempts_max' => 1,
		'attempts_delay' => 10,
	);

	function init()
	{
		parent::init();
        
        if ( $this->useRandomCookieFile )
            $this->setRandomCookieFile();
    }
    
    public function get($url)
    {
		return $this->request(array('url' => $url));
	}
    
    public function request($params)
    {
		$params = array_merge($this->defaults, $params);
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,     $params['url']);
		curl_setopt($ch, CURLOPT_HEADER,  $params['header']);
		curl_setopt($ch, CURLOPT_TIMEOUT, $params['timeout']);
		curl_setopt($ch, CURLOPT_REFERER, $params['ref']);
		curl_setopt($ch, CURLOPT_USERAGENT, $this->useragent);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_NOBODY, 0);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		
		if( isset($params['tofile']) ) {
			$tofile = fopen($params['tofile'], 'wb');
            
			if ( !$tofile )
				throw new CException(__CLASS__ . " couldn't open file '{$params['tofile']}' for edit.");
            
			curl_setopt($ch, CURLOPT_FILE, $tofile);
		}

		if( $this->use_proxy )
			curl_setopt($ch, CURLOPT_PROXY, $this->proxy());

		if( $params['post'] !== null ) {
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $params['post']);
		}
		
		if( $this->cookieFile !== null ) {
			curl_setopt ($ch, CURLOPT_COOKIEFILE, $this->cookieFile);
			curl_setopt ($ch, CURLOPT_COOKIEJAR,  $this->cookieFile);
		}
		
		// Debug code
		/*echo
			'<b>' . $params['url'] . '</b>' .
			'<pre>' . var_export($params['post'], true) . '</pre>';*/

		do {
			$res = curl_exec($ch);
		} while (
			$res === FALSE && 
			--$params['attempts_max'] != 0 &&
            sleep($params['attempts_delay']) !== FALSE
		);
		
		if(isset($params['tofile'])) {
			fclose($tofile);
		}
		$this->error = curl_error($ch);
		curl_close($ch);
		
		if($this->lastpageFile != null)
			file_put_contents($this->lastpageFile, $res);
        
		return $res;
	}

    /**
     * @deprecated
     * @return type 
     */
	protected function proxy() {
		return $this->proxies[ ++$this->i_proxy % count($this->proxies) ];
	}
	
    /* Getters */
    
    public function getCookieFile()
    {
        return $this->_cookieFile;
    }

    /**
     * @deprecated
     * @return type 
     */
    public function get_last_error() {
		return $this->error;
	}
	
	/* Setters */

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

    /* Actions */
    
	/**
     * Creates and clears cookie file 
     */
    public function clearCookieFile ()
    {
        if ($this->cookieFile !== null)
            file_put_contents($this->cookieFile, '');
	}

}

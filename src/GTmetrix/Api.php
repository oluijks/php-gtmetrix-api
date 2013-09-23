<?php namespace GTmetrix;

/**
 * Api
 *
 * @author      Olaf Luijks <oluijks@gmail.com>
 * @version     0.0.1
 */

use GTmetrix\Exception\InvalidArgumentException;

class Api {

    /**
     * Base URL for the GTmetrix API
     *
     * @link    http://gtmetrix.com/api/
     */
    const API_URL = 'https://gtmetrix.com/api/0.1';

    /**
     * @var string
     */
    protected $_username = '';

    /**
     * @var string
     */
    protected $_apiKey = '';

    /**
     * @var string
     *
     * @link    http://gtmetrix.com/api/
     */
    protected $_userAgent = 'Luijks_GTmetrix_API_php/0.0.1 (+http://gtmetrix.com/api/)';

    /**
     * @var string
     */
    protected $_testId = '';

    /**
     * @var int
     */
    protected $_testLocationId = 0;

    /**
     * @var bool
     */
    protected $_useAdblockPlugin = 0;

    /**
     * @var array
     */
    protected $_result = [];

    /**
     * @var string
     */
    protected $_error = '';

    /**
     *
     * @param   string $username
     * @param   string $apiKey
     * @throws  InvalidArgumentException
     */
    public function __construct($username = '', $apiKey = '')
    {
        $this->_checkRequirements();

        if ('' !== $username && '' !== $apiKey)
        {
            $this->_validateCredentials($username, $apiKey);
        }

        $this->_username = $username;
        $this->_apiKey   = $apiKey;
    }

    /**
     * Set username
     *
     * @access  public
     * @param   $username
     * @throws  InvalidArgumentException
     * @todo    validate
     */
    public function setUsername($username)
    {
        if ( ! filter_var($username, FILTER_VALIDATE_EMAIL))
        {
            throw new InvalidArgumentException('Invalid email address');
        }
        $this->_username = $username;
    }

    /**
     * Set api key
     *
     * @access  public
     * @param   $apiKey
     * @throws  InvalidArgumentException
     * @todo    validate
     */
    public function setApiKey($apiKey)
    {
        if (empty($apiKey))
        {
            throw new InvalidArgumentException('Invalid api key');
        }
        $this->_apiKey = $apiKey;
    }

    /**
     * Set a user agent
     *
     * @access  public
     * @param   $userAgent
     */
    public function setUserAgent($userAgent)
    {
        $this->_userAgent = $userAgent;
    }

    /**
     * Query the api
     *
     * @access  protected
     * @param   $command
     * @param   string  $request
     * @param   string  $params
     * @return  bool|mixed
     */
    protected function _callApi($command, $request = 'GET', $params = '')
    {
        $ch = curl_init();

        if (substr($command, 0, strlen(self::API_URL) - 1) == self::API_URL)
        {
            $Url = $command;
        }
        else
        {
            $Url = self::API_URL . '/' . $command;
        }

        curl_setopt($ch, CURLOPT_URL, $Url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->_userAgent);
        curl_setopt($ch, CURLOPT_USERPWD, $this->_username . ":" . $this->_apiKey);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $request);
        // CURLOPT_SSL_VERIFYPEER turned off to avoid failure when cURL has no CA cert bundle: see http://curl.haxx.se/docs/sslcerts.html
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        if (strtoupper('POST') === $request)
        {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        }

        $results = curl_exec($ch);
        if (false === $results)
        {
            $this->_error = curl_error($ch);
        }

        curl_close($ch);

        return $results;
    }

    /**
     * Check if we have a test id
     *
     * @access protected
     * @return bool
     */
    protected function _checkId()
    {
        if (empty($this->_testId))
        {
            $this->_error = 'Please start a new test or load an existing test first' . PHP_EOL;
            return false;
        }
        return true;
    }

    /**
     * Get error
     *
     * @access public
     * @return string
     */
    public function getError()
    {
        return $this->_error;
    }

    /**
     * Test a website
     *
     * @access  public
     * @param   $params
     * @return  string|bool
     */
    public function testSite($params)
    {
        if (empty($params))
        {
            $this->_error = 'At least a valid url to test must be provided' . PHP_EOL;
            return false;
        }

        if ( ! isset($params['url']) || empty($params['url']))
        {
            $this->_error = 'A valid url to test must be provided' . PHP_EOL;
            return false;
        }

        // Check Url
        if ( ! filter_var($params['url'], FILTER_VALIDATE_URL))
        {
            $this->_error = $params['url'] . ' is not a valid url' . PHP_EOL;
            return false;
        }

        // Set test location
        if ($this->_testLocationId)
        {
            $params['location'] = $this->_testLocationId;
        }

        // Adblock plugin
        if ($this->_useAdblockPlugin)
        {
            $params['x-metrix-adblock'] = $this->_useAdblockPlugin;
        }

        if ( ! empty($this->_result))
        {
            $this->_result = [];
        }

        $params = http_build_query($params);

        $result = $this->_callApi('test', 'POST', $params);

        if (false != $result)
        {
            $result = json_decode($result, true);
            if (empty($result['error']))
            {
                $this->_testId = $result['test_id'];

                if (isset($result['state']) && ! empty($result['state']))
                {
                    $this->_result = $result;
                }
                return $this->_testId;
            }
            else
            {
                $this->_error = $result['error'];
            }
        }
        return false;
    }

    /**
     * Query an existing test from GTMetrix API
     *
     * @access  public
     * @param   $testId
     * @return  void
     */
    public function loadTest($testId)
    {
        $this->_testId = $testId;

        if ( ! empty($this->_result))
        {
            $this->_result = [];
        }
    }

    /**
     * Delete the test from the GTMetrix
     *
     * @access public
     * @return bool|string
     */
    public function deleteTest()
    {
        if ( ! $this->_checkId())
        {
            return false;
        }

        $command = "test/" . $this->_testId;

        $result = $this->_callApi($command, "DELETE");
        if (false != $result)
        {
            $result = json_decode($result, true);
            return ($result['message']) ? true : false;
        }
        return false;
    }

    /**
     * Get test id
     *
     * @access public
     * @return bool|string
     */
    public function getTestId()
    {
        return ($this->_testId) ? $this->_testId : false;
    }

    /**
     * Poll for the test state
     *
     * @access protected
     * @return bool
     */
    protected function _pollTestState()
    {
        if ( ! $this->_checkId())
        {
            return false;
        }

        if ( ! empty($this->_result))
        {
            if ("completed" === $this->_result['state'])
            {
                return true;
            }
        }

        $command = "test/" . $this->_testId;

        $result = $this->_callApi($command);
        if (false != $result)
        {
            $result = json_decode($result, true);

            if ( ! empty($result['error']) && ! isset($result['state']))
            {
                $this->_error = $result['error'];
                return false;
            }

            $this->_result = $result;
            if ('error' == $result['state'])
            {
                $this->_error = $result['error'];
            }
            return true;
        }
        return false;
    }

    /**
     * Returns the state of the test [queued, started, completed, error]
     *
     * @access protected
     * @return bool|string
     */
    protected function _getTestState()
    {
        if ( ! $this->_checkId() || empty($this->_result))
        {
            return false;
        }
        return $this->_result['state'];
    }

    /**
     * Determine if test is completed
     *
     * @access protected
     * @return bool
     */
    protected function _isTestCompleted()
    {
        return ('completed' === $this->_getTestState()) ? true : false;
    }

    /**
     * locks and polls API until test results are received
     * waits for 6 seconds before first check, then polls every 2 seconds
     * at the 30 second mark it reduces frequency to 5 seconds
     *
     * @access  public
     * @return  void
     */
    public function waitForResults()
    {
        sleep(6);
        $i = 1;
        while ($this->_pollTestState())
        {
            if ('completed' == $this->_getTestState() || 'error' == $this->_getTestState())
            {
                break;
            }
            sleep($i++ <= 13 ? 2 : 5);
        }
    }

    /**
     * Set use AdBlock plugin
     *
     * @access  public
     * @param   bool $use
     */
    public function setAdblockPlugin($use = false)
    {
        $this->_useAdblockPlugin = $use;
    }

    /**
     * Get Test locations
     *
     * @access public
     * @return bool|mixed
     */
    public function getTestLocations()
    {
        $result = $this->_callApi('locations');
        if (false != $result)
        {
            $result = json_decode($result, true);
            if (empty($result['error']))
            {
                return $result;
            }
            $this->_error = $result['error'];
        }
        return false;
    }

    /**
     * @param $id
     */
    public function setTestLocation($id)
    {
        $this->_testLocationId = (int)$id;
    }

    /**
     * Get final test results
     *
     * @access public
     * @return bool|mixed
     */
    public function getTestResults()
    {
        if ( ! $this->_isTestCompleted())
        {
            return false;
        }
        return $this->_result['results'];
    }

    /**
     *
     * @access  public
     * @param   string $item
     * @return  bool
     * @todo    implement $item
     */
    public function getResourceUrls($item = 'all')
    {
        if ( ! $this->_isTestCompleted())
        {
            return false;
        }
        return $this->_result['resources'];
    }

    /**
     *
     * @access  public
     * @param   null    $items
     * @param   string  $location
     * @param   bool    $appendTestId
     * @return  bool
     * @todo    video
     */
    public function downloadResources($items = null, $location = './', $appendTestId = false)
    {

        if ( ! $this->_isTestCompleted())
        {
            return false;
        }

        $resources = $this->_result['resources'];

        $resourceTypes = [
            'report_pdf'        => 'pdf',
            'pagespeed'         => 'txt',
            'har'               => 'txt',
            'pagespeed_files'   => 'tar',
            'yslow'             => 'txt',
            'screenshot'        => 'jpg',
        ];

        if ( ! $items || '' == $items)
        {
            $items = array_keys($resourceTypes);
        }

        if ( ! is_array($items))
        {
            $items = [$items];
        }

        if ( ! is_writable($location))
        {
            $this->_error = 'Permission denied in ' . $location;
            return false;
        }

        foreach ($items as $item)
        {
            if ( ! array_key_exists($item, $resources))
            {
                $this->_error = $item . ' does not exist';
                return false;
            }

            $file = fopen($location . $item . ($appendTestId ? '-' . $this->_testId : '') . '.' . $resourceTypes[$item], "w");

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $resources[$item]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FILE, $file);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_USERAGENT, $this->_userAgent);
            curl_setopt($ch, CURLOPT_USERPWD, $this->_username . ":" . $this->_apiKey);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $results = curl_exec($ch);
            if (false === $results)
            {
                $this->_error = curl_error($ch);
            }
            curl_close($ch);
        }
        return true;
    }

    /**
     * Get account status, returns credits, and timestamp of next top up
     *
     * @access public
     * @return bool|mixed
     */
    public function getAccountStatus()
    {
        $result = $this->_callApi('status');
        if (false != $result)
        {
            $result = json_decode($result, true);
            if (empty($result['error']))
            {
                return $result;
            }
            $this->_error = $result['error'];
        }
        return false;
    }

    /**
     * Check requirements
     *
     * @return void
     */
    protected function _checkRequirements()
    {
        // PHP and cURL requirements
        if (version_compare(phpversion(), '5.4.0', '<'))
        {
            die('Please upgrade PHP to 5.4.x');
        }

        if ( ! in_array('curl', get_loaded_extensions()))
        {
            die('Please load the PHP curl extension');
        }
    }

    /**
     * Validate credentials
     *
     * @param $username
     * @param $apiKey
     * @throws Exception\InvalidArgumentException
     */
    protected function _validateCredentials($username, $apiKey)
    {
        if ( ! filter_var($username, FILTER_VALIDATE_EMAIL))
        {
            throw new InvalidArgumentException('Invalid email address');
        }

        if (empty($apiKey))
        {
            throw new InvalidArgumentException('Invalid api key');
        }
    }

}
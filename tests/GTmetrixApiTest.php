<?php namespace GTmetrix;

/**
 *
 *
 * @version 0.0.1 
 * @author  Olaf Luijks <oluijks@gmail.com>
 */

use GTmetrix\Api;

require_once __DIR__ . '/../bootstrap/autoload.php';

class GTmetrixApiTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var
     */
    protected $_api;

    /**
     *
     */
    public function setUp()
    {
        $this->_api = new Api();
    }

    /**
     *
     * @expectedException InvalidArgumentException
     */
    public function testEmptyUsernameThrowsInvalidArgumentException()
    {
        $this->_api->setUsername('');
    }

    /**
     *
     * @expectedException InvalidArgumentException
     */
    public function testInvalidUsernameThrowsInvalidArgumentException()
    {
        $this->_api->setUsername('invalid-email-address');
    }

    /**
     *
     * @expectedException InvalidArgumentException
     */
    public function testEmptyApiKeyThrowsInvalidArgumentException()
    {
        $this->_api->setApiKey('');
    }

    /**
     *
     */
    public function tearDown()
    {
        $this->_api = null;
    }
}

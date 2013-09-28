<?php namespace GTmetrix;

/**
 * php-gtmetrix-api - TestCase.php
 *
 * @version 0.0.1 
 * @author  Olaf Luijks <oluijks@gmail.com>
 */

use GTmetrix\Api;

require_once __DIR__ . '/../bootstrap/autoload.php';


class TestCase extends \PHPUnit_Framework_TestCase {

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
     */
    public function tearDown()
    {
        $this->_api = null;
    }

}
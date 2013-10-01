<?php namespace GTmetrix;

/**
 *
 *
 * @version 0.0.1 
 * @author  Olaf Luijks <oluijks@gmail.com>
 */

use GTmetrix\Api;

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
<?php namespace GTmetrix;

/**
 *
 *
 * @version 0.0.1 
 * @author  Olaf Luijks <oluijks@gmail.com>
 */

class GTmetrixApiTest extends TestCase {

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
     */
    public function testValidUsernameDoesNotThrowsInvalidArgumentException()
    {
        $this->_api->setUsername('email@example.com');
    }

    /**
     *
     * @expectedException InvalidArgumentException
     */
    public function testEmptyApiKeyThrowsInvalidArgumentException()
    {
        $this->_api->setApiKey('');
    }

}

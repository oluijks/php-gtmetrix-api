php-gtmetrix-api
================

A PHP library to interact with the GTmetrix REST API (based on GTmetrix own examples)

Version: very-alpha.0.0.1

*Requirements:*

+ PHP 5.4.x
+ cURL PHP extension

How to install
--------------

Begin by installing this package through composer. Edit your project's `composer.json` file to require `oluijks/gtmetrix`.

	"require": {
		"oluijks/gtmetrix": "dev-master"
	}

Next, update composer:

    composer update

Usage
-----

Create a new object

    $test = new GTmetrix\Api();

Or create a object via the constructor

    $test = new Api("email@example.com", "your-api-key");

Set username, api-key and some options

    $test->setUsername("email@example.com")
         ->setApiKey("your-api-key")
         ->setAdblockPlugin(true);

Call testSite and provide at least a url

    $testId = $test->testSite([
        'url' => 'http://example.com'
        // other options
        // , 'x-metrix-adblock' => 1
        // , 'x-metrix-video' => 1
    ]);

Wait for the test results

    $test->waitForResults();

Fetch the test results

    $results = $test->getTestResults();

Fetch the rerourse url's

    $resourceUrls = $test->getResourceUrls();

Download resources (files: har.txt, pagespeed.txt, pagespeed_files.tar, report_pdf.pdf, screenshot.jpg, yslow.txt)
You can specify specific items to download or append your test id to the file names.

    $test->downloadResources(null, './downloads/', false);


Options
-------

Public functions
----------------

    setUsername($username)
    setApiKey($apiKey)
    setUserAgent($userAgent)
    getError()
    loadTest($testId)
    getTestId()
    waitForResults()
    setAdblockPlugin($use = false)
    getTestLocations()
    setTestLocation($id)
    getTestResults()
    getResourceUrls($item = 'all')
    downloadResources($items = null, $location = './', $appendTestId = false)
    getAccountStatus()

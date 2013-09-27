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

    // 1. Create a new object
    $test = new GTmetrix\Api();

    // 1.1. Or create a object via the constructor
    $test = new Api("email@example.com", "your-api-key");

    // 2. Set username, api-key and some options
    $test->setUsername("email@example.com")
         ->setApiKey("your-api-key")
         ->setAdblockPlugin(true);

    // 3.
    $testId = $test->testSite([
        'url' => 'http://example.com'
        // more options (see Options)
    ]);

    // 4. Wait for the test results
    $test->waitForResults();

    // 5.
    $results = $test->getTestResults();

    // 6.
    $resourceUrls = $test->getResourceUrls();

    // 7. Download resources (files: har.txt, pagespeed.txt, pagespeed_files.tar, report_pdf.pdf, screenshot.jpg, yslow.txt)
    //    You can specify specific items to download or append your test Id.
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

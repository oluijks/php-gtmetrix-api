<?php

error_reporting(E_ALL);
ini_set('display_errors', 'On');
// xdebug_disable();

require_once __DIR__ . '/bootstrap/autoload.php';

// use GTmetrix\Api;

// $test = new Api("oluijks@gmail.com", "db5995f58e75bba0bc166db6cb534c14");

$test = new GTmetrix\Api();
$test->setUsername("oluijks@gmail.com");
$test->setApiKey("db5995f58e75bba0bc166db6cb534c14");
$test->setAdblockPlugin(true);

echo '<pre>';

// To test a site, run the test() method, and pass in at minimum a url to test. Returns
// the testid on success, or false and error messsage in $test->error if failure.
$url_to_test = "http://vrolijk.rhcdev.nl:84/";
echo "Testing $url_to_test\n";
$testid = $test->testSite([
    'url' => $url_to_test
    // , 'x-metrix-adblock' => 1
    // , 'x-metrix-video' => 1
]);

if ($testid) {
    echo "Test started with $testid<br />\n";
}
else {
    die("Test failed: " . $test->getError() . "<br />\n");
}

// Other options include:
//
//      location => 4  - test from the Dallas test region (see locations below)
//      login-user => 'foo',
//      login-pass => 'bar',  - the test requires http authentication
//      x-metrix-adblock => 1 - use the adblock plugin during this test to see the impact ads have on your site
//
// For more information on options, see http://gtmetrix.com/api/

// After calling the test method, your URL will begin testing. You can call:
echo "Waiting for test to finish<br />\n";
$test->waitForResults();

// which will block and return once your test finishes. Alternatively, can call:
//     $state = $test->state()
// which will return the current state. Please don't check more than once per second.

// Once your test is finished, chech that it completed ok, otherwise get the results.
// Note: you must check twice. The first ->test() method can fail if url is malformed, or
// other immediate error. However, if you get a job id, the test itself may fail if the url
// can not be reached, or some pagespeed error.
if ($test->getError())
{
    die($test->getError());
}
$testid = $test->getTestId();
// echo "Test completed succesfully with ID $testid<br />\n";
$results = $test->getTestResults();

foreach ($results as $result => $data)
{
    echo "  $result => $data<br />\n";
}

echo "\nResources<br />\n";
$resourceUrls = $test->getResourceUrls();

foreach ($resourceUrls as $resource => $url)
{
    echo "  Resource: $resource $url<br />\n";
}

$test->downloadResources(null, './downloads/', false);

// Each test has a unique test id. You can load an existing / old test result using:
echo "Loading test id $testid<br />\n";
$test->loadTest($testid);

// If you no longer need a test, you can delete it:
echo "Deleting test id $testid<br />\n";
$result = $test->deleteTest();
if (! $result) { die("error deleting test: " . $test->error()); }

// To list possible testing locations, use locations() method:
echo "\nLocations GTmetrix can test from:<br />\n";
$locations = $test->getTestLocations();
// Returns an array of associative arrays:
foreach ($locations as $location)
{
    echo "GTmetrix can run tests from: " . $location["name"] . " using id: " . $location["id"] . " default (" . $location["default"] . ")<br />\n";
}
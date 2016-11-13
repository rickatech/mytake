<?php
//require_once 'PHPUnit/Autoload.php';

//require_once ('PHPUnit/Framework/TestCase.php');

use PHPUnit\Framework\TestCase;

//  class SmokeTest extends TestCase
class SmokeTest extends PHPUnit_Framework_TestCase
{
    // ...

    public function testSmoke()
    {
        // Assert
        $this->assertEquals(1, 1);
    }

    // ...
}



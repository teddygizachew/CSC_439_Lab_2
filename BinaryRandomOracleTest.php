<?php
require_once 'Main.php';

use PHPUnit\Framework\TestCase;

class BinaryRandomOracleTest extends TestCase
{
    public function test_foo()
    {
        $this->assertSame(true, true);
    }

    public function test_get_outcome()
    {
        // create the sut
        // Create a stub for the SomeClass class.
        $stub = $this->createStub(RNG::class);

        // Configure the stub.
        $stub->method('choose')->willReturn(0);
        $sut = new BinaryRandomOracle($stub);
        $options = array("a" => "option a happened", "b" => "option b happened");

        // call a method on it
        $result = $sut->get_outcome($options);

        // verify the results
        $this->assertEquals("a", $result["bro_result"]);
        $this->assertEquals("option a happened", $result["result"]);

        $this->assertCount(2, $result);
    }

    public function test_get_outcome_exception()
    {
        // create the sut
        $this->expectException(BinaryRandomOracleException::class);
        $sut = new BinaryRandomOracle(new RNG());
        $options = array();

        // call a method on it
        $sut->get_outcome($options);
    }

    public function test_get_plain_outcome()
    {
        // create the sut
        // Create a stub for the SomeClass class.
        $stub = $this->createStub(RNG::class);

        // Configure the stub.
        $stub->method('choose')->willReturn(0);
        // create the sut
        $sut = new BinaryRandomOracle($stub);

        // call a method on it
        $result = $sut->get_plain_outcome(0);
        // verify the result

        $this->assertEquals($sut->get_bro_table()[0][0], $result);
    }
}

?>
<?php
require_once 'Main.php';

use PHPUnit\Framework\TestCase;

class BinaryRandomOracleTest extends TestCase
{
    public function test_foo(){
        $this->assertSame(true,true);
    }

//    public function test_get_outcome() {
//        // create the sut
//        $rng = new RNG();
//        $sut = new BinaryRandomOracle($rng);
//
//        // call a method on it
//        $result = $sut->get_outcome();
//        // verify the results
//        $this->assertEquals("", $result);
//    }
//
//    public function test_get_outcome_exception() {
//        $this->expectException(BinaryRandomOracleException::class);
//
//        // crete the sut
//        $rng = new RNG();
//        $sut = new BinaryRandomOracle($rng);
//    }
}

?>
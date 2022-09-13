<?php
require_once 'Main.php';

use PHPUnit\Framework\TestCase;

class CharacterModelTest extends TestCase
{
    public function setUp(): void
    {
        CharacterModel::add_available_attribute("Speed");
    }

    public function test_get_name()
    {
        // setup subject under test (sut)
        $rng = new RNG();
        $bro = new BinaryRandomOracle($rng);
        $sut = new CharacterModel($bro, "teddy", 0);

        // call a method on it
        $name = $sut->get_name();

        // verify the results
        // assertEquals(expected, actual)
        $this->assertEquals("teddy", $name);
    }

    public function test_increment_attribute()
    {
        // create the sut
        // Create a stub for the SomeClass class.
        $stub = $this->createStub(RNG::class);

        // Configure the stub.
        $stub->method('choose')->willReturn(1);
        $bro = new BinaryRandomOracle($stub);
        $sut = new CharacterModel($bro, "teddy", 1); // force it to have speed

        // call a method on it
        $sut->increment_attribute("Speed");

        // verify the results
        $this->assertEquals("HIGH", $sut->get_attributes()["Speed"]);

        $result = $sut->increment_attribute("Speed");
        $this->assertFalse($result);
    }

    public function test_increment_attribute_exception()
    {
        // "this" is CharacterModelTest that extends TestCase
        $this->expectException(CharacterModelException::class);
        // setup sut
        $rng = new RNG();
        $bro = new BinaryRandomOracle($rng);
        $sut = new CharacterModel($bro, "teddy", 0);

        // call method
        $sut->increment_attribute("dne");
    }

    public function test_decrement_attribute()
    {
        // create the sut
        // Create a stub for the SomeClass class.
        $stub = $this->createStub(RNG::class);

        // Configure the stub.
        $stub->method('choose')->willReturn(0);
        $bro = new BinaryRandomOracle($stub);
        $sut = new CharacterModel($bro, "teddy", 1); // force it to have speed

        // call a method on it
        $result = $sut->decrement_attribute("Speed");

        // verify the results
        $this->assertTrue($result);
        $this->assertEquals("LOW", $sut->get_attributes()["Speed"]);

        $result = $sut->decrement_attribute("Speed");
        $this->assertFalse($result);
    }

    public function test_decrement_attribute_exception()
    {
        // "this" is CharacterModelTest that extends TestCase
        $this->expectException(CharacterModelException::class);
        // setup sut
        $rng = new RNG();
        $bro = new BinaryRandomOracle($rng);
        $sut = new CharacterModel($bro, "teddy", 0);

        // call method
        $sut->decrement_attribute("Speed");
    }

    public function test_generate_character_exception()
    {
        $this->expectException(CharacterModelException::class);
        $bro = new BinaryRandomOracle(new RNG());
        $sut = new CharacterModel($bro, "teddy", 1);

        $sut->generate_character($bro, "teddy", 2);
    }

    public function test_add_available_attribute()
    {
        // create the sut
        $rng = new RNG();
        $bro = new BinaryRandomOracle($rng);
        $sut = new CharacterModel($bro, "teddy", 1);

        // call a method on it
        $result = $sut->add_available_attribute("Test");
        // verify the results
        $this->assertTrue($result);

        $result = $sut->add_available_attribute("Test");
        $this->assertFalse($result);
    }

    public function test_increment_brownie_points()
    {
        // create the sut
        $rng = new RNG();
        $bro = new BinaryRandomOracle($rng);
        $sut = new CharacterModel($bro, "teddy", 0);

        // call a method on it
        $result = $sut->increment_brownie_points(10);

        // verify the results
        $this->assertEquals(10, $result);

        $result = $sut->increment_brownie_points(-10);
        $this->assertEquals(0, $result);
    }

    public function test_decrement_brownie_points()
    {
        // create the sut
        $rng = new RNG();
        $bro = new BinaryRandomOracle($rng);
        $sut = new CharacterModel($bro, "teddy", 0);

        // call a method on it
        $result = $sut->decrement_brownie_points(10);

        // verify the results
        $this->assertEquals(0, $result);
    }

    public function test_get_brownie_points()
    {
        // create the sut
        $rng = new RNG();
        $bro = new BinaryRandomOracle($rng);
        $sut = new CharacterModel($bro, "teddy", 1);

        // call a method on it
        $result = $sut->get_brownie_points();
        // verify the results
        $this->assertEquals(0, $result);

        $sut->increment_brownie_points(10);
        $result = $sut->get_brownie_points();

        $this->assertEquals(10, $result);
    }

    public function test_get_attributes()
    {
        // create the sut
        $rng = new RNG();
        $bro = new BinaryRandomOracle($rng);
        $sut = new CharacterModel($bro, "teddy", 1);

        // call a method on it
        $result = $sut->get_attributes();
        // verify the results
        $this->assertCount(1, $result);
    }

    public function test__constructor()
    {
        // create the sut
        $bro = new BinaryRandomOracle(new RNG());
        $sut = new CharacterModel($bro, "apple", 1);

        $name = $sut->get_name();
        $brownie_points = $sut->get_brownie_points();
        $attributes = $sut->get_attributes();

        $this->assertCount(1, $attributes);
        $this->assertEquals("apple", $name);
        $this->assertEquals(0, $brownie_points);

    }

    public function test_generate_character()
    {
        // create the sut
        $stub = $this->createStub(RNG::class);

        // Configure the stub.
        $stub->method('choose')->willReturn(1);

        $bro = new BinaryRandomOracle($stub);
        CharacterModel::add_available_attribute("Speed");
        CharacterModel::add_available_attribute("Speed2");
        $sut = new CharacterModel($bro, "teddy", 1);

        // call a method on it
        $sut->generate_character($bro, "tedBear", 2);
        // verify the results
        $result = $sut->get_name();
        $this->assertEquals("teddy", $result);
    }

    // happens after each test case
    public function tearDown(): void
    {
        CharacterModel::clear_available_attributes();
    }
}

?>
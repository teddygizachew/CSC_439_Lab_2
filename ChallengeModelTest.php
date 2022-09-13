<?php
require_once 'Main.php';

use PHPUnit\Framework\TestCase;

class ChallengeModelTest extends TestCase
{
    public function test_foo()
    {
        $this->assertSame(true, true);
    }

    /**
     * Test the constructors using getter methods
     *
     * @return void
     */
    public function test__construct()
    {
        // create the sut
        $id = 20;
        $intro_text = "intro";
        $test_attribute = "test attribute";
        $threat = "threat";
        $succeed_next = "succeed_next";
        $failure_next = "failure_next";
        $equal_options = array();
        $character_advantage_options = array();
        $challenge_advantage_options = array();

        $sut = new ChallengeModel($id, $intro_text, $test_attribute, $threat, $succeed_next, $failure_next, $equal_options, $character_advantage_options, $challenge_advantage_options
        );

        // verify the result
        $this->assertEquals($id, $sut->get_id());
        $this->assertEquals($intro_text, $sut->get_intro_text());
        $this->assertEquals($test_attribute, $sut->get_test_attribute());
        $this->assertEquals($threat, $sut->get_threat());
        $this->assertEquals($succeed_next, $sut->get_succeed_next());
        $this->assertEquals($failure_next, $sut->get_failure_next());
        $this->assertEquals($equal_options, $sut->get_equal_options());
        $this->assertEquals($character_advantage_options, $sut->get_character_advantage_options());
        $this->assertEquals($challenge_advantage_options, $sut->get_challenge_advantage_options());
    }

    public function test_run_challenge_HIGH()
    {
        // create the sut
        $stub = $this->createStub(RNG::class);

        // Configure the stub.
        $stub->method('choose')->willReturn(0);

        $bro = new BinaryRandomOracle($stub);
        CharacterModel::add_available_attribute("Speed");
        $character = new CharacterModel($bro, "teddy", 1);
        $character->increment_attribute("Speed");

        // create the sut
        $id = "bus_challenge";
        $intro_text = "intro";
        $test_attribute = "Speed";
        $threat = "HIGH";
        $succeed_next = "seat_challenge";
        $failure_next = "_done";
        $equal_options = array(
            array("A" => "You run as fast as you can, and you almost catch up to the bus.",
                "B" => "You run as fast as you can, but you can't seem to gain any ground catching up to the bus."
            ),
            array("A" => "You run with all of your might, and the driver sees you in the mirror waving, and lets you on.",
                "B" => "You step in a puddle and it slows you down.",
            ),
            array("A" => "The bus pulls over out of pity and lets you on.",
                "B" => "You couldn't catch up, the bus drives away."
            )
        );
        $character_advantage_options = array(
            array("A" => "The bus is pulling away, but you are pretty fast and easily catch it.",
                "B" => "The bus is pulling away, and even though you are pretty fast, you can't seem to catch up."
            ),
            array("A" => "Although harder than you expected, you put your head down and eventually catch up to the bus, getting on.",
                "B" => "You are surprised to find you are not as fast as you once were."
            )
        );
        $challenge_advantage_options = array(
            array("B" => "The bus is pulling away, and you are far to slow to really catch it, and it drives out of sight.",
                "A" => "The bus is pulling away, and even though you are pretty slow, you seem to gain ground."
            ),
            array("B" => "But try as you might, it eventually gets away.",
                "A" => "By some miracle, you actually flag down the driver to stop, and get on."
            )
        );

        $bus_challenge = new ChallengeModel($id, $intro_text, $test_attribute, $threat, $succeed_next, $failure_next, $equal_options, $character_advantage_options, $challenge_advantage_options
        );

        // call a method on it
        $result = $bus_challenge->run_challenge($bro, $character);

        $this->assertTrue($result["success"]);
        $this->assertEquals($equal_options[0]["A"], $result["output"][1]);
        $this->assertEquals($succeed_next, $result["next"]);
    }

    public function test_run_challenge_character_advantage()
    {
        // create the sut
        $stub = $this->createStub(RNG::class);

        // Configure the stub.
        $stub->method('choose')->willReturn(1);

        $bro = new BinaryRandomOracle($stub);
        CharacterModel::add_available_attribute("Speed");
        $character = new CharacterModel($bro, "teddy", 1);
        $character->increment_attribute("Speed");


        // create the sut
        $id = "bus_challenge";
        $intro_text = "intro";
        $test_attribute = "Speed";
        $threat = "LOW";
        $succeed_next = "seat_challenge";
        $failure_next = "_done";
        $equal_options = array(
            array("A" => "You run as fast as you can, and you almost catch up to the bus.",
                "B" => "You run as fast as you can, but you can't seem to gain any ground catching up to the bus."
            ),
            array("A" => "You run with all of your might, and the driver sees you in the mirror waving, and lets you on.",
                "B" => "You step in a puddle and it slows you down.",
            ),
            array("A" => "The bus pulls over out of pity and lets you on.",
                "B" => "You couldn't catch up, the bus drives away."
            )
        );
        $character_advantage_options = array(
            array("A" => "The bus is pulling away, but you are pretty fast and easily catch it.",
                "B" => "The bus is pulling away, and even though you are pretty fast, you can't seem to catch up."
            ),
            array("A" => "Although harder than you expected, you put your head down and eventually catch up to the bus, getting on.",
                "B" => "You are surprised to find you are not as fast as you once were."
            )
        );
        $challenge_advantage_options = array(
            array("B" => "The bus is pulling away, and you are far to slow to really catch it, and it drives out of sight.",
                "A" => "The bus is pulling away, and even though you are pretty slow, you seem to gain ground."
            ),
            array("B" => "But try as you might, it eventually gets away.",
                "A" => "By some miracle, you actually flag down the driver to stop, and get on."
            )
        );

        $bus_challenge = new ChallengeModel($id, $intro_text, $test_attribute, $threat, $succeed_next, $failure_next, $equal_options, $character_advantage_options, $challenge_advantage_options
        );

        // call a method on it
        $result = $bus_challenge->run_challenge($bro, $character);

        $this->assertFalse($result["success"]);
        $this->assertEquals($equal_options[0]["B"], $result["output"][1]);
        $this->assertEquals($failure_next, $result["next"]);
    }

    public function test_run_challenge_challenge_advantage()
    {
        // create the sut
        $stub = $this->createStub(RNG::class);

        // Configure the stub.
        $stub->method('choose')->willReturn(1);

        $bro = new BinaryRandomOracle($stub);
        CharacterModel::add_available_attribute("Speed");
        $character = new CharacterModel($bro, "teddy", 1);
        $character->increment_attribute("Speed");
        $character->decrement_attribute("Speed");


        // create the sut
        $id = "bus_challenge";
        $intro_text = "intro";
        $test_attribute = "Speed";
        $threat = "HIGH";
        $succeed_next = "seat_challenge";
        $failure_next = "_done";
        $equal_options = array(
            array("A" => "You run as fast as you can, and you almost catch up to the bus.",
                "B" => "You run as fast as you can, but you can't seem to gain any ground catching up to the bus."
            ),
            array("A" => "You run with all of your might, and the driver sees you in the mirror waving, and lets you on.",
                "B" => "You step in a puddle and it slows you down.",
            ),
            array("A" => "The bus pulls over out of pity and lets you on.",
                "B" => "You couldn't catch up, the bus drives away."
            )
        );
        $character_advantage_options = array(
            array("A" => "The bus is pulling away, but you are pretty fast and easily catch it.",
                "B" => "The bus is pulling away, and even though you are pretty fast, you can't seem to catch up."
            ),
            array("A" => "Although harder than you expected, you put your head down and eventually catch up to the bus, getting on.",
                "B" => "You are surprised to find you are not as fast as you once were."
            )
        );
        $challenge_advantage_options = array(
            array("B" => "The bus is pulling away, and you are far to slow to really catch it, and it drives out of sight.",
                "A" => "The bus is pulling away, and even though you are pretty slow, you seem to gain ground."
            ),
            array("B" => "But try as you might, it eventually gets away.",
                "A" => "By some miracle, you actually flag down the driver to stop, and get on."
            )
        );

        $bus_challenge = new ChallengeModel($id, $intro_text, $test_attribute, $threat, $succeed_next, $failure_next, $equal_options, $character_advantage_options, $challenge_advantage_options
        );

        // call a method on it
        $result = $bus_challenge->run_challenge($bro, $character);

        $this->assertFalse($result["success"]);
        $this->assertEquals($equal_options[0]["B"], $result["output"][1]);
        $this->assertEquals($failure_next, $result["next"]);
    }
}

?>
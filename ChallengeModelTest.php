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
}

?>
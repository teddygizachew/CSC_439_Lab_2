<?php
declare(strict_types=1);

/**
 * BinaryRandomOracleException is a 'custom exception' we can raise, extending
 * from the base Exception.
 *
 */
class BinaryRandomOracleException extends Exception {
}

/**
 * CharacterModelException is a 'custom exception' we can raise, extending
 * from the base Exception.
 *
 */
class CharacterModelException extends Exception {
}

/**
 *RNG is a class so we can mock out the random number generation.
 */
class RNG {
    public function choose() : int {
        //we do not need to test this method
        return rand(0,1);
    }
}

/**
 * BinaryRandomOracle is a class that uses an RNG to generate random numbers,
 * and returns either HIGH/YES/A or LOW/NO/B.
 */
class BinaryRandomOracle {

    //These constants are used as indicies into the "columns" of $bro_table.
    const HIGH_LOW_IDX = 0;
    const YES_NO_IDX = 1;
    const A_B_IDX = 2;

    //The values that the BRO can return.
    private array $bro_table = array(
        array("HIGH", "YES", "A"),
        array("LOW", "NO", "B")
    );

    //An instance of RNG so we can generage numbers
    private RNG $rng;

    /**
     * constructor for the BinaryRandomOracle class.
     *
     * @param RNG $rng This is the random number generator for the BRO.
     */
    public function __construct(RNG $rng){
        $this->rng = $rng;
    }

    /**
     * Receives an array of options, returns an array with what the BRO said
     * and the option it picked.
     *
     * @param array $options A key/value array of the form "A"=>"option a",
     *      "B"=>"option b".  This will return one option based on RNG->choose()
     * @return array An array in the form of "bro_result" => "A", "result" =>
     *      "The result from $options"
     */
    public function get_outcome(array $options) : array {
        //select one of the two arrays from the $bro_table
        $outcome_keys = $this->bro_table[$this->rng->choose()];
        //Iterate through each of our options given in the parameter $options
        foreach($options as $key => $value){
            //if the uppercase key ("A", "YES", "LOW", etc.) is in what was
            //selected from the bro table...
            if( in_array(strtoupper($key), $outcome_keys) ) {
                //return that one
                return array(
                    "bro_result"=>$key,
                    "result"=>$options[$key]
                );
            }
        }
        //if we get through the for loop and nothing was chosen, throw exception
        //(we have invalid $options that don't match up with $bro_table)
        throw new BinaryRandomOracleException("Options array did not conform to bro table: $options");
    }

    /**
     * This returns a simple YES/NO, HIGH/LOW, A/B result.
     * @param int $idx Which "column" to use.
     * @return string an answer from $bro_table
     */
    public function get_plain_outcome(int $idx) : string {
        return $this->bro_table[$this->rng->choose()][$idx];
    }
}

/**
 * CharacterModel is a class representing the data of a character.
 */
class CharacterModel {
    //a key/value array of "attribute_name"=>"HIGH/LOW"
    private array $attributes;
    //an integer for character points
    private int $brownie_points;
    //a string name
    private string $name;

    //a static array (shared by all classes) that holds all possible attributes
    private static array $available_attributes = array();

    /**
     * constructor for the CharacterModel Class
     *
     * @param BinaryRandomOracle $bro A BinaryRandomOracle to pick attribute level
     * @param string $name The name of the character
     * @param int $num_attributes The number of attributes from the pool of attributes this character should randomly select.
     */
    public function __construct(BinaryRandomOracle $bro, string $name, int $num_attributes){
        //set attributes from params
        $this->attributes = array();
        $this->brownie_points = 0;
        $this->name = $name;
        //call generage character to set attributes
        $this->generate_character($bro, $name, $num_attributes);
    }

    /**
     * Adds an attribute to the total pool of attributes, handles duplicates.
     *
     * @param string $attribute The attribute to add to the pool.
     * @return bool True if it was successfully added, or false if duplicate
     */
    public static function add_available_attribute(string $attribute) : bool {
        //if the parameter $attribute is not in our static array of attributes
        if( !in_array($attribute, CharacterModel::$available_attributes) ){
            //append attribute to available_attributes ([] used like this is append
            CharacterModel::$available_attributes[] = $attribute;
            return true;
        } else {
            return false;
        }
    }

    /**
     * Generates character attibutes by selecting random attributes from the
     * pool, and then assigning them a random HIGH/LOW level.
     *
     * @param BinaryRandomOracle $bro We will use this to get random HIGH/LOW levels.
     * @param string $name Unused,to be removed in future versions.
     * @param int $num_attributes The number of attributes to randomly select from the pool.
     * @return void
     */
    public function generate_character(BinaryRandomOracle $bro, string $name, int $num_attributes) : void {
        //if we try to select more attributes than exist in our pool, throw exception
        if($num_attributes > count(CharacterModel::$available_attributes)){
            throw new CharacterModelException("Not enough available attributes to generate character with $num_attributes attributes.");
        } else {
            //while we still are getting attributes to the number requested...
            while(count($this->attributes) < $num_attributes) {
                //select a random key from the available attributes, this
                //will be a number index.
                $rand_key = array_rand(CharacterModel::$available_attributes);
                //the random attribute will be the value at that key
                $random_attribute = CharacterModel::$available_attributes[$rand_key];
                //If we do not currently have a key in $this->attribuets
                //with the name of the attribute we randomly selected...
                if( !isset($this->attributes[$random_attribute])){
                    //set it as a random HIGH/LOW level in our attributes
                    $this->attributes[$random_attribute] = $bro->get_plain_outcome($bro::HIGH_LOW_IDX);
                }
                //otherwise, keep looping until all are selected
            }
        }
    }

    /*
    public function load_character(string $filename) : bool {

    }

    public function save_character(string $filename) : bool {

    }
    */

    /**
     * Decrements an attribute from HIGH to LOW.  If already LOW, stays LOW.
     *
     * @param string $attribute The attribute to decrement.
     * @return bool true if was decremented, false if was already LOW.
     */
    public function decrement_attribute(string $attribute) : bool {
        //make sure this attribute is in our array of attributes...
        if( isset($this->attributes[$attribute]) ) {
            if($this->attributes[$attribute] == "HIGH"){
                $this->attributes[$attribute] = "LOW";
                return true;
            } else {
                return false;
            }
        } else {
            //otherwise throw exception.
            throw new CharacterModelException("Attempt to decrement attribute $attribute that the character does not have.");
        }
    }

    /**
     * Increments an attribute from LOW to HIGH.  If already HIGH, stays HIGH.
     *
     * @param string $attribute The attribute to increment.
     * @return bool true if was incremented, false if was already HIGH.
     */
    public function increment_attribute(string $attribute) : bool {
        //make sure this attribute is in our array of attributes...
        if( isset($this->attributes[$attribute]) ) {
            if($this->attributes[$attribute] == "LOW"){
                $this->attributes[$attribute] = "HIGH";
                return true;
            } else {
                return false;
            }
        } else {
            //otherwise throw exception.
            throw new CharacterModelException("Attempt to increment attribute $attribute that the character does not have.");
        }
    }

    /**
     * Increments brownie points by a specivied value
     *
     * @param int $value The amount to increment it by.
     * @return int The new value of brownie points.
     */
    public function increment_brownie_points(int $value) : int {
        $this->brownie_points += $value;
        return $this->brownie_points;
    }

    /**
     * Decrements brownie points, capping the minimum at 0.
     *
     * @param int $value The amount to decrement it by.
     * @return int The new value of brownie points.
     */
    public function decrement_brownie_points(int $value) : int {
        $this->brownie_points -= $value;
        if($this->brownie_points < 0){
            $this->brownie_points = 0;
        }
        return $this->brownie_points;
    }

    /**
     * Getter for the name attribute.
     *
     * @return string
     */
    public function get_name() : string {
        return $this->name;
    }

    /**
     * Getter for the brownie_points attribute
     *
     * @return int
     */
    public function get_brownie_points() : int {
        return $this->brownie_points;
    }

    /**
     * Getter for the attributes array.
     *
     * @return array An array in the form of "attribute"=>"level"
     */
    public function get_attributes() : array {
        return $this->attributes;
    }
}

/**
 * ChallengeModel represents the data for a challenge that the character will
 * go through.
 *
 * Each challenge has a trait and a threat level associated with it.  If the
 * characters trait level and the threat level are equal (HIGH/HIGH, LOW/LOW)
 * then it is a best 2 out of 3 of a BRO selecting outcomes from the
 * $equal_options array.  If the character attribute is HIGH and the threat
 * level is LOW, then the character must win 1/2 in order to win.  If the threat
 * is HIGH and character attribute is LOW, then you need 2/2 to win.
 */
class ChallengeModel {
    //id of the challenge
    private string $id;
    //intro text that is displayed before the challenge
    private string $intro_text;
    //text should be outputted if character succeeds
    private string $success_text;
    //text shoudl be outputted if character fails
    private string $failure_text;
    //what attribute this challenge tests
    private string $test_attribute;
    //the level of this challenge
    private string $threat;
    //the ID of the next challenge, if succeeds
    private string $succeed_next;
    //the id of the next challenge, if failed
    private string $failure_next;
    //an array of 3 arrays listing the best of 2 out of 3 options
    private array $equal_options;
    //an array of 2 arrays listing the at least 1 out of 2 options
    private array $character_advantage_options;
    //a array of 2 arrays listing the needs 2 out of 2 options
    private array $challenge_advantage_options;

    /**
     * Builds the challenge from paramaters given from the user.
     * See class for parameter description.
     */
    public function __construct(string $id,
                                string $intro_text,
                                string $test_attribute,
                                string $threat,
                                string $succeed_next,
                                string $failure_next,
                                array $equal_options,
                                array $character_advantage_options,
                                array $challenge_advantage_options
    ){
        $this->id = $id;
        $this->intro_text = $intro_text;
        $this->test_attribute = $test_attribute;
        $this->threat = $threat;
        $this->succeed_next = $succeed_next;
        $this->failure_next = $failure_next;
        $this->equal_options = $equal_options;
        $this->character_advantage_options = $character_advantage_options;
        $this->challenge_advantage_options = $challenge_advantage_options;
    }

    /**
     * Compares the character attribute level with the challenge threat level
     * and runs the challenge accordingly.
     *
     * @param BinaryRandomOracle $bro A BRO used to choose outcomes
     * @param CharacterModel $character The CharacterModel attempting this challenge
     * @return array An array with the values output=>output generated from
     * challenge, success=>success of challenge, next=>the next challengeid
     */
    public function run_challenge(BinaryRandomOracle $bro, CharacterModel $character) : array {
        //get the characters attribute level
        $attribute_level = $character->get_attributes()[$this->test_attribute];
        $result = array();
        //if equal, do the best 2 out of 3 option
        if($attribute_level === $this->threat){
            $result = $this->run_options($bro, $this->equal_options, 2, 2);
            //if not equal ane character is HIGH, then do the best 1 of 2 scenario
        } elseif ($attribute_level === "HIGH") {
            $result = $this->run_options($bro, $this->character_advantage_options, 1, 2);
            //otherwise, do the "need 2 to win" scenario
        } else {
            $result = $this->run_options($bro, $this->challenge_advantage_options, 2, 1);
        }

        //add the intro text to the beginning of the output generated by challenge
        array_unshift($result["output"], $this->intro_text);
        if($result["success"] === false){
            $result["next"] = $this->failure_next;
        } else {
            $result["next"] = $this->failure_success;
        }
        return $result;
    }

    /**
     * Runs the options based on the relationship of threat level and character
     * attribute level.
     *
     * @param BinaryRandomOracle $bro A BRO to make decisions.
     * @param array $options The array to choose from.
     * @param int $success_max The number of successes (A choices) required to succeed.
     * @param int $fail_max The number of failures (B choices) that will cause failure.
     * @return array An array with output=>array of lines of text, success=> bool if challenge succeeded.
     */
    private function run_options(BinaryRandomOracle $bro, array $options, $success_max, $failure_max) {
        //initialize rv (return value)
        $rv = array(
            "success"=> false,
            "output"=> array(),
        );
        //initialize succes/fail counts at 0.
        $success_count = 0;
        $fail_count = 0;
        //foreach of the options in the attribute given in the param:
        //(Note the bug here, should be foreach($options as $option) )
        //+1 for static analysis, -1 for coverage analysis
        foreach($this->equal_options as $option){
            //call the BRO's get_outcome with the options
            $outcome = $bro->get_outcome($option);
            //append the outcomes result text to our output
            //again, note the [] syntax for "append"
            $rv["output"][] = $outcome["result"];
            //A is success, B is failure...
            if($outcome["bro_result"] === "A"){
                $success_count++;
            } else {
                $failure_count++;
            }
            //if we've succeeded enough times, break, success true
            if($success_count == $success_max){
                $rv["success"] = true;
                break;
            }
            //if we've failed enough times, break, fail true
            if($failure_count == $failure_max){
                $rv["success"] = false;
                break;
            }
        }
        //finally, return $rv
        return $rv;
    }
}

/*
class AdventureController {

}

class View {

}
*/


if( isset($argv) && $argv[0] && realpath($argv[0]) == __FILE__){
    /*
    this block of code will only run if it is the main file run, if it is
    imported, then it will not be run.
    */

    $bro = new BinaryRandomOracle(new RNG());
    $options = array("a" => "option a happened", "b" => "option b happened");
    var_dump( $bro->get_outcome($options));

    CharacterModel::add_available_attribute("Speed");
    CharacterModel::add_available_attribute("Morality");
    CharacterModel::add_available_attribute("Toughness");
    $character = new CharacterModel($bro, "Brian", 3);
    $character->increment_brownie_points(5);
    $character->decrement_brownie_points(2);
    $character->increment_attribute("Speed");
    $character->decrement_attribute("Toughness");
    echo "brownie points: $character->get_brownie_points()";
    var_dump($character);

    $bus_challenge = new ChallengeModel(
        "bus_challenge",
        "You are attempting to catch the bus, but you are late, and it is pulling away from the stop.",
        "Speed",
        "HIGH",
        "seat_challenge",
        "_done",
        array(
            array("A"=> "You run as fast as you can, and you almost catch up to the bus.",
                "B"=> "You run as fast as you can, but you can't seem to gain any ground catching up to the bus."
            ),
            array("A"=> "You run with all of your might, and the driver sees you in the mirror waving, and lets you on.",
                "B" => "You step in a puddle and it slows you down.",
            ),
            array("A"=> "The bus pulls over out of pity and lets you on.",
                "B" => "You couldn't catch up, the bus drives away."
            )
        ),
        array(
            array("A"=> "The bus is pulling away, but you are pretty fast and easily catch it.",
                "B"=> "The bus is pulling away, and even though you are pretty fast, you can't seem to catch up."
            ),
            array("A"=> "Although harder than you expected, you put your head down and eventually catch up to the bus, getting on.",
                "B"=>"You are surprised to find you are not as fast as you once were."
            )
        ),
        array(
            array("B"=> "The bus is pulling away, and you are far to slow to really catch it, and it drives out of sight.",
                "A"=> "The bus is pulling away, and even though you are pretty slow, you seem to gain ground."
            ),
            array("B"=> "But try as you might, it eventually gets away.",
                "A"=>"By some miracle, you actually flag down the driver to stop, and get on."
            )
        )
    );
    $result = $bus_challenge->run_challenge($bro, $character);
    var_dump($result);

}

?>
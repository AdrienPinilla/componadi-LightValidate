<?php


class LightValidate{

    /**
     * An array with all type of data we can test
     *
     * @var array
     */
    private $validateType = [
        'i' => FILTER_VALIDATE_INT,
        'f' => FILTER_VALIDATE_FLOAT,
        'a' => FILTER_SANITIZE_SPECIAL_CHARS,
        'pwd' => FILTER_SANITIZE_SPECIAL_CHARS,
        'url' => FILTER_VALIDATE_URL,
        'mail' => FILTER_VALIDATE_EMAIL
    ];

    /**
     * all invalide characters for password (you can redefine or add a special character)
     *
     * @var string
     */
    private $invalideSpecialCharacterPassword = "\[,:;=|'<>.^*()\]";

    /**
     * An Array with all method of our filter_input
     *
     * @var array
     */
    private $validateMethod = [
        'GET' => INPUT_GET,
        'POST' => INPUT_POST
    ];

    /**
     * An array to stock the result of our validation test
     *
     * @var array
     */
    private $result = [
        'data' => "",
        'isValide' => true,
        'errorMessage' => "everything is ok !"
    ];

    /**
     * Method who test a data to validate data
     * We exit method when encounter one error and return an error message in array $result
     *
     * @param mixed $dataToTest name of value you want to test in your method
     * @param string $method method utilised by data you need to enter 'GET' or 'POST'
     * @param string $type type of $dataToTest you need to enter :
     *                                                              - 'i' => to test an int
     *                                                              - 'f' => to test a float
     *                                                              - 'a' => to test a string
     *                                                              - 'pwd' => to test a password
     *                                                              - 'url' => to test an url
     *                                                              - 'mail' => to test an email
     *                                                              
     * @param array $minMax (option) You can define a min or max value of your $dataToTest you need to enter an array
     *                               this test works with int, float and string, for a string value 'min-e' and 'max-e' is not necesairy. You can define a :
     *                               - min value with the syntax : 'min'
     *                               - min or equal value with the syntax : 'min-e'
     *                               - max value with the syntax : 'max'
     *                               - max or equal value with the syntax : 'max-e'
     *                               ex:
     *                                      [ 'min' => 3 , 'max-e' => 10]
     *                                      [ 'min-e' => 5 ]
     *                                      [ 'max' => 25 ]
     * 
     * @return array return an array $outputResult with $dataToTest, a bool true or false if the validation is correct or not and an error message to know what is incorrect
     */
    public function validate($dataToTest,string $method,string $type, array $minMax = []){

        $this->result['data'] = $_POST[$dataToTest];

        //==========================================================
        //              Tests parameter's values 
        //==========================================================

        if (!array_key_exists($method, $this->validateMethod)){

            $this->result['errorMessage'] = "You enter an incorrect method value";
            $this->result['isValide'] = false;

            $outputResult = $this->result;
            return $outputResult;
        }

        if(!array_key_exists($type, $this->validateType)){

            $this->result['errorMessage'] = "You enter an incorrect type value";
            $this->result['isValide'] = false;
            $outputResult = $this->result;
            return $outputResult;
        }

        // we test values of $minMax if $minMax is not empty
        if(!empty($minMax)){

            $numberOfValue = 0;

            if(isset($minMax['min'])){
                $numberOfValue ++;
            } elseif (isset($minMax['min-e'])){
                if($type === 'a' || $type === 'url' || $type === 'mail'){
                    $this->result['errorMessage'] = "You enter an incorrect min/max value, if you use a string type you need to enter 'min' or 'max'";
                    $this->result['isValide'] = false;
                    $outputResult = $this->result;
                    return $outputResult;
                }
                $numberOfValue ++;
            } elseif (isset($minMax['max'])){
                $numberOfValue ++;
            } elseif (isset($minMax['max-e'])){
                if($type === 'a' || $type === 'url' || $type === 'mail'){
                    $this->result['errorMessage'] = "You enter an incorrect min/max value, if you use a string type you need to enter 'min' or 'max'";
                    $this->result['isValide'] = false;
                    $outputResult = $this->result;
                    return $outputResult;
                }
                $numberOfValue ++;
            }

            // If not have one correct value  
            if ($numberOfValue === 0){
                $this->result['errorMessage'] = "You enter an incorrect min/max value, you need to enter 'min', 'min-e', 'max', 'max-e' ";
                $this->result['isValide'] = false;
                $outputResult = $this->result;
                return $outputResult;
            }
        }

        //==========================================================
        //          Test $dataToTest with filter input 
        //==========================================================

        $validateResult = filter_input($this->validateMethod[$method], $dataToTest, $this->validateType[$type]);

        // if $dataToTest is incorrect
        if(!$validateResult){

            $this->result['errorMessage'] = "Your data isn't pass validate test";
            $this->result['isValide'] = false;
            $outputResult = $this->result;
            return $outputResult;
        } else {

            // we test if $validateResult contains a special character, if is true is incorrect
            if ($type === 'a'){
                $regex = "/[$&+,:;=?@#|'-<>.^*()%!]/";

                if(preg_match($regex, $validateResult)){
                    $this->result['errorMessage'] = "Your data contains a special character who not authorised, your data can't contains '[$&+,:;=?@#|'<>.-^*()%!]'";
                    $this->result['isValide'] = false;
                    $outputResult = $this->result;
                    return $outputResult;
                }
            } elseif ($type === 'pwd'){
                $regex = "/[{$this->invalideSpecialCharacterPassword}]/";

                if(preg_match($regex, $validateResult)){
                    $this->result['errorMessage'] = "Your data contains a special character who not authorised, your data can't contains '{$this->invalideSpecialCharacterPassword}'";
                    $this->result['isValide'] = false;
                    $outputResult = $this->result;
                    return $outputResult;
                }
            }
        }

        //==========================================================
        // Test $valideResult with $minMax if $minMax is not empty
        //==========================================================

        // if $minMax is not empty, and $validateResult is true
        if(!empty($minMax)){

            // Make test with numeric values
            if($type === 'i' || $type === 'f'){
                
                //test if 'min' exist
                if (isset($minMax['min'])){

                    // test $validateResult with min value
                    if ($validateResult < $minMax['min']){

                        $this->result['errorMessage'] = "Your data is too small, your data need bigger than : " . $minMax['min'];
                        $this->result['isValide'] = false;
                        $outputResult = $this->result;
                        return $outputResult;
                    }
                // if 'min-e' exist 
                } elseif (isset($minMax['min-e'])){
                    
                    // test $validateResult with min-e value
                    if ($validateResult <= $minMax['min-e']){

                        $this->result['errorMessage'] = "Your data is too small, your data need bigger or equal than : " . $minMax['min-e'];
                        $this->result['isValide'] = false;
                        $outputResult = $this->result;
                        return $outputResult;
                    }
                // if 'max' exist
                } elseif (isset($minMax['max'])){

                    // test $validateResult with max value
                    if ($validateResult > $minMax['max']){

                        $this->result['errorMessage'] = "Your data is too big, your data need smaller than : " . $minMax['max'];
                        $this->result['isValide'] = false;
                        $outputResult = $this->result;
                        return $outputResult;
                    }
                // if 'max-e' exist
                } elseif (isset($minMax['max-e'])){
                    
                    // test $validateResult with max-e value
                    if ($validateResult >= $minMax['max-e']){

                        $this->result['errorMessage'] = "Your data is too big, your data need smaller or equal than : " . $minMax['max-e'];
                        $this->result['isValide'] = false;
                        $outputResult = $this->result;
                        return $outputResult;
                    }
                }
            } elseif ($type === 'a' || $type === 'url' || $type === 'mail'){

                //test if 'min' exist
                if (isset($minMax['min'])){

                    // test $validateResult with min value
                    if (strlen($validateResult) < $minMax['min']){

                        $this->result['errorMessage'] = "Your data is too short, your data need longer or equal than : " . $minMax['min'];
                        $this->result['isValide'] = false;
                        $outputResult = $this->result;
                        return $outputResult;
                    }
                // if 'max' exist
                } elseif (isset($minMax['max'])){

                    // test $validateResult with max value
                    if (strlen($validateResult) > $minMax['max']){

                        $this->result['errorMessage'] = "Your data is too long, your data need shorter or equal than : " . $minMax['max'];
                        $this->result['isValide'] = false;
                        $outputResult = $this->result;
                        return $outputResult;
                    }
                }
            }
        }



        //==========================================================
        //          If all test is ok we return $result
        //==========================================================

        $this->result['data'] = $validateResult;

        $outputResult = $this->result;
        return $outputResult;
    }

    /**
     * This method add one or more invalid special character 
     *
     * @param string $specialCharacter you can specify one or more invalide special character to add
     * @return void
     */
    public function addInvalideSpecialCharacter($specialCharacter){

        $this->invalideSpecialCharacterPassword .= $specialCharacter;
    }

    /**
     * This method remove special character you want
     *
     * @param string $specialCharacterToRemove you can specify one or more invalid special character to remove
     * @return void
     */
    public function removeInvalideSpecialCharacter($specialCharacterToRemove){

        str_replace($specialCharacterToRemove, "", $this->invalideSpecialCharacterPassword);
    }

    /**
     * Get all invalide characters for password
     *
     * @return  string
     */ 
    public function getInvalideSpecialCharacterPassword()
    {

        return str_replace("\\", "", $this->invalideSpecialCharacterPassword);
    }

    /**
     * Set all invalide characters for password
     *
     * @param  string  $invalideSpecialCharacterPassword  all invalide characters for password
     *
     * @return  self
     */ 
    public function setInvalideSpecialCharacterPassword(string $invalideSpecialCharacterPassword)
    {
        $this->invalideSpecialCharacterPassword = $invalideSpecialCharacterPassword;

        return $this;
    }
}
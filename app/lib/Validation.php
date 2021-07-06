<?php

namespace app\lib;

class Validation{

    protected $element;
    protected $value;
    public $errors = [];

    public function setElement($element,$value){
        $this->element = $element;
        $this->value = $value;
        return $this;
    }

    public function required(){                       
        if (!isset($this->errors[$this->element]) && empty($this->value)) {
            $this->errors[$this->element] = ucfirst($this->element).' muss ausgefüllt werden';
        }
            return $this;
    }

    public function min(int $min){
        if (!isset($this->errors[$this->element]) && mb_strlen($this->value) < $min) {
            $this->errors[$this->element] = 'Der '.ucfirst($this->element).' muss mind. '.$min.' Zeichen haben.';
        }
            return $this;
    }

    public function max(int $max){
        if (!isset($this->errors[$this->element]) && mb_strlen($this->value) > $max) {
            $this->errors[$this->element] = 'Der '.ucfirst($this->element).' darf max. '.$max.' Zeichen haben.';
        }
            return $this;
    }

    public function email(){
        if (!isset($this->errors[$this->element]) && !filter_var($this->value,FILTER_VALIDATE_EMAIL)) {
            $this->errors[$this->element] = ucfirst($this->element).'format ist falsch.';
        }
    }

    public function arrayCheck($arr, $custom_msg)
    {
        if (!isset($this->errors[$this->element]) && !in_array($this->value,$arr)) {
            $this->errors[$this->element] = $custom_msg;
        }
        return $this;
    }

    public function isValid($element=false){
        $element = (!$element) ? $this->element : $element;
        if (isset($this->errors[$element])) {
            return $this->errors[$element];
        }
        return false;
    }

    public function isError(){
        return !empty($this->errors);
    }
}
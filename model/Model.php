<?php

/**
 * Model.php
 * 
 * @author gio regarde <gioregarde@outlook.com>
 */
class Model {

    private $name;
    private $address;
    private $subModel;

    function __construct() {
        $this -> name = 'Model : Hello World!';
        $this -> address = '';

        $subModel = new SubModel();
        $subModel -> setName('SubModel : Hello World!');
        $subModel -> setAddress('');

        $this -> subModel = $subModel;
    }

    function setName($par) {
        $this -> name = $par;
    }

    function getName() {
        return $this -> name;
    }

    function setAddress($par) {
        $this -> address = $par;
    }

    function getAddress() {
        return $this -> address;
    }

    function setSubModel($par) {
        $this -> subModel = $par;
    }

    function getSubModel() {
        return $this -> subModel;
    }

}

?>
<?php

/**
 * SubModel.php
 *
 * @author gio regarde <gioregarde@outlook.com>
 */
class SubModel {

    private $name;
    private $address;

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

}

?>
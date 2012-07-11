<?php

/**
 * Class for working with database.
 *
 * @author Alexey Korolev <koroleffas@gmail.com>
 */
class Table {

    private $name;
    private $colls = array();
    private $options = array();
    private $engine = 'InnoDB';
    private $comment = '';

    public function __construct() {
        
    }

}

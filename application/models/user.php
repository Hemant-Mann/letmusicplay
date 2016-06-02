<?php

/**
 * The User Model
 *
 * @author Hemant Mann
 */
namespace Models;
class User extends \Shared\MongoModel {

    /**
     * @column
     * @readwrite
     * @type text
     * @length 100
     * 
     * @validate required, alpha, min(3), max(32)
     * @label first name
     */
    protected $_name;

    /**
     * @column
     * @readwrite
     * @type text
     * @length 100
     * @uindex
     * 
     * @validate required, max(100)
     * @label email address
     */
    protected $_email;

    /**
     * @column
     * @readwrite
     * @type text
     * @length 100
     * @index
     * 
     * @validate required, alpha, min(8), max(32)
     * @label password
     */
    protected $_password;
    
    /**
    * @column
    * @readwrite
    * @type boolean
    */
    protected $_admin = false;

}

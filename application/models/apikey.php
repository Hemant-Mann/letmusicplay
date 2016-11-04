<?php

/**
 * The ApiKey
 *
 * @author Hemant Mann
 */
namespace Models;
class ApiKey extends \Shared\Model {

    /**
     * @column
     * @readwrite
     * @type integer
     * 
     * @validate required
     * @label User ID
     */
    protected $_user_id;

    /**
     * @column
     * @readwrite
     * @type text
     * @length 100
     * @uindex
     * 
     * @validate required, max(100)
     * @label API KEY
     */
    protected $_key;
    
    /**
    * @column
    * @readwrite
    * @type integer
    *
    * @value integer (0: infinite requests)
    */
    protected $_quota = 0;

}

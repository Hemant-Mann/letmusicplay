<?php

/**
 * The Downloads
 *
 * @author Hemant Mann
 */
namespace Models;
class Download extends \Shared\Model {

    /**
     * @column
     * @readwrite
     * @type text
     * @length 50
     * 
     * @validate required
     * @label Youtube ID
     */
    protected $_youtube_id;
    
    /**
    * @column
    * @readwrite
    * @type integer
    *
    * @value integer
    */
    protected $_count = 0;

}

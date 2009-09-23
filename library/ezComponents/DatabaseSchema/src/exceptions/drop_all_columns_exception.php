<?php
/**
 * File containing the ezcDbSchemaDropAllColumnsException class
 *
 * @package DatabaseSchema
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2008 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Exception that is thrown when trying to drop all columns in some table.
 *
 * @package DatabaseSchema
 * @version 1.4.2
 */
class ezcDbSchemaDropAllColumnsException extends ezcDbSchemaException
{
    /**
     * Constructs an ezcDbSchemaDropAllColumnsException 
     *
     * @param string $message reason of fail.
     */
    function __construct( $message )
    {
        parent::__construct( "Couldn't drop all columns in table. {$message}" );
    }
}
?>

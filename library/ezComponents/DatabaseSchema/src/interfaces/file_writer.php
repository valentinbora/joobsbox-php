<?php
/**
 * File containing the ezcDbSchemaFileWriter interface
 *
 * @package DatabaseSchema
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2008 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * This class provides the interface for file schema writers
 *
 * @package DatabaseSchema
 * @version 1.4.2
 */
interface ezcDbSchemaFileWriter extends ezcDbSchemaWriter
{
    /**
     * Saves the schema definition in $schema to the file $file.
     *
     * @param string      $file
     * @param ezcDbSchema $schema
     */
    public function saveToFile( $file, ezcDbSchema $schema );
}
?>

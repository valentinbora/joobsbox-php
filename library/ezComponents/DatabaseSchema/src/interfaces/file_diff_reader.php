<?php
/**
 * File containing the ezcDbSchemaFileDiffReader interface
 *
 * @package DatabaseSchema
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2008 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * This class provides the interface for file difference schema readers
 *
 * @package DatabaseSchema
 * @version 1.4.2
 */
interface ezcDbSchemaDiffFileReader extends ezcDbSchemaDiffReader
{
    /**
     * Returns an ezcDbSchemaDiff object created from the differences stored in the file $file
     *
     * @param string $file
     * @return ezcDbSchemaDiff
     */
    public function loadDiffFromFile( $file );
}
?>

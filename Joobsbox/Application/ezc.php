<?php
$db = ezcDbFactory::create(DDL_HERE!!);
$schema = ezcDbSchema::createFromDb( $db );
$schema->writeToFile( 'xml', APPLICATION_DIRECTORY . '/data/base.xml' );
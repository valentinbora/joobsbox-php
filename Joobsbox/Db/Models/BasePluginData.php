<?php

/**
 * BasePluginData
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $plugin_name
 * @property string $option_name
 * @property string $option_value
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 5925 2009-06-22 21:27:17Z jwage $
 */
abstract class BasePluginData extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('plugin_data');
        $this->hasColumn('id', 'integer', 4, array(
             'type' => 'integer',
             'unsigned' => '1',
             'primary' => true,
             'autoincrement' => true,
             'length' => '4',
             ));
        $this->hasColumn('plugin_name', 'string', 255, array(
             'type' => 'string',
             'fixed' => 0,
             'primary' => false,
             'default' => '',
             'notnull' => true,
             'autoincrement' => false,
             'length' => '255',
             ));
        $this->hasColumn('option_name', 'string', 255, array(
             'type' => 'string',
             'fixed' => 0,
             'primary' => false,
             'default' => '',
             'notnull' => true,
             'autoincrement' => false,
             'length' => '255',
             ));
        $this->hasColumn('option_value', 'string', 4096, array(
             'type' => 'string',
             'fixed' => 0,
             'primary' => false,
             'default' => '',
             'notnull' => true,
             'autoincrement' => false,
             'length' => '4096',
             ));
    }

    public function setUp()
    {
        parent::setUp();
    
    }
}
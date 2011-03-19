<?php

/**
 * sfGuardGroup: create table
 */
class PluginMigration_sfGuardGroup_CreateTable extends Doctrine_Migration_Base
{
    /**
     * Migrate
     */
    public function migrate($upDown)
    {
        $this->table($upDown, 'sf_guard_group',
            array(
                'id' => array(
                    'type' => 'integer',
                    'length' => 4,
                    'autoincrement' => true,
                    'primary' => true,
                ),
                'name' => array(
                    'type' => 'string',
                    'unique' => true,
                    'length' => 255,
                ),
                'description' => array(
                    'type' => 'string',
                    'length' => 255,
                ),
                'created_at' => array(
                    'notnull' => true,
                    'type' => 'timestamp',
                    'length' => 25,
                ),
                'updated_at' => array(
                    'notnull' => true,
                    'type' => 'timestamp',
                    'length' => 25,
                ),
            ),
            array(
                'type'    => 'INNODB',
                'collate' => 'utf8_general_ci',
                'charset' => 'utf8',
            )
        );

    }
}

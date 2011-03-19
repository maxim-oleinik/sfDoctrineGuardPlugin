<?php

/**
 * sfGuardUserGroup: create table
 */
class PluginMigration_sfGuardUserGroup_CreateTable extends Doctrine_Migration_Base
{
    /**
     * Migrate
     */
    public function migrate($upDown)
    {
        $this->table($upDown, 'sf_guard_user_group',
            array(
                'user_id' => array(
                    'type' => 'integer',
                    'primary' => true,
                    'length' => 4,
                ),
                'group_id' => array(
                    'type' => 'integer',
                    'primary' => true,
                    'length' => 4,
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
                'indexes' => array(
                    'group' => array('fields' => array('group_id')),
                ),
            )
        );


        // FK
        if ('up' == $upDown) {
            $this->foreignKey($upDown, 'sf_guard_user_group', 'sfGuardUserGroup_VS_sfGuardUser', array(
                'local' => 'user_id',
                'foreign' => 'id',
                'foreignTable' => 'sf_guard_user',
                'onUpdate' => NULL,
                'onDelete' => 'CASCADE',
            ));
            $this->foreignKey($upDown, 'sf_guard_user_group', 'sfGuardUserGroup_VS_sfGuardGroup', array(
                'local' => 'group_id',
                'foreign' => 'id',
                'foreignTable' => 'sf_guard_group',
                'onUpdate' => NULL,
                'onDelete' => 'CASCADE',
            ));
        }
    }
}

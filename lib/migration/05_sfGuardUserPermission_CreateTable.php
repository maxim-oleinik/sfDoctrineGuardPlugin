<?php

/**
 * sfGuardUserPermission: create table
 */
class PluginMigration_sfGuardUserPermission_CreateTable extends Doctrine_Migration_Base
{
    /**
     * Migrate
     */
    public function migrate($upDown)
    {
        $this->table($upDown, 'sf_guard_user_permission',
            array(
                'user_id' => array(
                    'type' => 'integer',
                    'primary' => true,
                    'length' => 4,
                ),
                'permission_id' => array(
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
                    'permission' => array('fields' => array('permission_id')),
                ),
            )
        );


        // FK
        if ('up' == $upDown) {
            $this->foreignKey($upDown, 'sf_guard_user_permission', 'sfGuardUserPermission_VS_sfGuardUser', array(
                'local' => 'user_id',
                'foreign' => 'id',
                'foreignTable' => 'sf_guard_user',
                'onUpdate' => NULL,
                'onDelete' => 'CASCADE',
            ));
            $this->foreignKey($upDown, 'sf_guard_user_permission', 'sfGuardUserPermission_VS_sfGuardPermission', array(
                'local' => 'permission_id',
                'foreign' => 'id',
                'foreignTable' => 'sf_guard_permission',
                'onUpdate' => NULL,
                'onDelete' => 'CASCADE',
            ));
        }
    }

}

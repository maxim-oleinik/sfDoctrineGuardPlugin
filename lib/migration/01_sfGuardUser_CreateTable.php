<?php

/**
 * sfGuardUser: create table
 */
class PluginMigration_sfGuardUser_CreateTable extends Doctrine_Migration_Base
{
    /**
     * Migrate
     */
    public function migrate($upDown)
    {
        $this->table($upDown, 'sf_guard_user',
            $fields = array(
                'id' => array(
                    'type'          => 'integer',
                    'length'        => 4,
                    'autoincrement' => true,
                    'primary'       => true,
                ),
                'first_name' => array(
                    'type'     => 'string',
                    'length'   => 255,
                ),
                'last_name' => array(
                    'type'     => 'string',
                    'length'   => 255,
                ),
                'email_address' => array(
                    'type'     => 'string',
                    'length'   => 255,
                    'notnull'  => true,
                    'unique'   => true,
                ),
                'username' => array(
                    'type'     => 'string',
                    'length'   => 255,
                    'notnull'  => true,
                    'unique'   => true,
                ),
                'algorithm' => array(
                    'type'     => 'string',
                    'length'   => 128,
                    'notnull'  => true,
                    'default'  => 'sha1',
                ),
                'salt' => array(
                    'type'     => 'string',
                    'length'   => 128,
                ),
                'password' => array(
                    'type'     => 'string',
                    'length'   => 128,
                ),
                'is_active' => array(
                    'type'     => 'integer',
                    'length'   => 1,
                    'default'  => 1,
                    'notnull'  => true,
                ),
                'is_super_admin' => array(
                    'type'     => 'integer',
                    'length'   => 1,
                    'default'  => 0,
                    'notnull'  => true,
                ),
                'last_login' => array(
                    'type'   => 'timestamp',
                    'length' => '25',
                    'notnull'  => false,
                    'default'  => null,
                ),
                'created_at' => array(
                    'type'   => 'timestamp',
                    'length' => '25',
                    'notnull'  => true,
                ),
                'updated_at' => array(
                    'type'   => 'timestamp',
                    'length' => '25',
                    'notnull'  => true,
                ),
            ),
            $options = array(
                'type'    => 'INNODB',
                'charset' => 'utf8',
                'indexes' => array(
                    'is_active' => array('fields' => array('is_active')),
                ),
            )
        );
    }

}

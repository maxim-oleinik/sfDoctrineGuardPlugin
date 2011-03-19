<?php

/**
 * sfGuardRememberKey: create table
 */
class PluginMigration_sfGuardRememberKey_CreateTable extends Doctrine_Migration_Base
{
    /**
     * Migrate
     */
    public function migrate($upDown)
    {
        $this->table($upDown, 'sf_guard_remember_key',
            $fields = array(
                'id' => array(
                    'type'          => 'integer',
                    'length'        => 4,
                    'autoincrement' => true,
                    'primary'       => true,
                ),
                'user_id' => array(
                    'type'          => 'integer',
                    'length'        => 4,
                    'notnull'       => true,
                ),
                'remember_key' => array(
                    'type'     => 'string',
                    'length'   => 32,
                    'notnull'  => true,
                ),
                'ip_address' => array(
                    'type'     => 'string',
                    'length'   => 50,
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
                    'user_id' => array('fields' => array('user_id')),
                ),
            )
        );


        if ('up' == $upDown) {
            $this->createForeignKey('sf_guard_remember_key', 'sfGuardRememberKey_VS_sfGuardUser', array(
                 'local'        => 'user_id',
                 'foreign'      => 'id',
                 'foreignTable' => 'sf_guard_user',
                 'onDelete'     => 'CASCADE',
            ));
        }
    }

}

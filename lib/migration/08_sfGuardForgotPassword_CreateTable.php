<?php

/**
 * sfGuardForgotPassword: create table
 */
class PluginMigration_sfGuardForgotPassword_CreateTable extends Doctrine_Migration_Base
{
    /**
     * Migrate
     */
    public function migrate($upDown)
    {
        $this->table($upDown, 'sf_guard_forgot_password',
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
                'unique_key' => array(
                    'type'     => 'string',
                    'length'   => 32,
                    'fixed'    => true,
                    'notnull'  => true,
                ),
                'created_at' => array(
                    'type'   => 'timestamp',
                    'length' => '25',
                    'notnull'  => true,
                ),
                'expires_at' => array(
                    'type'   => 'timestamp',
                    'length' => '25',
                    'notnull'  => true,
                ),
            ),
            $options = array(
                'type'    => 'INNODB',
                'charset' => 'utf8',
                'indexes' => array(
                    'user_id'    => array('fields' => array('user_id')),
                    'unique_key' => array('fields' => array('unique_key'), 'type' => 'unique'),
                ),
            )
        );


        if ('up' == $upDown) {
            $this->createForeignKey('sf_guard_forgot_password', 'sfGuardForgotPassword_VS_sfGuardUser', array(
                 'local'        => 'user_id',
                 'foreign'      => 'id',
                 'foreignTable' => 'sf_guard_user',
                 'onDelete'     => 'CASCADE',
            ));
        }
    }

}

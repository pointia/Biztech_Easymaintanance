<?php

    $installer = $this;

    $installer->startSetup();

    $installer->run("

        -- DROP TABLE IF EXISTS {$this->getTable('easysite_notify')};
        CREATE TABLE {$this->getTable('easysite_notify')} (
        `notification_id` int(11) unsigned NOT NULL auto_increment,
        `name` varchar(255) NOT NULL default '',
        `email` varchar(255) NOT NULL default '',
        PRIMARY KEY (`notification_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

        ");

    $installer->endSetup(); 
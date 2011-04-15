ALTER TABLE `personalizer_config` MODIFY `cookyExpires` varchar(20) NOT NULL default '30'
ALTER TABLE `personalizer_config` ADD `cron_id` varchar(255) NOT NULL
ALTER TABLE `personalizer_config` MODIFY `enable_roles` tinyint(4) NOT NULL
ALTER TABLE `personalizer_config` MODIFY `group_table` varchar(255) NOT NULL default 'group'
ALTER TABLE `personalizer_config` MODIFY `ldapswitch` tinyint(4) NOT NULL
ALTER TABLE `personalizer_config` MODIFY `masterPassword` varchar(32) NOT NULL
ALTER TABLE `personalizer_config` MODIFY `relations_table` varchar(255) NOT NULL default 'relations'
ALTER TABLE `personalizer_config` MODIFY `std_group_tpl_none` varchar(255) NOT NULL
ALTER TABLE `personalizer_config` MODIFY `std_group_tpl_own` varchar(100) NOT NULL
ALTER TABLE `personalizer_config` MODIFY `std_group_tpl_sub` varchar(255) NOT NULL
ALTER TABLE `personalizer_config` MODIFY `stdCutOuts` varchar(255) NOT NULL
ALTER TABLE `personalizer_config` MODIFY `stdUserFields` varchar(255) NOT NULL
ALTER TABLE `personalizer_config` MODIFY `user_table` varchar(255) NOT NULL default 'user'
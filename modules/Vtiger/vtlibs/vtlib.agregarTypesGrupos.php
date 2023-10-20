<?php

global $adb;
$sql = "ALTER TABLE `vtiger_groups`
	ADD COLUMN `type` VARCHAR(200) NULL AFTER `description`";
$adb->pquery($sql);
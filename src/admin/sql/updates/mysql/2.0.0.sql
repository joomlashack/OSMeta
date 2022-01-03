-- MySQL Workbench Synchronization
-- Generated: 2022-01-03 11:17
-- Model: OSMeta
-- Version: 2.0.0
-- Project: OSMeta
-- Author: Joomlashack

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;

ALTER TABLE `#__osmeta_metadata`
    ENGINE = InnoDB ,
    DROP INDEX `UniqueItemIdAndItemType` ,
    ADD UNIQUE INDEX `idx_itemid_itemtype` (`item_id` ASC, `item_type` ASC);


SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

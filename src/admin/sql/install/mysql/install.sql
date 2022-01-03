-- MySQL Script generated by MySQL Workbench
-- Mon Jan  3 11:16:20 2022
-- Model: OSMeta    Version: 2.0.0
-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;

-- -----------------------------------------------------
-- Schema osmeta
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Table `#__osmeta_metadata`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__osmeta_metadata` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `item_id` INT(11) NOT NULL,
  `item_type` INT(11) NOT NULL,
  `title` TEXT NOT NULL,
  `description` TEXT NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `idx_itemid_itemtype` (`item_id` ASC, `item_type` ASC))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
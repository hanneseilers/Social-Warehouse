SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

CREATE SCHEMA IF NOT EXISTS `Spendenliste` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
USE `Spendenliste` ;

-- -----------------------------------------------------
-- Table `Spendenliste`.`groups`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Spendenliste`.`groups` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` MEDIUMTEXT NOT NULL,
  `description` LONGTEXT NULL,
  `password` MEDIUMTEXT NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Spendenliste`.`categories`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Spendenliste`.`categories` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `parent` INT NULL,
  `group` INT NOT NULL,
  `name` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_categories_groups_idx` (`group` ASC),
  INDEX `fk_categories_categories1_idx` (`parent` ASC),
  CONSTRAINT `fk_categories_groups`
    FOREIGN KEY (`group`)
    REFERENCES `Spendenliste`.`groups` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_categories_categories1`
    FOREIGN KEY (`parent`)
    REFERENCES `Spendenliste`.`categories` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Spendenliste`.`palettes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Spendenliste`.`palettes` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `group` INT NOT NULL,
  `name` MEDIUMTEXT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_palettes_groups1_idx` (`group` ASC),
  CONSTRAINT `fk_palettes_groups1`
    FOREIGN KEY (`group`)
    REFERENCES `Spendenliste`.`groups` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Spendenliste`.`storages`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Spendenliste`.`storages` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `category` INT NOT NULL,
  `palette` INT NULL,
  `amount` INT NOT NULL,
  `estimated` TINYINT(1) NOT NULL DEFAULT False,
  PRIMARY KEY (`id`),
  INDEX `fk_storages_categories1_idx` (`category` ASC),
  INDEX `fk_storages_palettes1_idx` (`palette` ASC),
  CONSTRAINT `fk_storages_categories1`
    FOREIGN KEY (`category`)
    REFERENCES `Spendenliste`.`categories` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_storages_palettes1`
    FOREIGN KEY (`palette`)
    REFERENCES `Spendenliste`.`palettes` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

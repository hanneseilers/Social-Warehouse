SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

CREATE SCHEMA IF NOT EXISTS `Social-Warehouse` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
USE `Social-Warehouse` ;

-- -----------------------------------------------------
-- Table `Social-Warehouse`.`warehouses`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Social-Warehouse`.`warehouses` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` TINYTEXT NOT NULL,
  `description` LONGTEXT NULL,
  `country` TINYTEXT NOT NULL,
  `city` TINYTEXT NOT NULL,
  `password` MEDIUMTEXT NOT NULL,
  `parent` INT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_warehouses_warehouses1_idx` (`parent` ASC),
  CONSTRAINT `fk_warehouses_warehouses1`
    FOREIGN KEY (`parent`)
    REFERENCES `Social-Warehouse`.`warehouses` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Social-Warehouse`.`categories`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Social-Warehouse`.`categories` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `parent` INT NULL,
  `warehouse` INT NOT NULL,
  `name` VARCHAR(45) NOT NULL,
  `required` INT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_categories_groups_idx` (`warehouse` ASC),
  INDEX `fk_categories_categories1_idx` (`parent` ASC),
  CONSTRAINT `fk_categories_groups`
    FOREIGN KEY (`warehouse`)
    REFERENCES `Social-Warehouse`.`warehouses` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_categories_categories1`
    FOREIGN KEY (`parent`)
    REFERENCES `Social-Warehouse`.`categories` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Social-Warehouse`.`palettes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Social-Warehouse`.`palettes` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `warehouse` INT NOT NULL,
  `name` MEDIUMTEXT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_palettes_groups1_idx` (`warehouse` ASC),
  CONSTRAINT `fk_palettes_groups1`
    FOREIGN KEY (`warehouse`)
    REFERENCES `Social-Warehouse`.`warehouses` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Social-Warehouse`.`storages`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Social-Warehouse`.`storages` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `category` INT NOT NULL,
  `palette` INT NULL,
  `in` INT NOT NULL,
  `out` BIGINT NOT NULL,
  `estimated` TINYINT(1) NOT NULL,
  `male` TINYINT(1) NULL,
  `female` TINYINT(1) NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_storages_categories1_idx` (`category` ASC),
  INDEX `fk_storages_palettes1_idx` (`palette` ASC),
  CONSTRAINT `fk_storages_categories1`
    FOREIGN KEY (`category`)
    REFERENCES `Social-Warehouse`.`categories` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_storages_palettes1`
    FOREIGN KEY (`palette`)
    REFERENCES `Social-Warehouse`.`palettes` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

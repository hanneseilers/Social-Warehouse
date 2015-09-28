SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Table `sw_warehouses`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sw_warehouses` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` TINYTEXT NOT NULL,
  `description` LONGTEXT NULL,
  `country` TINYTEXT NOT NULL,
  `city` TINYTEXT NOT NULL,
  `password` MEDIUMTEXT NOT NULL,
  `passwordRestricted` MEDIUMTEXT NULL,
  `mail` TEXT NOT NULL,
  `disableLocationLess` TINYINT(1) NOT NULL DEFAULT 0,
  `disablePlatetteLess` TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `sw_categories`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sw_categories` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `parent` INT NULL,
  `warehouse` INT NOT NULL,
  `name` VARCHAR(45) NOT NULL,
  `required` INT NULL,
  `carton` VARCHAR(8) NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_categories_groups_idx` (`warehouse` ASC),
  INDEX `fk_categories_categories1_idx` (`parent` ASC),
  CONSTRAINT `fk_categories_groups`
    FOREIGN KEY (`warehouse`)
    REFERENCES `sw_warehouses` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_categories_categories1`
    FOREIGN KEY (`parent`)
    REFERENCES `sw_categories` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `sw_palettes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sw_palettes` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `warehouse` INT NOT NULL,
  `name` MEDIUMTEXT NOT NULL,
  `cleared` TINYINT(1) NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  INDEX `fk_palettes_groups1_idx` (`warehouse` ASC),
  CONSTRAINT `fk_palettes_groups1`
    FOREIGN KEY (`warehouse`)
    REFERENCES `sw_warehouses` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `sw_locations`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sw_locations` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `warehouse` INT NOT NULL,
  `name` TINYTEXT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_locations_warehouses1_idx` (`warehouse` ASC),
  CONSTRAINT `fk_locations_warehouses1`
    FOREIGN KEY (`warehouse`)
    REFERENCES `sw_warehouses` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `sw_storages`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sw_storages` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `category` INT NOT NULL,
  `warehouse` INT NOT NULL DEFAULT 0,
  `location` INT NULL,
  `palette` INT NULL,
  `income` BIGINT NOT NULL,
  `outgo` BIGINT NOT NULL,
  `male` TINYINT(1) NULL,
  `female` TINYINT(1) NULL,
  `baby` VARCHAR(45) NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_storages_categories1_idx` (`category` ASC),
  INDEX `fk_storages_palettes1_idx` (`palette` ASC),
  INDEX `fk_storages_locations1_idx` (`location` ASC),
  INDEX `fk_sw_storages_sw_warehouses1_idx` (`warehouse` ASC),
  CONSTRAINT `fk_storages_categories1`
    FOREIGN KEY (`category`)
    REFERENCES `sw_categories` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_storages_palettes1`
    FOREIGN KEY (`palette`)
    REFERENCES `sw_palettes` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_storages_locations1`
    FOREIGN KEY (`location`)
    REFERENCES `sw_locations` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_sw_storages_sw_warehouses1`
    FOREIGN KEY (`warehouse`)
    REFERENCES `sw_warehouses` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

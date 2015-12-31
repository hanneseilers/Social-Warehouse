SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';


-- -----------------------------------------------------
-- Table `sw_warehouses`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sw_warehouses` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` TINYTEXT NOT NULL,
  `description` TEXT NULL,
  `country` TINYTEXT NOT NULL,
  `city` TINYTEXT NOT NULL,
  `password` TINYTEXT NOT NULL,
  `passwordRestricted` TINYTEXT NULL,
  `mail` TEXT NOT NULL,
  `disableLocationLess` TINYINT(1) NOT NULL DEFAULT 0,
  `disablePaletteLess` TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `sw_categories`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sw_categories` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `parent` INT NULL,
  `warehouse` INT NOT NULL,
  `name` TINYTEXT NOT NULL,
  `demand` INT NULL,
  `male` TINYINT(1) NULL DEFAULT 0,
  `female` TINYINT(1) NULL DEFAULT 0,
  `children` TINYINT(1) NULL DEFAULT 0,
  `baby` TINYINT(1) NULL DEFAULT 0,
  `summer` TINYINT(1) NULL DEFAULT 0,
  `winter` TINYINT(1) NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  INDEX `fk_categories_groups_idx` (`warehouse` ASC),
  INDEX `fk_categories_categories1_idx` (`parent` ASC),
  CONSTRAINT `fk_categories_groups`
    FOREIGN KEY (`warehouse`)
    REFERENCES `sw_warehouses` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_categories_categories1`
    FOREIGN KEY (`parent`)
    REFERENCES `sw_categories` (`id`)
    ON DELETE CASCADE
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
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `sw_palettes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sw_palettes` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `number` INT NOT NULL,
  `warehouse` INT NOT NULL,
  `location` INT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_palettes_groups1_idx` (`warehouse` ASC),
  INDEX `fk_sw_palettes_sw_locations1_idx` (`location` ASC),
  CONSTRAINT `fk_palettes_groups1`
    FOREIGN KEY (`warehouse`)
    REFERENCES `sw_warehouses` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_sw_palettes_sw_locations1`
    FOREIGN KEY (`location`)
    REFERENCES `sw_locations` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `sw_cartons`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sw_cartons` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `warehouse` INT NOT NULL DEFAULT 0,
  `palette` INT NULL,
  `location` INT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_sw_storages_sw_warehouses1_idx` (`warehouse` ASC),
  INDEX `fk_sw_cartons_sw_palettes1_idx` (`palette` ASC),
  INDEX `fk_sw_cartons_sw_locations1_idx` (`location` ASC),
  CONSTRAINT `fk_sw_storages_sw_warehouses1`
    FOREIGN KEY (`warehouse`)
    REFERENCES `sw_warehouses` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_sw_cartons_sw_palettes1`
    FOREIGN KEY (`palette`)
    REFERENCES `sw_palettes` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_sw_cartons_sw_locations1`
    FOREIGN KEY (`location`)
    REFERENCES `sw_locations` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `sw_sessions`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sw_sessions` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `warehouse` INT NOT NULL,
  `restricted` TINYINT(1) NOT NULL DEFAULT 1,
  `lastUpdate` TIMESTAMP NOT NULL DEFAULT NOW(),
  PRIMARY KEY (`id`),
  INDEX `fk_Sessions_sw_warehouses1_idx` (`warehouse` ASC),
  CONSTRAINT `fk_Sessions_sw_warehouses1`
    FOREIGN KEY (`warehouse`)
    REFERENCES `sw_warehouses` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `sw_stock`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sw_stock` (
  `category` INT NOT NULL,
  `carton` INT NOT NULL,
  `male` TINYINT(1) NOT NULL DEFAULT 0,
  `female` TINYINT(1) NOT NULL DEFAULT 0,
  `children` TINYINT(1) NOT NULL DEFAULT 0,
  `baby` TINYINT(1) NOT NULL DEFAULT 0,
  `summer` TINYINT(1) NOT NULL DEFAULT 0,
  `winter` TINYINT(1) NOT NULL DEFAULT 0,
  `income` INT NOT NULL DEFAULT 0,
  `outgo` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`category`, `carton`, `male`, `female`, `children`, `baby`, `summer`, `winter`),
  INDEX `fk_sw_categories_has_sw_cartons_sw_cartons1_idx` (`carton` ASC),
  INDEX `fk_sw_categories_has_sw_cartons_sw_categories1_idx` (`category` ASC),
  CONSTRAINT `fk_sw_categories_has_sw_cartons_sw_categories1`
    FOREIGN KEY (`category`)
    REFERENCES `sw_categories` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_sw_categories_has_sw_cartons_sw_cartons1`
    FOREIGN KEY (`carton`)
    REFERENCES `sw_cartons` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

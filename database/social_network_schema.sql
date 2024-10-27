-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema social_network
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema social_network
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `social_network` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci ;
USE `social_network` ;

-- -----------------------------------------------------
-- Table `social_network`.`users`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `social_network`.`users` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(45) NOT NULL,
  `email` VARCHAR(45) NOT NULL,
  `password` CHAR(60) NOT NULL,
  `description` LONGTEXT NULL DEFAULT NULL,
  `createDate` DATE NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `email_UNIQUE` (`email` ASC) INVISIBLE,
  UNIQUE INDEX `username_UNIQUE` (`username` ASC) VISIBLE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `social_network`.`publications`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `social_network`.`publications` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `userId` INT NOT NULL,
  `text` LONGTEXT NOT NULL,
  `createDate` DATE NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_publications_users_idx` (`userId` ASC) VISIBLE,
  CONSTRAINT `fk_publications_users`
    FOREIGN KEY (`userId`)
    REFERENCES `social_network`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `social_network`.`follows`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `social_network`.`follows` (
  `users_id` INT NOT NULL,
  `userToFollowId` INT NOT NULL,
  PRIMARY KEY (`users_id`, `userToFollowId`),
  INDEX `fk_users_has_users_users2_idx` (`userToFollowId` ASC) VISIBLE,
  INDEX `fk_users_has_users_users1_idx` (`users_id` ASC) VISIBLE,
  CONSTRAINT `fk_users_has_users_users1`
    FOREIGN KEY (`users_id`)
    REFERENCES `social_network`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_users_has_users_users2`
    FOREIGN KEY (`userToFollowId`)
    REFERENCES `social_network`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

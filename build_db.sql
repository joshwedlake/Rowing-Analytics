-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema rowing
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema rowing
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `rowing` DEFAULT CHARACTER SET utf8 ;
USE `rowing` ;

-- -----------------------------------------------------
-- Table `rowing`.`season`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `rowing`.`season` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `description` VARCHAR(45) NULL,
  `date_begins` DATE NULL,
  `date_agegroup` DATE NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `rowing`.`rower`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `rowing`.`rower` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name_last` VARCHAR(45) NULL,
  `name_first` VARCHAR(45) NULL,
  `date_birth` DATE NULL,
  `schoolyear_offset` INT NULL,
  `season_joined_id` INT NULL,
  `season_novice_id` INT NULL,
  `is_active` TINYINT NULL,
  PRIMARY KEY (`id`),
  INDEX `s_idx` (`season_joined_id` ASC),
  INDEX `fk_rower_season_novice_idx` (`season_novice_id` ASC),
  CONSTRAINT `fk_rower_season_joined`
    FOREIGN KEY (`season_joined_id`)
    REFERENCES `rowing`.`season` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_rower_season_novice`
    FOREIGN KEY (`season_novice_id`)
    REFERENCES `rowing`.`season` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `rowing`.`coach`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `rowing`.`coach` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name_last` VARCHAR(45) NULL,
  `name_first` VARCHAR(45) NULL,
  `is_active` TINYINT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `rowing`.`sporttype`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `rowing`.`sporttype` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `display_index` INT NULL,
  `description` VARCHAR(45) NULL,
  `is_active` TINYINT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `rowing`.`activitytype`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `rowing`.`activitytype` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `display_index` INT NULL,
  `description` VARCHAR(45) NULL,
  `sporttype_id` INT NULL,
  `is_active` TINYINT NULL,
  `uses_boats` TINYINT NULL,
  `uses_oars` TINYINT NULL,
  `uses_ergs` TINYINT NULL,
  PRIMARY KEY (`id`),
  INDEX `_idx` (`sporttype_id` ASC),
  CONSTRAINT `fk_activitytype_sporttype`
    FOREIGN KEY (`sporttype_id`)
    REFERENCES `rowing`.`sporttype` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `rowing`.`location`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `rowing`.`location` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `display_index` INT NULL,
  `description` VARCHAR(45) NULL,
  `is_active` TINYINT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `rowing`.`boat`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `rowing`.`boat` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `is_active` TINYINT NULL,
  `description` VARCHAR(45) NULL,
  `description_name` VARCHAR(45) NULL,
  `description_manufacturer` VARCHAR(45) NULL,
  `description_model` VARCHAR(45) NULL,
  `is_erg_type` TINYINT NULL,
  `is_sweep_scull` BIT(2) NULL,
  `weight_kg_min` INT NULL,
  `weight_kg_max` INT NULL,
  `year_manufacture` INT NULL,
  `seats_count` TINYINT NULL,
  `is_coxed` TINYINT NULL,
  `quantity_count` INT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `rowing`.`boatrig`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `rowing`.`boatrig` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `description` VARCHAR(45) NULL,
  `seats_count` TINYINT NULL,
  `is_sweep_side` BIT(8) NULL,
  `footsteer_seat_index` TINYINT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `rowing`.`riggedboat`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `rowing`.`riggedboat` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `boat_id` INT NULL,
  `boatrig_id` INT NULL,
  `date_rigged` DATETIME NULL,
  INDEX `boattype_idx` (`boatrig_id` ASC),
  INDEX `boat_idx` (`boat_id` ASC),
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_riggedboat_boatrig`
    FOREIGN KEY (`boatrig_id`)
    REFERENCES `rowing`.`boatrig` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_riggedboat_boat`
    FOREIGN KEY (`boat_id`)
    REFERENCES `rowing`.`boat` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `rowing`.`session`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `rowing`.`session` (
  `id` INT NOT NULL,
  `datetime_start` DATETIME NULL,
  `datetime_end` DATETIME NULL,
  `location_id` INT NULL,
  `description` VARCHAR(45) NULL,
  PRIMARY KEY (`id`),
  INDEX `location_idx` (`location_id` ASC),
  CONSTRAINT `fk_session_location`
    FOREIGN KEY (`location_id`)
    REFERENCES `rowing`.`location` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `rowing`.`workgroup`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `rowing`.`workgroup` (
  `id` INT NOT NULL,
  `index` INT NULL,
  `repeat_count` INT NULL,
  `repeat_rest_seconds` DOUBLE NULL,
  `has_parent_workgroup_id` INT NULL,
  PRIMARY KEY (`id`),
  INDEX `parent_workgroup_idx` (`has_parent_workgroup_id` ASC),
  CONSTRAINT `fk_workgroup_workgroup`
    FOREIGN KEY (`has_parent_workgroup_id`)
    REFERENCES `rowing`.`workgroup` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `rowing`.`commonsessionplan`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `rowing`.`commonsessionplan` (
  `id` INT NOT NULL,
  `description` VARCHAR(45) NULL,
  `master_workgroup_id` INT NULL,
  `primary_total_measure` INT NULL,
  `total_distance_metres` DOUBLE NULL,
  `total_time_seconds` DOUBLE NULL,
  `total_trimp` DOUBLE NULL,
  `total_tss` DOUBLE NULL,
  PRIMARY KEY (`id`),
  INDEX `master_workgroup_idx` (`master_workgroup_id` ASC),
  CONSTRAINT `fk_commonsessionplan_workgroup`
    FOREIGN KEY (`master_workgroup_id`)
    REFERENCES `rowing`.`workgroup` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `rowing`.`sessionplan`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `rowing`.`sessionplan` (
  `id` INT NOT NULL,
  `session_id` INT NULL,
  `commonsessionplan_id` INT NULL,
  PRIMARY KEY (`id`),
  INDEX `session_idx` (`session_id` ASC),
  INDEX `work_idx` (`commonsessionplan_id` ASC),
  CONSTRAINT `fk_sessionplan_session`
    FOREIGN KEY (`session_id`)
    REFERENCES `rowing`.`session` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_sessionplan_commonsessionplan`
    FOREIGN KEY (`commonsessionplan_id`)
    REFERENCES `rowing`.`commonsessionplan` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `rowing`.`commoncrew`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `rowing`.`commoncrew` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `description` VARCHAR(45) NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `rowing`.`class`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `rowing`.`class` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `description_short` VARCHAR(45) NULL,
  `description_full` VARCHAR(45) NULL,
  `age_years` INT NULL,
  `is_over_age` TINYINT NULL,
  `competition_grade_level` INT NULL,
  `lightweight_individual_kg` DOUBLE NULL,
  `lightweight_crew_average_kg` DOUBLE NULL,
  PRIMARY KEY (`id`),
  INDEX `grading_idx` (`competition_grade_level` ASC, `is_over_age` ASC, `age_years` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `rowing`.`prognostic`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `rowing`.`prognostic` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `record_speed_m_s` DOUBLE NULL,
  `is_erg_type` TINYINT NULL COMMENT '0 false\n1 raw\n2 weight adjusted',
  `is_sweep` TINYINT NULL,
  `is_coxed` TINYINT NULL,
  `gender` BIT NULL,
  `seat_count` INT NULL,
  `class_id` INT NULL,
  PRIMARY KEY (`id`),
  INDEX `class_idx` (`class_id` ASC),
  CONSTRAINT `fk_prognostic_class`
    FOREIGN KEY (`class_id`)
    REFERENCES `rowing`.`class` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `rowing`.`oar`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `rowing`.`oar` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `is_active` TINYINT NULL,
  `display_index` INT NULL,
  `description_name` VARCHAR(45) NULL,
  `description_comment` VARCHAR(45) NULL,
  `description_manufacturer` VARCHAR(45) NULL,
  `description_style` VARCHAR(45) NULL,
  `year_manufacture` INT NULL,
  `seats_count` TINYINT NULL,
  `is_sweep` TINYINT NULL,
  `is_hatchet` TINYINT NULL,
  `is_fat` TINYINT NULL,
  `is_smoothie` TINYINT NULL,
  `is_vortex` TINYINT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `rowing`.`oarrig`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `rowing`.`oarrig` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `description` VARCHAR(45) NULL,
  `overall_cm` VARCHAR(45) NULL,
  `inboard_cm` VARCHAR(45) NULL,
  `display_index` INT NULL,
  `is_active` TINYINT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `rowing`.`riggedoar`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `rowing`.`riggedoar` (
  `id` INT NOT NULL,
  `oar_id` INT NULL,
  `oarrig_id` INT NULL,
  `date_rigged` DATETIME NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_riggedoar_oar_idx` (`oar_id` ASC),
  INDEX `fk_riggedoar_oarrig_idx` (`oarrig_id` ASC),
  CONSTRAINT `fk_riggedoar_oar`
    FOREIGN KEY (`oar_id`)
    REFERENCES `rowing`.`oar` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_riggedoar_oarrig`
    FOREIGN KEY (`oarrig_id`)
    REFERENCES `rowing`.`oarrig` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `rowing`.`partial_session_totals`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `rowing`.`partial_session_totals` (
  `id` INT NOT NULL,
  `total_distance_metres` DOUBLE NULL,
  `total_time_seconds` DOUBLE NULL,
  `total_trimp` DOUBLE NULL,
  `total_tss` DOUBLE NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `rowing`.`crew`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `rowing`.`crew` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `sessionplan_id` INT NULL,
  `partial_session_totals_id` INT NULL,
  `riggedboat_id` INT NULL,
  `boat_index` INT NULL,
  `riggedoar_id` INT NULL,
  `commoncrew_id` INT NULL,
  `individual_id` INT NULL,
  `prognostic_primary_id` INT NULL,
  `prognostic_secondary_id` INT NULL,
  `result` DOUBLE NULL,
  `result_datum_group_index` INT NULL,
  PRIMARY KEY (`id`),
  INDEX `commoncrew_idx` (`commoncrew_id` ASC),
  INDEX `prognostic_idx` (`prognostic_primary_id` ASC),
  INDEX `sessionplan_idx` (`sessionplan_id` ASC),
  INDEX `riggedboat_idx` (`riggedboat_id` ASC),
  INDEX `fk_crew_prognostic_secondary_idx` (`prognostic_secondary_id` ASC),
  INDEX `fk_crew_partial_session_totals_idx` (`partial_session_totals_id` ASC),
  INDEX `fk_crew_rower_idx` (`individual_id` ASC),
  INDEX `fk_crew_riggedoar_idx` (`riggedoar_id` ASC),
  CONSTRAINT `fk_crew_sessionplan`
    FOREIGN KEY (`sessionplan_id`)
    REFERENCES `rowing`.`sessionplan` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_crew_commoncrew`
    FOREIGN KEY (`commoncrew_id`)
    REFERENCES `rowing`.`commoncrew` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_crew_prognostic_primary`
    FOREIGN KEY (`prognostic_primary_id`)
    REFERENCES `rowing`.`prognostic` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_crew_riggedboat`
    FOREIGN KEY (`riggedboat_id`)
    REFERENCES `rowing`.`riggedboat` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_crew_riggedoar`
    FOREIGN KEY (`riggedoar_id`)
    REFERENCES `rowing`.`riggedoar` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_crew_prognostic_secondary`
    FOREIGN KEY (`prognostic_secondary_id`)
    REFERENCES `rowing`.`prognostic` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_crew_partial_session_totals`
    FOREIGN KEY (`partial_session_totals_id`)
    REFERENCES `rowing`.`partial_session_totals` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_crew_rower`
    FOREIGN KEY (`individual_id`)
    REFERENCES `rowing`.`rower` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `rowing`.`commoncrew_rower_link`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `rowing`.`commoncrew_rower_link` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `commoncrew_id` INT NULL,
  `rower_id` INT NULL,
  `seat_index` INT NULL,
  PRIMARY KEY (`id`),
  INDEX `rower_idx` (`rower_id` ASC),
  INDEX `commoncrew_idx` (`commoncrew_id` ASC),
  CONSTRAINT `fk_commoncrew_rower_link_rower`
    FOREIGN KEY (`rower_id`)
    REFERENCES `rowing`.`rower` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_commoncrew_rower_link_commoncrew`
    FOREIGN KEY (`commoncrew_id`)
    REFERENCES `rowing`.`commoncrew` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `rowing`.`zone`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `rowing`.`zone` (
  `id` INT NOT NULL,
  `description_name` VARCHAR(45) NULL,
  `description_shortcode` VARCHAR(45) NULL,
  `description_full` VARCHAR(45) NULL,
  `cost_minute_trimp` DOUBLE NULL,
  `cost_minute_tss` DOUBLE NULL,
  `hr_begins` DOUBLE NULL,
  `percent_speed_begins` DOUBLE NULL,
  `percent_power_begins` DOUBLE NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `rowing`.`work`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `rowing`.`work` (
  `id` INT NOT NULL,
  `parent_workgroup_id` INT NULL,
  `index` INT NULL,
  `zone_id` INT NULL,
  `activitytype_id` INT NULL,
  `description` VARCHAR(45) NULL,
  `primary_duration_measure` INT NULL,
  `duration_time_seconds` DOUBLE NULL,
  `duration_distance_metres` DOUBLE NULL,
  `duration_strokes` INT NULL,
  `rating_average` INT NULL,
  `rating_lower` INT NULL,
  `rating_upper` INT NULL,
  `is_rest` TINYINT NULL,
  `is_excluded_total` TINYINT NULL,
  PRIMARY KEY (`id`),
  INDEX `parent_workgroup_idx` (`parent_workgroup_id` ASC),
  INDEX `activitytype_idx` (`activitytype_id` ASC),
  INDEX `zone_idx` (`zone_id` ASC),
  CONSTRAINT `fk_work_workgroup`
    FOREIGN KEY (`parent_workgroup_id`)
    REFERENCES `rowing`.`workgroup` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_work_activitytype`
    FOREIGN KEY (`activitytype_id`)
    REFERENCES `rowing`.`activitytype` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_work_zone`
    FOREIGN KEY (`zone_id`)
    REFERENCES `rowing`.`zone` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `rowing`.`worktype`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `rowing`.`worktype` (
  `id` INT NOT NULL,
  `description` VARCHAR(45) NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `rowing`.`work_worktype_link`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `rowing`.`work_worktype_link` (
  `work_id` INT NOT NULL,
  `worktype_id` INT NOT NULL,
  INDEX `work_idx` (`work_id` ASC),
  INDEX `worktype_idx` (`worktype_id` ASC),
  PRIMARY KEY (`work_id`, `worktype_id`),
  CONSTRAINT `fk_work_worktype_link_work`
    FOREIGN KEY (`work_id`)
    REFERENCES `rowing`.`work` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_work_worktype_link_worktype`
    FOREIGN KEY (`worktype_id`)
    REFERENCES `rowing`.`worktype` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `rowing`.`summary_commonsessionplan_worktype_link`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `rowing`.`summary_commonsessionplan_worktype_link` (
  `commonsessionplan_id` INT NOT NULL,
  `worktype_id` INT NOT NULL,
  INDEX `sessionplan_idx` (`commonsessionplan_id` ASC),
  INDEX `worktype_idx` (`worktype_id` ASC),
  PRIMARY KEY (`commonsessionplan_id`, `worktype_id`),
  CONSTRAINT `fk_summary_commonsessionplan`
    FOREIGN KEY (`commonsessionplan_id`)
    REFERENCES `rowing`.`commonsessionplan` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_summary_worktype`
    FOREIGN KEY (`worktype_id`)
    REFERENCES `rowing`.`worktype` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `rowing`.`abilitylevel`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `rowing`.`abilitylevel` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `display_index` INT NULL,
  `description` VARCHAR(45) NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `rowing`.`rowability`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `rowing`.`rowability` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `rower_id` INT NULL,
  `date_begins` DATE NULL,
  `cox_abilitylevel_id` INT NULL,
  `sweep_stroke_abilitylevel_id` INT NULL,
  `sweep_bow_abilitylevel_id` INT NULL,
  `scull_abilitylevel_id` INT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_rowability_abilitylevel_cox_idx` (`cox_abilitylevel_id` ASC),
  INDEX `fk_rowability_abilitylevel_sweep_stroke_idx` (`sweep_stroke_abilitylevel_id` ASC),
  INDEX `fk_rowability_abilitylevel_sweep_bow_idx` (`sweep_bow_abilitylevel_id` ASC),
  INDEX `fk_rowability_abilitylevel_scull_idx` (`scull_abilitylevel_id` ASC),
  INDEX `fk_rowability_rower` (`rower_id` ASC),
  CONSTRAINT `fk_rowability_rower`
    FOREIGN KEY (`rower_id`)
    REFERENCES `rowing`.`rower` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_rowability_abilitylevel_cox`
    FOREIGN KEY (`cox_abilitylevel_id`)
    REFERENCES `rowing`.`abilitylevel` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_rowability_abilitylevel_stroke`
    FOREIGN KEY (`sweep_stroke_abilitylevel_id`)
    REFERENCES `rowing`.`abilitylevel` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_rowability_abilitylevel_bow`
    FOREIGN KEY (`sweep_bow_abilitylevel_id`)
    REFERENCES `rowing`.`abilitylevel` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_rowability_abilitylevel_scull`
    FOREIGN KEY (`scull_abilitylevel_id`)
    REFERENCES `rowing`.`abilitylevel` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `rowing`.`crew_work_result`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `rowing`.`crew_work_result` (
  `id` INT NOT NULL,
  `crew_id` INT NULL,
  `work_id` INT NULL,
  `work_repetition_index` INT NULL,
  `result` DOUBLE NULL,
  `result_datum_group_index` INT NULL,
  PRIMARY KEY (`id`),
  INDEX `crew_idx` (`crew_id` ASC),
  INDEX `work_idx` (`work_id` ASC),
  CONSTRAINT `fk_crew_work_result_crew`
    FOREIGN KEY (`crew_id`)
    REFERENCES `rowing`.`crew` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_crew_work_result_work`
    FOREIGN KEY (`work_id`)
    REFERENCES `rowing`.`work` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `rowing`.`squad`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `rowing`.`squad` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `display_index` INT NULL,
  `description` VARCHAR(45) NULL,
  `is_active` TINYINT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `rowing`.`rower_squad_link`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `rowing`.`rower_squad_link` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `rower_id` INT NULL,
  `squad_id` INT NULL,
  `date_begins` DATE NULL,
  `date_ends` DATE NULL,
  INDEX `rower_idx` (`rower_id` ASC),
  INDEX `squad_idx` (`squad_id` ASC),
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_rower_squad_link_rower`
    FOREIGN KEY (`rower_id`)
    REFERENCES `rowing`.`rower` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_rower_squad_link_squad`
    FOREIGN KEY (`squad_id`)
    REFERENCES `rowing`.`squad` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `rowing`.`weight`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `rowing`.`weight` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `rower_id` INT NULL,
  `weight_kg` DOUBLE NULL,
  `date_weighed` DATE NULL,
  PRIMARY KEY (`id`),
  INDEX `rower_idx` (`rower_id` ASC),
  UNIQUE INDEX `rower_id_date_weighed_UNIQUE` (`rower_id` ASC, `date_weighed` ASC),
  CONSTRAINT `fk_weight_rower`
    FOREIGN KEY (`rower_id`)
    REFERENCES `rowing`.`rower` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `rowing`.`measurement`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `rowing`.`measurement` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `rower_id` INT NULL,
  `height_cm` DOUBLE NULL,
  `armspan_cm` DOUBLE NULL,
  `date_measured` DATE NULL,
  PRIMARY KEY (`id`),
  INDEX `rower_idx` (`rower_id` ASC),
  UNIQUE INDEX `rower_id_date_measured_UNIQUE` (`rower_id` ASC, `date_measured` ASC),
  CONSTRAINT `rower`
    FOREIGN KEY (`rower_id`)
    REFERENCES `rowing`.`rower` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `rowing`.`crew_coach_link`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `rowing`.`crew_coach_link` (
  `crew_id` INT NOT NULL,
  `coach_id` INT NOT NULL,
  INDEX `crew_idx` (`crew_id` ASC),
  INDEX `coach_idx` (`coach_id` ASC),
  PRIMARY KEY (`crew_id`, `coach_id`),
  CONSTRAINT `fk_crew_coach_link_crew`
    FOREIGN KEY (`crew_id`)
    REFERENCES `rowing`.`crew` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_crew_coach_link_coach`
    FOREIGN KEY (`coach_id`)
    REFERENCES `rowing`.`coach` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `rowing`.`coach_session_link`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `rowing`.`coach_session_link` (
  `coach_id` INT NOT NULL,
  `session_id` INT NOT NULL,
  INDEX `coach_idx` (`coach_id` ASC),
  INDEX `session_idx` (`session_id` ASC),
  PRIMARY KEY (`coach_id`, `session_id`),
  CONSTRAINT `fk_coach_session_link_coach`
    FOREIGN KEY (`coach_id`)
    REFERENCES `rowing`.`coach` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_coach_session_link_session`
    FOREIGN KEY (`session_id`)
    REFERENCES `rowing`.`session` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `rowing`.`squad_session_link`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `rowing`.`squad_session_link` (
  `squad_id` INT NOT NULL,
  `session_id` INT NOT NULL,
  INDEX `squad_idx` (`squad_id` ASC),
  INDEX `session_idx` (`session_id` ASC),
  PRIMARY KEY (`squad_id`, `session_id`),
  CONSTRAINT `fk_squad_session_link_squad`
    FOREIGN KEY (`squad_id`)
    REFERENCES `rowing`.`squad` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_squad_session_link_session`
    FOREIGN KEY (`session_id`)
    REFERENCES `rowing`.`session` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `rowing`.`absence`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `rowing`.`absence` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `rower_id` INT NULL,
  `session_id` INT NULL,
  `is_excused` TINYINT NULL,
  `is_noshow` TINYINT NULL,
  `description` VARCHAR(45) NULL,
  PRIMARY KEY (`id`),
  INDEX `rower_idx` (`rower_id` ASC),
  INDEX `session_idx` (`session_id` ASC),
  CONSTRAINT `fk_absence_session`
    FOREIGN KEY (`session_id`)
    REFERENCES `rowing`.`session` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_absence_rower`
    FOREIGN KEY (`rower_id`)
    REFERENCES `rowing`.`rower` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `rowing`.`injury`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `rowing`.`injury` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `rower_id` INT NULL,
  `description` VARCHAR(45) NULL,
  `date_begin` DATETIME NULL,
  `date_end` DATETIME NULL,
  PRIMARY KEY (`id`),
  INDEX `rower_idx` (`rower_id` ASC),
  CONSTRAINT `fk_injury_rower`
    FOREIGN KEY (`rower_id`)
    REFERENCES `rowing`.`rower` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `rowing`.`injury_absence_link`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `rowing`.`injury_absence_link` (
  `injury_id` INT NOT NULL,
  `absence_id` INT NOT NULL,
  PRIMARY KEY (`injury_id`, `absence_id`),
  INDEX `fk_injury_has_absence_absence1_idx` (`absence_id` ASC),
  INDEX `fk_injury_has_absence_injury1_idx` (`injury_id` ASC),
  CONSTRAINT `fk_injury_absence_link_injury`
    FOREIGN KEY (`injury_id`)
    REFERENCES `rowing`.`injury` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_injury_absence_link_absence`
    FOREIGN KEY (`absence_id`)
    REFERENCES `rowing`.`absence` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `rowing`.`injuryseverity`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `rowing`.`injuryseverity` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `display_index` INT NULL,
  `description` VARCHAR(45) NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `rowing`.`injury_injuryseverity_link`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `rowing`.`injury_injuryseverity_link` (
  `injury_id` INT NOT NULL,
  `injuryseverity_id` INT NOT NULL,
  PRIMARY KEY (`injury_id`, `injuryseverity_id`),
  INDEX `fk_injury_has_injuryseverity_injuryseverity1_idx` (`injuryseverity_id` ASC),
  INDEX `fk_injury_has_injuryseverity_injury1_idx` (`injury_id` ASC),
  CONSTRAINT `fk_injury_injuryseverity_link_injury`
    FOREIGN KEY (`injury_id`)
    REFERENCES `rowing`.`injury` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_injury_injuryseverity_link_injuryseverity`
    FOREIGN KEY (`injuryseverity_id`)
    REFERENCES `rowing`.`injuryseverity` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `rowing`.`config`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `rowing`.`config` (
  `id` INT NOT NULL,
  `current_season_id` INT NULL,
  `day_schoolyear_begins` TINYINT NULL,
  `month_schoolyear_begins` TINYINT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_config_season_idx` (`current_season_id` ASC),
  CONSTRAINT `fk_config_season`
    FOREIGN KEY (`current_season_id`)
    REFERENCES `rowing`.`season` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `rowing`.`boatrig_seat`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `rowing`.`boatrig_seat` (
  `boatrig_id` INT NOT NULL,
  `seat_index` TINYINT NOT NULL,
  `span_spread_cm` DOUBLE NULL,
  `feet_through_work_cm` DOUBLE NULL,
  `height_deck_heel_cm` DOUBLE NULL,
  `height_deck_seat_cm` DOUBLE NULL,
  PRIMARY KEY (`boatrig_id`, `seat_index`),
  CONSTRAINT `fk_boatrig_seat_rig`
    FOREIGN KEY (`boatrig_id`)
    REFERENCES `rowing`.`boatrig` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `rowing`.`boatrig_seat_side`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `rowing`.`boatrig_seat_side` (
  `rig_id` INT NOT NULL,
  `seat_index` TINYINT NOT NULL,
  `side_index` TINYINT NOT NULL,
  `height_seat_gate_min_cm` DOUBLE NULL,
  `height_seat_gate_max_cm` DOUBLE NULL,
  `pitch_stern_degrees` DOUBLE NULL,
  `pitch_out_degrees` DOUBLE NULL,
  `pitch_bushing_degrees` DOUBLE NULL,
  PRIMARY KEY (`rig_id`, `seat_index`, `side_index`),
  CONSTRAINT `fk_boatrig_seat_side_boatrig`
    FOREIGN KEY (`rig_id`)
    REFERENCES `rowing`.`boatrig` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `rowing`.`oarrig_seat`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `rowing`.`oarrig_seat` (
  `oarrig_id` INT NOT NULL,
  `seat_index` TINYINT NOT NULL,
  `overall_cm` DOUBLE NULL,
  `inboard_cm` DOUBLE NULL,
  PRIMARY KEY (`oarrig_id`, `seat_index`),
  CONSTRAINT `fk_seat_oarrig`
    FOREIGN KEY (`oarrig_id`)
    REFERENCES `rowing`.`oarrig` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

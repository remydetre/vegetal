ALTER TABLE `%{db.var.prefix}paygreen_buttons`
  ADD `perCentPayment` INT NULL,
  ADD `subOption` INT DEFAULT 0,
  ADD `reductionPayment` VARCHAR(45) DEFAULT 'none',
  ADD `integration` INT NOT NULL DEFAULT 0
;

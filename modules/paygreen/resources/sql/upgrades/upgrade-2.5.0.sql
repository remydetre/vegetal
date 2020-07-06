ALTER TABLE `%{db.var.prefix}paygreen_transaction_locks`
  CHANGE `lockedAt` `locked_at` INT(11) UNSIGNED NOT NULL
;

ALTER TABLE `%{db.var.prefix}paygreen_transactions`
  CHANGE `id_cart` `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  CHANGE `pid` `pid` VARCHAR(50) NOT NULL,
  CHANGE `id_order` `id_order` INT(10) UNSIGNED NOT NULL,
  CHANGE `type` `mode` VARCHAR(50) NOT NULL,
  CHANGE `created_at` `created_at` INT(10) UNSIGNED NOT NULL,
  CHANGE `updated_at` `amount` INT(10) UNSIGNED NOT NULL,
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE (`pid`, `id_order`)
;

ALTER TABLE `%{db.var.prefix}paygreen_recurring_transaction`
  CHANGE `id` `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  CHANGE `rank` `rank` INT(10) UNSIGNED NOT NULL AFTER `state`,
  CHANGE `pid` `pid` VARCHAR(50) NOT NULL AFTER `id`,
  CHANGE `amount` `amount` INT(10) NOT NULL,
  CHANGE `state` `state` VARCHAR(50) NOT NULL AFTER `pid`,
  CHANGE `type` `mode` VARCHAR(50) NOT NULL,
  CHANGE `date_payment` `created_at` INT(10) UNSIGNED NOT NULL,
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`id`)
;

ALTER TABLE `%{db.var.prefix}paygreen_recurring_transaction`
  ADD `id_order` INT(10) UNSIGNED NOT NULL AFTER `pid`,
  ADD `state_order_before` VARCHAR(50) NOT NULL AFTER `state`,
  ADD `state_order_after` VARCHAR(50) NULL DEFAULT NULL AFTER `state_order_before`,
  ADD UNIQUE (`pid`, `id_order`),
  ADD UNIQUE (`rank`, `id_order`)
;


ALTER TABLE `%{db.var.prefix}paygreen_buttons`
  CHANGE `nbPayment` `paymentNumber` INT(5) UNSIGNED NOT NULL DEFAULT 1,
  CHANGE `perCentPayment` `firstPaymentPart` INT(5) UNSIGNED DEFAULT NULL,
  CHANGE `subOption` `orderRepeated` INT(1) UNSIGNED DEFAULT 0,
  CHANGE `reportPayment` `paymentReport` VARCHAR(15) DEFAULT NULL,
  CHANGE `image` `image` VARCHAR(250) DEFAULT NULL,
  CHANGE `integration` `integration` VARCHAR(10) NOT NULL DEFAULT 'EXTERNAL',
  CHANGE `displayType` `displayType` VARCHAR(10) NOT NULL DEFAULT 'DEFAULT',
  CHANGE `executedAt` `paymentMode` VARCHAR(10) NOT NULL DEFAULT 'CASH',
  CHANGE `reductionPayment` `discount` VARCHAR(45) DEFAULT NULL,
  DROP `defaultimg`;

# rewrite discount values
UPDATE `%{db.var.prefix}paygreen_buttons`
SET `discount` = NULL
WHERE `discount` = 'none'
   OR `discount` = '0'
   OR `discount` = '';

# rewrite paymentMode values
UPDATE `%{db.var.prefix}paygreen_buttons`
SET `paymentMode` = 'CASH'
WHERE `paymentMode` = '0';

UPDATE `%{db.var.prefix}paygreen_buttons`
SET `paymentMode` = 'RECURRING'
WHERE `paymentMode` = '3';

UPDATE `%{db.var.prefix}paygreen_buttons`
SET `paymentMode` = 'XTIME'
WHERE `paymentMode` = '1';

UPDATE `%{db.var.prefix}paygreen_buttons`
SET `paymentMode` = 'TOKENIZE'
WHERE `paymentMode` = '-1';

# rewrite displayType values
UPDATE `%{db.var.prefix}paygreen_buttons`
SET `displayType` = 'PICTURE'
WHERE `displayType` = '1';

UPDATE `%{db.var.prefix}paygreen_buttons`
SET `displayType` = 'TEXT'
WHERE `displayType` = '2';

UPDATE `%{db.var.prefix}paygreen_buttons`
SET `displayType` = 'DEFAULT'
WHERE `displayType` = '3';

UPDATE `%{db.var.prefix}paygreen_buttons`
SET `displayType` = 'HALF'
WHERE `displayType` = 'half';

UPDATE `%{db.var.prefix}paygreen_buttons`
SET `displayType` = 'BLOC'
WHERE `displayType` = 'bloc';

UPDATE `%{db.var.prefix}paygreen_buttons`
SET `displayType` = 'DEFAULT'
WHERE `displayType` = 'full';

# rewrite integration values
UPDATE `%{db.var.prefix}paygreen_buttons`
SET `integration` = 'EXTERNAL'
WHERE `integration` = '0';

UPDATE `%{db.var.prefix}paygreen_buttons`
SET `integration` = 'INSITE'
WHERE `integration` = '1';

UPDATE `%{db.var.prefix}paygreen_buttons` SET `discount` = '0' WHERE `discount` IS NULL;

ALTER TABLE `%{db.var.prefix}paygreen_buttons`
  CHANGE `discount` `discount` INT(11) UNSIGNED NOT NULL DEFAULT 0;

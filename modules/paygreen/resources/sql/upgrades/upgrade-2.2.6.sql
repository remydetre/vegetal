CREATE TABLE `%{db.var.prefix}paygreen_transaction_locks`
(
  `id`       int          NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `pid`      varchar(100) NOT NULL,
  `lockedAt` INT          NULL DEFAULT NULL
) ENGINE = `%{db.var.engine}`
  DEFAULT CHARSET = utf8
;

ALTER TABLE `%{db.var.prefix}paygreen_transaction_locks`
  ADD UNIQUE (`pid`)
;

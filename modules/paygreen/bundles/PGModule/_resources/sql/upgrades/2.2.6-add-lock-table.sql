CREATE TABLE `%{database.entities.lock.table}`
(
  `id`       int          NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `pid`      varchar(100) NOT NULL,
  `lockedAt` INT          NULL DEFAULT NULL
) ENGINE = `%{db.var.engine}`
  DEFAULT CHARSET = utf8
;

ALTER TABLE `%{database.entities.lock.table}`
  ADD UNIQUE (`pid`)
;

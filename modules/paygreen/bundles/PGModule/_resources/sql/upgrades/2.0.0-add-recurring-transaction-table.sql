CREATE TABLE IF NOT EXISTS `%{database.entities.recurring_transaction.table}`
(
  `id`           int(11)      NOT NULL,
  `rank`         int(11)      NOT NULL,
  `pid`          varchar(250) NOT NULL,
  `amount`       int(11)      NOT NULL,
  `state`        varchar(50)  NOT NULL,
  `type`         varchar(50)  NOT NULL,
  `date_payment` date         NOT NULL,
  CONSTRAINT `fk_rec_transac` FOREIGN KEY (`id`) REFERENCES `%{database.entities.transaction.table}` (`id_cart`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  PRIMARY KEY (`id`, `rank`)
) ENGINE = `%{db.var.engine}`
  DEFAULT CHARSET = utf8
;

ALTER TABLE `%{database.entities.button.table}`
  ADD `integration` INT NOT NULL DEFAULT 0
;

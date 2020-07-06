CREATE TABLE `%{database.entities.category_has_payment.table}`
(
  `id`          int(11)     NOT NULL AUTO_INCREMENT,
  `id_category` int(11)     NOT NULL,
  `payment`     varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE = `%{db.var.engine}`
  DEFAULT CHARSET = utf8
;

CREATE TABLE `%{db.var.prefix}paygreen_categories_has_payments`
(
  `id`          int(11)     NOT NULL AUTO_INCREMENT,
  `id_category` int(11)     NOT NULL,
  `payment`     varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE = `%{db.var.engine}`
  DEFAULT CHARSET = utf8
;

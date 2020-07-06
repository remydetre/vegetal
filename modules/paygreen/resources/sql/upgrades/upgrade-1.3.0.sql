CREATE TABLE IF NOT EXISTS `%{db.var.prefix}paygreen_transactions`
(
  `id_cart`  int(11)      NOT NULL,
  `pid`      varchar(250) NOT NULL,
  `id_order` int(11)      NOT NULL,
  `state`    varchar(50)  NOT NULL,
  `type`     varchar(50)  NOT NULL
) ENGINE = `%{db.var.engine}`
  DEFAULT CHARSET = latin1
;

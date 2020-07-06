ALTER TABLE `%{db.var.prefix}paygreen_buttons`
  CHANGE `id_shop` `id_shop` int(11) NOT NULL
;

ALTER TABLE `%{db.var.prefix}paygreen_categories_has_payments`
  CHANGE `id_shop` `id_shop` int(11) NOT NULL
;

ALTER TABLE `%{db.var.prefix}paygreen_buttons`
  ADD `id_shop` int(11) NULL DEFAULT NULL
;

ALTER TABLE `%{db.var.prefix}paygreen_categories_has_payments`
  ADD `id_shop` int(11) NULL DEFAULT NULL
;

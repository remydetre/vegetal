ALTER TABLE `%{database.entities.button.table}`
  CHANGE `id_shop` `id_shop` int(11) NOT NULL
;

ALTER TABLE `%{database.entities.category_has_payment.table}`
  CHANGE `id_shop` `id_shop` int(11) NOT NULL
;

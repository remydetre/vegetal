
CREATE TABLE `%{database.entities.setting.table}`
(
  `id`       int(11)      UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `id_shop`  int(11)      UNSIGNED NULL DEFAULT NULL,
  `name`     varchar(50)  NOT NULL,
  `value`    text         NOT NULL
) ENGINE = `%{db.var.engine}`
  DEFAULT CHARSET = latin1
;

DROP TABLE IF EXISTS `%{database.entities.fingerprint.table}`;

CREATE TABLE `%{database.entities.fingerprint.table}`
(
    `id`          int(11)      UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `session`     varchar(100) NOT NULL,
    `browser`     varchar(255) NOT NULL,
    `device`      varchar(255) NOT NULL,
    `pages`       int(6)       UNSIGNED NOT NULL,
    `pictures`    int(6)       UNSIGNED NOT NULL,
    `time`        int(11)      UNSIGNED NOT NULL,
    `created_at`  datetime     NOT NULL
) ENGINE = `%{db.var.engine}`
  DEFAULT CHARSET = `utf8`
;

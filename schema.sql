CREATE TABLE `bets`
(
  `id`         int(11) auto_increment,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `amount`     int(11)   NOT NULL,
  `users_id`   int(11)   NOT NULL,
  `lots_id`    int(11)   NOT NULL,
  PRIMARY KEY (ID)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

CREATE TABLE `categories`
(
  `id`          int(11) auto_increment,
  `name`        char(64) NOT NULL,
  `symbol_code` char(32) NOT NULL,
  PRIMARY KEY (ID)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

CREATE TABLE `lots`
(
  `id`              int(11) auto_increment,
  `created_at`      timestamp    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      timestamp    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `name`            varchar(255) NOT NULL,
  `description`     text,
  `image_url`       text         NOT NULL,
  `initial_price`   double       NOT NULL,
  `completion_date` date         NOT NULL,
  `bet_step`        int(11)      NOT NULL,
  `author_users_id` int(11)      NOT NULL,
  `winner_users_id` int(11)               DEFAULT NULL,
  `categories_id`   int(11)      NOT NULL,
  PRIMARY KEY (ID)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

CREATE FULLTEXT INDEX lot_ft_search ON lots(name, description);

CREATE TABLE `users`
(
  `id`         int(11) auto_increment,
  `created_at` timestamp    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `email`      text         NOT NULL,
  `name`       varchar(255) NOT NULL,
  `password`   varchar(255) NOT NULL,
  `contacts`   text,
  PRIMARY KEY (ID)

) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

ALTER TABLE `categories`
  ADD UNIQUE KEY `Name_idx` (`name`) USING BTREE;

ALTER TABLE `lots`
  ADD KEY `id_users_author` (`author_users_id`),
  ADD KEY `id_categories` (`categories_id`),
  ADD KEY `name` (`name`);
ALTER TABLE `lots`
  ADD FULLTEXT KEY `description` (`description`);


ALTER TABLE `lots`
  ADD CONSTRAINT `lots_ibfk_1` FOREIGN KEY (`author_users_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `lots_ibfk_2` FOREIGN KEY (`categories_id`) REFERENCES `categories` (`id`);

ALTER TABLE `bets`
  ADD CONSTRAINT `bets_ibfk_1` FOREIGN KEY (`lots_id`) REFERENCES `lots` (`id`),
  ADD CONSTRAINT `bets_ibfk_2` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`);


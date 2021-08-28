CREATE
  DATABASE IF NOT EXISTS `yeticave` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE
  `yeticave`;

CREATE TABLE `bets`
(
  `id`            int(11)   NOT NULL,
  `creation_time` timestamp NOT NULL,
  `amount`        int(11)   NOT NULL,
  `id_users`      int(11)   NOT NULL,
  `id_lots`       int(11)   NOT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

CREATE TABLE `categories`
(
  `id`          int(11)  NOT NULL,
  `name`        char(64) NOT NULL,
  `symbol_code` char(32) NOT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

INSERT INTO `categories` (`id`, `name`, `symbol_code`)
VALUES (1, 'Доски и лыжи', 'boards'),
       (2, 'Крепления', 'attachment'),
       (3, 'Ботинки', 'boots'),
       (4, 'Одежда', 'clothing'),
       (5, 'Инструменты', 'tools'),
       (6, 'Разное', 'other');

CREATE TABLE `lots`
(
  `id`              int(11)      NOT NULL,
  `creation_time`   timestamp    NOT NULL,
  `name`            varchar(255) NOT NULL,
  `description`     text,
  `url_image`       varchar(255) NOT NULL,
  `initial_price`   double       NOT NULL,
  `completion_date` date         NOT NULL,
  `bid_step`        int(11)      NOT NULL,
  `id_users_author` int(11)      NOT NULL,
  `id_users_winner` int(11) DEFAULT NULL,
  `id_categories`   int(11)      NOT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

CREATE TABLE `users`
(
  `id`                int(11)      NOT NULL,
  `registration_date` timestamp    NOT NULL,
  `email`             char(64)     NOT NULL,
  `name`              varchar(255) NOT NULL,
  `password`          varchar(255) NOT NULL,
  `contacts`          text,
  `id_lots`           int(11)      NOT NULL,
  `id_bets`           int(11)      NOT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

ALTER TABLE `bets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_lots` (`id_lots`),
  ADD KEY `id_users` (`id_users`);

ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `Name_idx` (`name`) USING BTREE;

ALTER TABLE `lots`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_users_author` (`id_users_author`),
  ADD KEY `id_categories` (`id_categories`),
  ADD KEY `name` (`name`);
ALTER TABLE `lots`
  ADD FULLTEXT KEY `description` (`description`);


ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_bets` (`id_bets`),
  ADD KEY `id_lots` (`id_lots`);

ALTER TABLE `bets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,
  AUTO_INCREMENT = 7;

ALTER TABLE `lots`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `bets`
  ADD CONSTRAINT `bets_ibfk_1` FOREIGN KEY (`id_lots`) REFERENCES `lots` (`id`),
  ADD CONSTRAINT `bets_ibfk_2` FOREIGN KEY (`id_users`) REFERENCES `users` (`id`);

ALTER TABLE `lots`
  ADD CONSTRAINT `lots_ibfk_1` FOREIGN KEY (`id_users_author`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `lots_ibfk_2` FOREIGN KEY (`id_categories`) REFERENCES `categories` (`id`);

ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`id_bets`) REFERENCES `bets` (`id`),
  ADD CONSTRAINT `users_ibfk_2` FOREIGN KEY (`id_lots`) REFERENCES `lots` (`id`);



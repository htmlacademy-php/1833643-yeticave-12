CREATE TABLE `bets`
(
  `id`         int(11)   NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `amount`     int(11)   NOT NULL,
  `users_id`   int(11)   NOT NULL,
  `lots_id`    int(11)   NOT NULL
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
  `categories_id`   int(11)      NOT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

CREATE TABLE `users`
(
  `id`         int(11)      NOT NULL,
  `created_at` timestamp    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `email`      text         NOT NULL,
  `name`       varchar(255) NOT NULL,
  `password`   varchar(255) NOT NULL,
  `contacts`   text,
  `lots_id`    int(11)      NOT NULL,
  `bets_id`    int(11)      NOT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

ALTER TABLE `bets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lots_id` (`lots_id`),
  ADD KEY `users_id` (`users_id`);

ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `Name_idx` (`name`) USING BTREE;

ALTER TABLE `lots`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_users_author` (`author_users_id`),
  ADD KEY `id_categories` (`categories_id`),
  ADD KEY `name` (`name`);
ALTER TABLE `lots`
  ADD FULLTEXT KEY `description` (`description`);


ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_bets` (`bets_id`),
  ADD KEY `id_lots` (`lots_id`);

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
  ADD CONSTRAINT `bets_ibfk_1` FOREIGN KEY (`lots_id`) REFERENCES `lots` (`id`),
  ADD CONSTRAINT `bets_ibfk_2` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`);

ALTER TABLE `lots`
  ADD CONSTRAINT `lots_ibfk_1` FOREIGN KEY (`author_users_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `lots_ibfk_2` FOREIGN KEY (`categories_id`) REFERENCES `categories` (`id`);

ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`bets_id`) REFERENCES `bets` (`id`),
  ADD CONSTRAINT `users_ibfk_2` FOREIGN KEY (`lots_id`) REFERENCES `lots` (`id`);



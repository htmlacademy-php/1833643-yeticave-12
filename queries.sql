#
#Напишите запросы для добавления информации в БД:
#  существующий список категорий;
INSERT INTO categories (`name`, `symbol_code`)
VALUES ('Доски и лыжи', 'boards'),
       ('Крепления', 'attachment'),
       ('Ботинки', 'boots'),
       ('Одежда', 'clothing'),
       ('Инструменты', 'tools'),
       ('Разное', 'other');

# придумайте пару пользователей;
INSERT INTO `users`(`email`, `name`, `password`)
VALUES ('user1@mail.ru', 'user1', '$2y$10$nJSxgERzB22nt7VQI0nbzOYgZMIY5muXBcQnoScS0oZiMgnW04jnC'),
       ('user2@mail.ru', 'user2', '$2y$10$nJSxgERzB22nt7VQI0nbzOYgZMIY5muXBcQnoScS0oZiMgnW04jnC');

# существующий список объявлений;
INSERT INTO `lots`(`name`, `image_url`, `initial_price`, `completion_date`, `bet_step`, `author_users_id`,
                   `categories_id`)
VALUES ('2014 Rossignol District Snowboard', 'img/lot-1.jpg', 10999, '2022-12-23', 1, 1, 1),
       ('DC Ply Mens 2016/2017 Snowboard', 'img/lot-2.jpg', 15999, '2022-12-18', 1, 1, 1),
       ('Крепления Union Contact Pro 2015 года размер L/XL', 'img/lot-3.jpg', 8000, '2022-12-01', 1, 1, 2),
       ('Ботинки для сноуборда DC Mutiny Charocal', 'img/lot-4.jpg', 10999, '2022-11-30', 1, 1, 3),
       ('Куртка для сноуборда DC Mutiny Charocal', 'img/lot-5.jpg', 7500, '2022-11-29', 1, 1, 4),
       ('Маска Oakley Canopy', 'img/lot-6.jpg', 5400, '2022-10-22', 1, 1, 6);

# добавьте пару ставок для любого объявления
INSERT INTO `bets`(`amount`, `users_id`, `lots_id`)
VALUES (5500, 2, 6),
       (7600, 2, 5);

# Напишите запросы для этих действий:
# получить все категории;
SELECT name as category
FROM categories;

# получить самые новые, открытые лоты. Каждый лот должен включать название, стартовую цену, ссылку на изображение, цену, название категории;
SELECT lots.id,
       lots.name       as name,
       image_url,
       initial_price,
       categories.name as category
FROM lots
       INNER JOIN categories ON lots.categories_id = categories.id
WHERE completion_date > now();

#  показать лот по его ID. Получите также название категории, к которой принадлежит лот;
SELECT lots.id,
       lots.name       as name,
       image_url,
       initial_price,
       categories.name as category
FROM lots
       INNER JOIN categories ON lots.categories_id = categories.id
WHERE lots.id = 1;


#  обновить название лота по его идентификатору;
UPDATE lots
SET name = '2014 Rossignol District Snowboard'
WHERE id = 1;

# получить список ставок для лота по его идентификатору с сортировкой по дате
SELECT created_at,
       updated_at,
       amount
FROM bets
WHERE lots_id = 6
order by created_at

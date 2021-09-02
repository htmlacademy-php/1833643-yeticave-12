#
Напишите запросы для добавления информации в БД:
#  существующий список категорий;
INSERT INTO categories (`id`, `name`, `symbol_code`)
VALUES (1, 'Доски и лыжи', 'boards'),
       (2, 'Крепления', 'attachment'),
       (3, 'Ботинки', 'boots'),
       (4, 'Одежда', 'clothing'),
       (5, 'Инструменты', 'tools'),
       (6, 'Разное', 'other');

# придумайте пару пользователей;
INSERT INTO `users`(`id`, `email`, `name`, `password`)
VALUES (1, 'user1@mail.ru', 'user1', 'password'),
       (2, 'user2@mail.ru', 'user2', 'password')

# существующий список объявлений;
INSERT INTO `lots`(`id`, `name`, `image_url`, `initial_price`, `completion_date`, `bet_step`, `author_users_id`,
            `categories_id`)
VALUES (1, '2014 Rossignol District Snowboard', 'img/lot-1.jpg', 10999, '2021-09-23', 1, 1, 1),
       (2, 'DC Ply Mens 2016/2017 Snowboard', 'img/lot-2.jpg', 15999, '2021-09-18', 1, 1, 1),
       (3, 'Крепления Union Contact Pro 2015 года размер L/XL', 'img/lot-3.jpg', 8000, '2021-09-19', 1, 1, 2),
       (4, 'Ботинки для сноуборда DC Mutiny Charocal', 'img/lot-4.jpg', 10999, '2021-09-20', 1, 1, 3),
       (5, 'Куртка для сноуборда DC Mutiny Charocal', 'img/lot-5.jpg', 7500, '2021-09-21', 1, 1, 4),
       (6, 'Маска Oakley Canopy', 'img/lot-6.jpg', 5400, '2021-09-22', 1, 1, 6)

# добавьте пару ставок для любого объявления
INSERT INTO `bets`(`amount`, `users_id`, `lots_id`)
VALUES (5500, 2, 6),
  (7600, 2, 5)

  # Напишите запросы для этих действий:
  # получить все категории;
SELECT name as Category
FROM categories

  # получить самые новые, открытые лоты. Каждый лот должен включать название, стартовую цену, ссылку на изображение, цену, название категории;
SELECT lots.id,
       lots.name            as Наименование,
       image_url            as Изображение,
       initial_price + ' р' as Стартовая_цена,
       categories.name      as Категория
FROM lots
       INNER JOIN categories ON lots.categories_id = categories.id
WHERE completion_date > CURRENT_TIMESTAMP

#  показать лот по его ID. Получите также название категории, к которой принадлежит лот;
SELECT lots.id,
       lots.name            as Наименование,
       image_url            as Изображение,
       initial_price + ' р' as Стартовая_цена,
       categories.name      as Категория
FROM lots
       INNER JOIN categories ON lots.categories_id = categories.id
WHERE lots.id = 1


#  обновить название лота по его идентификатору;
UPDATE lots
SET name = '2014 Rossignol District Snowboard'
WHERE id = 1;

# получить список ставок для лота по его идентификатору с сортировкой по дате
SELECT
       created_at as Создано,
       updated_at as Изменено,
       amount as Сумма
FROM bets
WHERE lots_id = 6
order by created_at

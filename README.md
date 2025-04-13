# tr-crm

1. Текст задания на доработку CRM (обновленная версия)
   Цель:
   Реализовать в CRM систему управления переводчиками с учетом их доступности в будние/выходные дни, используя нормализованную структуру БД и современный стек технологий (Yii2, Vue.js, Docker).

Требования к системе:
База данных:
    Таблица translators:
        id (PK)
        name (string)
        email (string, unique)
        password (string, хешированный)
    
    Таблица translator_schedules:
        id (PK)
        translator_id (FK к translators.id)
        day_type (enum: weekday, weekend)
        is_available (boolean)

Бэкенд (Yii2):
    Реализовать модели:
        Translator с связью hasMany к TranslatorSchedule.
        TranslatorSchedule с связью belongsTo к Translator.

Миграции для создания таблиц и индексов.

REST API для:
    Получения списка переводчиков, доступных в текущий день.
    Управления учетными данными (регистрация/авторизация через password).

Фронтенд (Vue.js):
    Страница /translators с динамическим отображением:
        Списка доступных переводчиков (в зависимости от дня недели).
    Индикации типа занятости (иконки для будней/выходных).

Компонент для администратора:
    Добавление/редактирование расписания переводчиков.

Критерии приемки:
    Безопасность:
        Пароли хранятся в виде хешей (использовать yii\base\Security).
        Нет прямого доступа к API без аутентификации.

Логика доступности:
    В будни (Пн–Пт) выбираются переводчики с day_type = 'weekday'.
    В выходные (Сб–Вс) — с day_type = 'weekend'.

Производительность:
    Запросы к БД используют индексы на translator_id и day_type.
    Данные от API кешируются на 5 минут.

(Исходил исключительно из преамбулы, по этому сделал все просто, исходя из многих возникающих вопросов можно было 
расширить backend но не стал выходить за рамки ТЗ, в основном из за отсутствия времени)

Примеры SQL запросов (не очень понял для чего):
(В приложежнии таблицы сосздаются через файлы миграциЙ)
Создание таблицы translators:
CREATE TABLE `translators` (
`id` INT(11) NOT NULL AUTO_INCREMENT,
`name` VARCHAR(255) NOT NULL,
`email` VARCHAR(255) NOT NULL UNIQUE,
`password` VARCHAR(255) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

Создание таблицы translator_schedules:
CREATE TABLE `translator_schedules` (
`id` INT(11) NOT NULL AUTO_INCREMENT,
`translator_id` INT(11) NOT NULL,
`day_type` ENUM('weekday', 'weekend') NOT NULL,
`is_available` TINYINT(1) NOT NULL DEFAULT 0,
PRIMARY KEY (`id`),
FOREIGN KEY (`translator_id`)
REFERENCES `translators`(`id`)
ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `translator_schedules`
ADD INDEX `idx_day_type_availability` (`day_type`, `is_available`);

-- Добавляем переводчиков
INSERT INTO `translators` (`name`, `email`, `password`)
VALUES
('Иван Петров', 'ivan@example.com', '$2y$10$hashed_password_1'),
('Анна Сидорова', 'anna@example.com', '$2y$10$hashed_password_2');

-- Добавляем расписания
INSERT INTO `translator_schedules` (`translator_id`, `day_type`, `is_available`)
VALUES (1, 'weekday', 1),(2, 'weekend', 1);

-- Запретить работу переводчика с ID=1 в будни
UPDATE `translator_schedules`
SET `is_available` = 0
WHERE
`translator_id` = 1
AND `day_type` = 'weekday';

-- Удалить все расписания для переводчика с ID=5
DELETE FROM `translator_schedules`
WHERE `translator_id` = 5;

-- Получить все доступные расписания
SELECT *
FROM `translator_schedules`
WHERE `is_available` = 1;

-- Проверить, работает ли переводчик с ID=3 в выходные
SELECT `is_available`
FROM `translator_schedules`
WHERE
`translator_id` = 3
AND `day_type` = 'weekend';

Получить всех переводчиков, доступных сегодня
SELECT
t.`id`,
t.`name`,
t.`email`,
ts.`day_type`
FROM `translators` t
INNER JOIN `translator_schedules` ts
ON t.`id` = ts.`translator_id`
WHERE
ts.`is_available` = 1
AND ts.`day_type` = IF(DAYOFWEEK(NOW()) IN (1,7), 'weekend', 'weekday');

-- Количество переводчиков по типам дней
SELECT
ts.`day_type`,
COUNT(*) AS `total_available`
FROM `translator_schedules` ts
WHERE ts.`is_available` = 1
GROUP BY ts.`day_type`;

Поиск "универсальных" переводчиков (работают и в будни, и в выходные)
SELECT
t.`id`,
t.`name`
FROM `translators` t
WHERE t.`id` IN (
SELECT `translator_id`
FROM `translator_schedules`
WHERE `is_available` = 1
GROUP BY `translator_id`
HAVING COUNT(DISTINCT `day_type`) = 2
);

Транзакции
-- Обновить email и добавить новое расписание атомарно
START TRANSACTION;

UPDATE `translators`
SET `email` = 'updated@example.com'
WHERE `id` = 1;

INSERT INTO `translator_schedules` (`translator_id`, `day_type`, `is_available`)
VALUES (1, 'weekend', 1);

COMMIT;

Все SQL запросы выполняются в приложении через построитель запросов
либо могут быть выполнены через модели:
// Пример: Транзакция в Yii2
Yii::$app->db->transaction(function() {
$translator = Translator::findOne(1);
$translator->email = 'updated@example.com';
$translator->save();

    $schedule = new TranslatorSchedule();
    $schedule->translator_id = 1;
    $schedule->day_type = 'weekend';
    $schedule->is_available = 1;
    $schedule->save();
});

Примемры работы с бд в приложении
// Создание расписания
TranslatorSchedule::updateSchedule(1, 'weekday', true);

// Получение переводчиков
$translators = Translator::getAvailableTranslators();


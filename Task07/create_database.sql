PRAGMA foreign_keys = ON;

-- 1. Очистка таблиц
DROP TABLE IF EXISTS exam;
DROP TABLE IF EXISTS subject_list;
DROP TABLE IF EXISTS student_card;
DROP TABLE IF EXISTS student;
DROP TABLE IF EXISTS study_group;
DROP TABLE IF EXISTS speciality;

-- 2. Специальности
CREATE TABLE speciality (
                            id INTEGER PRIMARY KEY AUTOINCREMENT,
                            name TEXT NOT NULL UNIQUE
);

-- 3. Группы
CREATE TABLE study_group (
                             id INTEGER PRIMARY KEY AUTOINCREMENT,
                             speciality_id INTEGER NOT NULL,
                             name TEXT NOT NULL UNIQUE,
                             FOREIGN KEY (speciality_id) REFERENCES speciality(id) ON DELETE CASCADE
);

-- 4. Студенты
CREATE TABLE student (
                         id INTEGER PRIMARY KEY AUTOINCREMENT,
                         name TEXT NOT NULL,
                         family TEXT NOT NULL,
                         father_name TEXT,
                         sex TEXT CHECK (sex IN ('male', 'female')),
                         birth_date TEXT
);

-- 5. Справочник предметов (привязан к специальности и курсу)
CREATE TABLE subject_list (
                              id INTEGER PRIMARY KEY AUTOINCREMENT,
                              speciality_id INTEGER NOT NULL,
                              subject_name TEXT NOT NULL,
                              course_year INTEGER NOT NULL CHECK (course_year BETWEEN 1 AND 4),
                              FOREIGN KEY (speciality_id) REFERENCES speciality(id) ON DELETE CASCADE
);

-- 6. Студенческие карточки
CREATE TABLE student_card (
                              id INTEGER PRIMARY KEY AUTOINCREMENT,
                              id_student INTEGER NOT NULL UNIQUE,
                              id_group INTEGER NOT NULL,
                              ticket_number TEXT NOT NULL UNIQUE,
                              start_date TEXT NOT NULL,
                              end_date TEXT NOT NULL,
                              CHECK (start_date GLOB '????-??-??'),
    CHECK (end_date GLOB '????-??-??' AND date(end_date) > date(start_date)),
    FOREIGN KEY (id_group) REFERENCES study_group(id) ON DELETE CASCADE,
    FOREIGN KEY (id_student) REFERENCES student(id) ON DELETE CASCADE
);

-- 7. Результаты экзаменов (ссылаются на предмет из справочника)
CREATE TABLE exam (
                      id INTEGER PRIMARY KEY AUTOINCREMENT,
                      id_student INTEGER NOT NULL,
                      id_subject INTEGER NOT NULL,
                      exam_date TEXT NOT NULL,
                      grade INTEGER NOT NULL CHECK (grade BETWEEN 2 AND 5),
                      FOREIGN KEY (id_student) REFERENCES student(id) ON DELETE CASCADE,
                      FOREIGN KEY (id_subject) REFERENCES subject_list(id) ON DELETE CASCADE
);

-- ВАЛИДАЦИЯ: Год окончания не больше текущего года (через триггер)
CREATE TRIGGER validate_graduation_year_insert
    BEFORE INSERT ON student_card
    FOR EACH ROW
BEGIN
    SELECT CASE
               WHEN strftime('%Y', NEW.end_date) > strftime('%Y', 'now')
                   THEN RAISE (ABORT, 'Ошибка: Год окончания обучения не может быть в будущем!')
               END;
END;

-- ЗАПОЛНЕНИЕ ДАННЫМИ --

INSERT INTO speciality (id, name) VALUES
                                      (1, 'Фундаментальная информатика и информационные технологии'),
                                      (2, 'Прикладная математика и информатика'),
                                      (3, 'Программная инженерия');

INSERT INTO study_group (id, speciality_id, name) VALUES
                                                      (1, 1, '301'), (2, 1, '302'), (3, 2, '303'), (4, 3, '304'), (5, 3, '305');

-- Предметы для специальностей
-- ФИТ (1 курс и 2 курс)
INSERT INTO subject_list (speciality_id, subject_name, course_year) VALUES
                                                                        (1, 'Высшая математика', 1), (1, 'Дискретная математика', 1), (1, 'Базы данных', 2), (1, 'Алгоритмы', 2);
-- ПМИ
INSERT INTO subject_list (speciality_id, subject_name, course_year) VALUES
                                                                        (2, 'Математический анализ', 1), (2, 'Линейная алгебра', 1), (2, 'Теория вероятностей', 2);
-- ПИ
INSERT INTO subject_list (speciality_id, subject_name, course_year) VALUES
                                                                        (3, 'Введение в ПИ', 1), (3, 'Архитектура ЭВМ', 1), (3, 'Тестирование ПО', 2);

-- Вставка всех 96 студентов
INSERT INTO student (id, name, family, father_name, sex, birth_date) VALUES
                                                                         (1, 'Никита', 'Андронов', 'Алексеевич', 'male', '2005-03-12'), (2, 'Данила', 'Бабин', 'Олегович', 'male', '2004-11-24'),
                                                                         (3, 'Алина', 'Буянкина', 'Сергеевна', 'female', '2005-07-18'), (4, 'Павел', 'Голиков', 'Александрович', 'male', '2004-09-05'),
                                                                         (5, 'Константин', 'Гончаров', 'Дмитриевич', 'male', '2005-01-30'), (6, 'Вадим', 'Еремин', 'Сергеевич', 'male', '2004-05-22'),
                                                                         (7, 'Илья', 'Журин', 'Александрович', 'male', '2005-08-14'), (8, 'Ильдар', 'Кармышев', 'Русланович', 'male', '2004-12-09'),
                                                                         (9, 'Альвина', 'Кучина', 'Сергеевна', 'female', '2005-04-03'), (10, 'Максим', 'Ларькин', 'Сергеевич', 'male', '2004-10-27'),
                                                                         (11, 'Роман', 'Лемясев', 'Николаевич', 'male', '2005-02-19'), (12, 'Максим', 'Лузин', 'Дмитриевич', 'male', '2004-06-11'),
                                                                         (13, 'Михаил', 'Марьин', 'Вячеславович', 'male', '2005-09-28'), (14, 'Вадим', 'Орлов', 'Дмитриевич', 'male', '2004-08-07'),
                                                                         (15, 'Роман', 'Пьянов', 'Владимирович', 'male', '2005-11-15'), (16, 'Михаил', 'Родионов', 'Николаевич', 'male', '2004-03-21'),
                                                                         (17, 'Максим', 'Самылкин', 'Евгеньевич', 'male', '2005-10-02'), (18, 'Максим', 'Сарайкин', 'Евгеньевич', 'male', '2004-07-16'),
                                                                         (19, 'Александр', 'Сеничев', 'Сергеевич', 'male', '2005-12-25'), (20, 'Сергей', 'Фомин', 'Олегович', 'male', '2004-02-28'),
                                                                         (21, 'Данил', 'Харитонов', 'Витальевич', 'male', '2005-05-09'), (22, 'Николай', 'Холов', 'Александрович', 'male', '2004-01-17'),
                                                                         (23, 'Юлия', 'Хрипченко', 'Владимировна', 'female', '2005-06-30'), (24, 'Максим', 'Чернов', 'Михайлович', 'male', '2004-04-13'),
                                                                         (25, 'Егор', 'Гришуков', 'Витальевич', 'male', '2005-02-10'), (26, 'Иван', 'Данькин', '', 'male', '2004-08-25'),
                                                                         (27, 'Егор', 'Ермаков', 'Александрович', 'male', '2005-04-18'), (28, 'Никита', 'Кармазов', 'Александрович', 'male', '2004-11-03'),
                                                                         (29, 'Евгений', 'Китаев', 'Витальевич', 'male', '2005-06-12'), (30, 'Александр', 'Колевин', 'Сергеевич', 'male', '2004-09-20'),
                                                                         (31, 'Иван', 'Лоханов', 'Вячеславович', 'male', '2005-01-28'), (32, 'Роман', 'Лукьянов', 'Александрович', 'male', '2004-07-09'),
                                                                         (33, 'Константин', 'Маркин', 'Романович', 'male', '2005-03-15'), (34, 'Дмитрий', 'Романов', 'Алексеевич', 'male', '2004-12-05'),
                                                                         (35, 'Ирина', 'Соснина', 'Васильевна', 'female', '2005-08-22'), (36, 'Максим', 'Тиосса', 'Николаевич', 'male', '2004-05-30'),
                                                                         (37, 'Данила', 'Тужин', 'Олегович', 'male', '2005-10-17'), (38, 'Никита', 'Учуваткин', 'Сергеевич', 'male', '2004-01-24'),
                                                                         (39, 'Алексей', 'Шапошников', 'Алексеевич', 'male', '2005-07-07'), (40, 'Ольга', 'Шиляева', 'Игоревна', 'female', '2004-03-14'),
                                                                         (41, 'Андрей', 'Абатуров', 'Дмитриевич', 'male', '2005-04-11'), (42, 'Иван', 'Агафонов', 'Сергеевич', 'male', '2004-06-22'),
                                                                         (43, 'Александр', 'Арсенин', 'Игоревич', 'male', '2005-02-03'), (44, 'Артур', 'Бабанин', 'Александрович', 'male', '2004-09-17'),
                                                                         (45, 'Константин', 'Беляев', 'Владимирович', 'male', '2005-07-29'), (46, 'Александр', 'Богданов', 'Сергеевич', 'male', '2004-11-08'),
                                                                         (47, 'Михаил', 'Борисов', 'Алексеевич', 'male', '2005-01-20'), (48, 'Алексей', 'Бочков', 'Вячеславович', 'male', '2004-03-15'),
                                                                         (49, 'Данил', 'Будников', 'Олегович', 'male', '2005-08-05'), (50, 'Евгений', 'Васильев', 'Александрович', 'male', '2004-05-12'),
                                                                         (51, 'Даниил', 'Волков', 'Дмитриевич', 'male', '2005-10-30'), (52, 'Андрей', 'Гаврилов', 'Романович', 'male', '2004-12-22'),
                                                                         (53, 'Павел', 'Голубев', 'Сергеевич', 'male', '2005-06-09'), (54, 'Артем', 'Горбунов', 'Иванович', 'male', '2004-02-14'),
                                                                         (55, 'Антон', 'Грибанов', 'Михайлович', 'male', '2005-09-16'), (56, 'Дмитрий', 'Демидов', 'Алексеевич', 'male', '2004-04-25'),
                                                                         (57, 'Никита', 'Егоров', 'Витальевич', 'male', '2005-11-03'), (58, 'Александр', 'Еремин', 'Сергеевич', 'male', '2004-07-18'),
                                                                         (59, 'Максим', 'Жданов', 'Олегович', 'male', '2005-03-27'), (60, 'Иван', 'Жуков', 'Андреевич', 'male', '2004-10-02'),
                                                                         (61, 'Алексей', 'Зайцев', 'Дмитриевич', 'male', '2005-12-19'), (62, 'Павел', 'Козлов', 'Сергеевич', 'male', '2004-01-08'),
                                                                         (63, 'Андрей', 'Кузнецов', 'Владимирович', 'male', '2005-05-10'), (64, 'Даниил', 'Лебедев', 'Александрович', 'male', '2004-08-23'),
                                                                         (65, 'Александр', 'Логинов', 'Игоревич', 'male', '2005-02-28'), (66, 'Арсений', 'Макаров', 'Андреевич', 'male', '2004-06-07'),
                                                                         (67, 'Роман', 'Медведев', 'Сергеевич', 'male', '2005-09-04'), (68, 'Артём', 'Морозов', 'Дмитриевич', 'male', '2004-11-21'),
                                                                         (69, 'Роман', 'Аксенов', 'Михайлович', 'male', '2005-01-15'), (70, 'Дмитрий', 'Артемьев', 'Алексеевич', 'male', '2004-07-02'),
                                                                         (71, 'Дмитрий', 'Афонькин', 'Евгеньевич', 'male', '2005-03-12'), (72, 'Роман', 'Гераськин', 'Геннадьевич', 'male', '2004-09-28'),
                                                                         (73, 'Олег', 'Доля', 'Альбертович', 'male', '2005-05-19'), (74, 'Максим', 'Забненков', 'Алексеевич', 'male', '2004-12-01'),
                                                                         (75, 'Роман', 'Кижаем', 'Петрович', 'male', '2005-02-14'), (76, 'Никита', 'Киселев', 'Сергеевич', 'male', '2004-04-09'),
                                                                         (77, 'Владислав', 'Кочетов', 'Андреевич', 'male', '2005-06-27'), (78, 'Роман', 'Кувакин', 'Александрович', 'male', '2004-08-11'),
                                                                         (79, 'Ренард', 'Курмакаев', 'Анварович', 'male', '2005-10-05'), (80, 'Ксения', 'Луковатая', 'Вадимовна', 'female', '2004-01-18'),
                                                                         (81, 'Иван', 'Мигачев', 'Павлович', 'male', '2005-07-22'), (82, 'Олег', 'Моисеев', 'Максимович', 'male', '2004-03-09'),
                                                                         (83, 'Роман', 'Зубков', 'Сергеевич', 'male', '2005-09-30'), (84, 'Максим', 'Иванов', 'Александрович', 'male', '2004-11-12'),
                                                                         (85, 'Артём', 'Ивенин', 'Андреевич', 'male', '2005-04-05'), (86, 'Иван', 'Казейкин', 'Иванович', 'male', '2004-02-23'),
                                                                         (87, 'Артем', 'Кочнев', 'Алексеевич', 'male', '2005-08-17'), (88, 'Илья', 'Логунов', 'Сергеевич', 'male', '2004-10-08'),
                                                                         (89, 'Юлия', 'Макарова', 'Сергеевна', 'female', '2005-12-01'), (90, 'Сергей', 'Маклаков', 'Александрович', 'male', '2004-05-14'),
                                                                         (91, 'Наталья', 'Маскинская', 'Сергеевна', 'female', '2005-01-29'), (92, 'Дмитрий', 'Мукасеев', 'Александрович', 'male', '2004-06-18'),
                                                                         (93, 'Владислав', 'Наумкин', 'Валерьевич', 'male', '2005-03-25'), (94, 'Василий', 'Паркаев', 'Александрович', 'male', '2004-07-07'),
                                                                         (95, 'Дмитрий', 'Полковников', 'Александрович', 'male', '2005-09-14'), (96, 'Полина', 'Пшеницына', 'Алексеевна', 'female', '2004-11-27');

-- Генерация карточек
INSERT INTO student_card (id_student, id_group, ticket_number, start_date, end_date)
SELECT id,
       CASE
           WHEN id BETWEEN 1 AND 24 THEN 1
           WHEN id BETWEEN 25 AND 40 THEN 2
           WHEN id BETWEEN 41 AND 68 THEN 3
           WHEN id BETWEEN 69 AND 82 THEN 4
           ELSE 5
           END,
       ('23' || (1000 + id)),
       '2023-09-01', '2025-01-30'
FROM student;

-- Генерация экзаменов
INSERT INTO exam (id_student, id_subject, exam_date, grade)
SELECT s.id, 1, '2024-01-15', 5 FROM student s JOIN student_card sc ON s.id=sc.id_student WHERE sc.id_group IN (1,2);
INSERT INTO exam (id_student, id_subject, exam_date, grade)
SELECT s.id, 5, '2024-01-15', 4 FROM student s JOIN student_card sc ON s.id=sc.id_student WHERE sc.id_group = 3;
INSERT INTO exam (id_student, id_subject, exam_date, grade)
SELECT s.id, 8, '2024-01-15', 4 FROM student s JOIN student_card sc ON s.id=sc.id_student WHERE sc.id_group IN (4,5);
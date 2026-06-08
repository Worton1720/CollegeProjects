BEGIN;

DROP TABLE IF EXISTS zakaz_tovar, zakaz, tovar, sotrudnik, punkt_vidachi,
    status_zakaza, kategoria, proizvoditel, postavshik, ed_izm, role CASCADE;

CREATE TABLE role (
    id INTEGER PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE ed_izm (
    id INTEGER PRIMARY KEY,
    name VARCHAR(20) NOT NULL UNIQUE
);

CREATE TABLE postavshik (
    id INTEGER PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE proizvoditel (
    id INTEGER PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE kategoria (
    id INTEGER PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE status_zakaza (
    id INTEGER PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE punkt_vidachi (
    id INTEGER PRIMARY KEY,
    adres VARCHAR(255) NOT NULL
);

CREATE TABLE sotrudnik (
    id INTEGER PRIMARY KEY,
    id_role INTEGER NOT NULL REFERENCES role(id),
    fio VARCHAR(150) NOT NULL,
    login VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(100) NOT NULL
);

CREATE TABLE tovar (
    id INTEGER PRIMARY KEY,
    artikul VARCHAR(20) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL,
    id_ed_izm INTEGER NOT NULL REFERENCES ed_izm(id),
    price NUMERIC(10,2) NOT NULL CHECK (price >= 0),
    id_postavshik INTEGER NOT NULL REFERENCES postavshik(id),
    id_proizvoditel INTEGER NOT NULL REFERENCES proizvoditel(id),
    id_kategoria INTEGER NOT NULL REFERENCES kategoria(id),
    skidka INTEGER NOT NULL DEFAULT 0 CHECK (skidka >= 0 AND skidka <= 100),
    kol_vo INTEGER NOT NULL DEFAULT 0 CHECK (kol_vo >= 0),
    opisanie TEXT,
    foto VARCHAR(255)
);

CREATE TABLE zakaz (
    id INTEGER PRIMARY KEY,
    start_date DATE NOT NULL,
    finish_date DATE,
    id_punkt_vidachi INTEGER NOT NULL REFERENCES punkt_vidachi(id),
    id_client INTEGER NOT NULL REFERENCES sotrudnik(id),
    id_status INTEGER NOT NULL REFERENCES status_zakaza(id),
    kod_polucheniya INTEGER NOT NULL
);

CREATE TABLE zakaz_tovar (
    id INTEGER PRIMARY KEY,
    id_zakaz INTEGER NOT NULL REFERENCES zakaz(id) ON DELETE CASCADE,
    id_tovar INTEGER NOT NULL REFERENCES tovar(id),
    kol_vo INTEGER NOT NULL CHECK (kol_vo > 0),
    UNIQUE (id_zakaz,
    id_tovar)
);

INSERT INTO role (id, name) VALUES (1, 'Администратор');
INSERT INTO role (id, name) VALUES (2, 'Менеджер');
INSERT INTO role (id, name) VALUES (3, 'Авторизированный клиент');

INSERT INTO ed_izm (id, name) VALUES (1, 'шт.');

INSERT INTO postavshik (id, name) VALUES (1, 'Pikeshop');
INSERT INTO postavshik (id, name) VALUES (2, 'Playbig');
INSERT INTO postavshik (id, name) VALUES (3, 'Knauf');
INSERT INTO postavshik (id, name) VALUES (4, 'CHILITOY');
INSERT INTO postavshik (id, name) VALUES (5, 'Vinylon');

INSERT INTO proizvoditel (id, name) VALUES (1, 'ABSпластик');
INSERT INTO proizvoditel (id, name) VALUES (2, 'BambiniFelici');
INSERT INTO proizvoditel (id, name) VALUES (3, 'Junion');

INSERT INTO kategoria (id, name) VALUES (1, 'Игровой набор');
INSERT INTO kategoria (id, name) VALUES (2, 'Конструктор');
INSERT INTO kategoria (id, name) VALUES (3, 'Детский музыкальный инструмент');
INSERT INTO kategoria (id, name) VALUES (4, 'Машинка');

INSERT INTO status_zakaza (id, name) VALUES (1, 'Завершен');
INSERT INTO status_zakaza (id, name) VALUES (2, 'Новый');

INSERT INTO punkt_vidachi (id, adres) VALUES (1, '420151, г. Лесной, ул. Вишневая, 32');
INSERT INTO punkt_vidachi (id, adres) VALUES (2, '125061, г. Лесной, ул. Подгорная, 8');
INSERT INTO punkt_vidachi (id, adres) VALUES (3, '630370, г. Лесной, ул. Шоссейная, 24');
INSERT INTO punkt_vidachi (id, adres) VALUES (4, '400562, г. Лесной, ул. Зеленая, 32');
INSERT INTO punkt_vidachi (id, adres) VALUES (5, '614510, г. Лесной, ул. Маяковского, 47');
INSERT INTO punkt_vidachi (id, adres) VALUES (6, '410542, г. Лесной, ул. Светлая, 46');
INSERT INTO punkt_vidachi (id, adres) VALUES (7, '620839, г. Лесной, ул. Цветочная, 8');
INSERT INTO punkt_vidachi (id, adres) VALUES (8, '443890, г. Лесной, ул. Коммунистическая, 1');
INSERT INTO punkt_vidachi (id, adres) VALUES (9, '603379, г. Лесной, ул. Спортивная, 46');
INSERT INTO punkt_vidachi (id, adres) VALUES (10, '603721, г. Лесной, ул. Гоголя, 41');
INSERT INTO punkt_vidachi (id, adres) VALUES (11, '410172, г. Лесной, ул. Северная, 13');
INSERT INTO punkt_vidachi (id, adres) VALUES (12, '614611, г. Лесной, ул. Молодежная, 50');
INSERT INTO punkt_vidachi (id, adres) VALUES (13, '454311, г.Лесной, ул. Новая, 19');
INSERT INTO punkt_vidachi (id, adres) VALUES (14, '660007, г.Лесной, ул. Октябрьская, 19');
INSERT INTO punkt_vidachi (id, adres) VALUES (15, '603036, г. Лесной, ул. Садовая, 4');
INSERT INTO punkt_vidachi (id, adres) VALUES (16, '394060, г.Лесной, ул. Фрунзе, 43');
INSERT INTO punkt_vidachi (id, adres) VALUES (17, '410661, г. Лесной, ул. Школьная, 50');
INSERT INTO punkt_vidachi (id, adres) VALUES (18, '625590, г. Лесной, ул. Коммунистическая, 20');
INSERT INTO punkt_vidachi (id, adres) VALUES (19, '625683, г. Лесной, ул. 8 Марта');
INSERT INTO punkt_vidachi (id, adres) VALUES (20, '450983, г.Лесной, ул. Комсомольская, 26');
INSERT INTO punkt_vidachi (id, adres) VALUES (21, '394782, г. Лесной, ул. Чехова, 3');
INSERT INTO punkt_vidachi (id, adres) VALUES (22, '603002, г. Лесной, ул. Дзержинского, 28');
INSERT INTO punkt_vidachi (id, adres) VALUES (23, '450558, г. Лесной, ул. Набережная, 30');
INSERT INTO punkt_vidachi (id, adres) VALUES (24, '344288, г. Лесной, ул. Чехова, 1');
INSERT INTO punkt_vidachi (id, adres) VALUES (25, '614164, г.Лесной,  ул. Степная, 30');
INSERT INTO punkt_vidachi (id, adres) VALUES (26, '394242, г. Лесной, ул. Коммунистическая, 43');
INSERT INTO punkt_vidachi (id, adres) VALUES (27, '660540, г. Лесной, ул. Солнечная, 25');
INSERT INTO punkt_vidachi (id, adres) VALUES (28, '125837, г. Лесной, ул. Шоссейная, 40');
INSERT INTO punkt_vidachi (id, adres) VALUES (29, '125703, г. Лесной, ул. Партизанская, 49');
INSERT INTO punkt_vidachi (id, adres) VALUES (30, '625283, г. Лесной, ул. Победы, 46');
INSERT INTO punkt_vidachi (id, adres) VALUES (31, '614753, г. Лесной, ул. Полевая, 35');
INSERT INTO punkt_vidachi (id, adres) VALUES (32, '426030, г. Лесной, ул. Маяковского, 44');
INSERT INTO punkt_vidachi (id, adres) VALUES (33, '450375, г. Лесной ул. Клубная, 44');
INSERT INTO punkt_vidachi (id, adres) VALUES (34, '625560, г. Лесной, ул. Некрасова, 12');
INSERT INTO punkt_vidachi (id, adres) VALUES (35, '630201, г. Лесной, ул. Комсомольская, 17');
INSERT INTO punkt_vidachi (id, adres) VALUES (36, '190949, г. Лесной, ул. Мичурина, 26');

INSERT INTO sotrudnik (id, id_role, fio, login, password) VALUES (1, 1, 'Ворсин Петр Евгеньевич', '94d5ous@gmail.com', 'uzWC67');
INSERT INTO sotrudnik (id, id_role, fio, login, password) VALUES (2, 1, 'Старикова Елена Павловна', 'uth4iz@mail.com', '2L6KZG');
INSERT INTO sotrudnik (id, id_role, fio, login, password) VALUES (3, 1, 'Одинцов Серафим Артёмович', 'yzls62@outlook.com', 'JlFRCZ');
INSERT INTO sotrudnik (id, id_role, fio, login, password) VALUES (4, 2, 'Михайлюк Анна Вячеславовна', '1diph5e@tutanota.com', '8ntwUp');
INSERT INTO sotrudnik (id, id_role, fio, login, password) VALUES (5, 2, 'Ситдикова Елена Анатольевна', 'tjde7c@yahoo.com', 'YOyhfR');
INSERT INTO sotrudnik (id, id_role, fio, login, password) VALUES (6, 2, 'Никифорова Весения Николаевна', 'wpmrc3do@tutanota.com', 'RSbvHv');
INSERT INTO sotrudnik (id, id_role, fio, login, password) VALUES (7, 3, 'Степанов Михаил Артёмович', '5d4zbu@tutanota.com', 'rwVDh9');
INSERT INTO sotrudnik (id, id_role, fio, login, password) VALUES (8, 3, 'Ворсин Петр Евгеньевич', 'ptec8ym@yahoo.com', 'LdNyos');
INSERT INTO sotrudnik (id, id_role, fio, login, password) VALUES (9, 3, 'Старикова Елена Павловна', '1qz4kw@mail.com', 'gynQMT');
INSERT INTO sotrudnik (id, id_role, fio, login, password) VALUES (10, 3, 'Сазонов Руслан Германович', '4np6se@mail.com', 'AtnDjr');

INSERT INTO tovar (id, artikul, name, id_ed_izm, price, id_postavshik, id_proizvoditel, id_kategoria, skidka, kol_vo, opisanie, foto) VALUES (1, 'PMEZMH', 'Детский игровой набор машинок Щенячий патруль / Dogs mini . 9 героев + 9 инерфионных машинок', 1, 1414, 1, 1, 1, 22, 50, 'Детский набор машинок с героями мультсериала «Щенячий патруль» подойдет как для мальчиков, так и для девочек. В детский набор входит 9 фигурок щенков спасателей. ', '1.jpg');
INSERT INTO tovar (id, artikul, name, id_ed_izm, price, id_postavshik, id_proizvoditel, id_kategoria, skidka, kol_vo, opisanie, foto) VALUES (2, 'BPV4MM', 'Конструктор Гарри Поттер Сова Букля 630 деталей совместим с lego harry potter, лего совместимый)', 1, 771, 2, 1, 2, 15, 26, 'Коллекционная модель Букля состоит из множества потрясающих элементов, а также специального механизма внутри. С его помощью можно плавно поднимать-опускать крылья птицы.', '2.jpg');
INSERT INTO tovar (id, artikul, name, id_ed_izm, price, id_postavshik, id_proizvoditel, id_kategoria, skidka, kol_vo, opisanie, foto) VALUES (3, 'JVL42J', 'Музыкальные инструменты для детей, ксилофон, барабаны, развивающие игрушки, игрушки для детей', 1, 2750, 2, 2, 3, 15, 0, 'Откройте мир музыки для вашего ребенка с этой уникальной игрушкой! Это многофункциональное музыкальное чудо объединяет в себе всё, что нужно для творческого развития.', '3.jpg');
INSERT INTO tovar (id, artikul, name, id_ed_izm, price, id_postavshik, id_proizvoditel, id_kategoria, skidka, kol_vo, opisanie, foto) VALUES (4, 'F895RB', 'Машинка игрушка диско шар светящаяся музыкальная', 1, 368, 3, 1, 4, 6, 7, 'Светящаяся музыкальная машина с диско шаром переливается разными цветами, играет ритмичные мелодии, объезжает препятствия и крутится, поэтому с ней точно не будет скучно.', '4.jpg');
INSERT INTO tovar (id, artikul, name, id_ed_izm, price, id_postavshik, id_proizvoditel, id_kategoria, skidka, kol_vo, opisanie, foto) VALUES (5, '3XBOTN', 'Игровой набор Hot Wheels Action Loop Cyclone Challenge Track, с машинкой и удобным хранением, HTK16', 1, 3426, 3, 2, 1, 10, 21, 'Игровой набор Hot Wheels Action Loop Cyclone Challenge Track - это уникальная игра, которая позволит вам испытать себя и своих друзей в скорости и ловкости. Этот набор состоит из металлической дорожки с циклоном, которая создает потрясающий эффект и добавляет дополнительную сложность в игру.', '5.jpg');
INSERT INTO tovar (id, artikul, name, id_ed_izm, price, id_postavshik, id_proizvoditel, id_kategoria, skidka, kol_vo, opisanie, foto) VALUES (6, '3L7RCZ', 'Игровой набор с деревянными машинками Стройплощадка Кран-Паркс, Junion', 1, 7400, 3, 3, 1, 15, 0, 'Игровой набор «Стройплощадка Кран-Паркс Junion» — это большая игрушечная парковка с деревянными машинками и настоящим подъёмным краном, придуманная в Яндексе настоящими родителями.', '6.jpg');
INSERT INTO tovar (id, artikul, name, id_ed_izm, price, id_postavshik, id_proizvoditel, id_kategoria, skidka, kol_vo, opisanie, foto) VALUES (7, 'S72AM3', 'Синтезатор детский с микрофоном 61 клавиша', 1, 1749, 4, 3, 3, 10, 35, 'Откройте для ребенка дверь в мир музыки с детским синтезатором! Этот компактный инструмент с микрофоном станет верным другом для юных музыкантов, помогая им развивать творческий потенциал и получать удовольствие от игры.', '7.jpg');
INSERT INTO tovar (id, artikul, name, id_ed_izm, price, id_postavshik, id_proizvoditel, id_kategoria, skidka, kol_vo, opisanie, foto) VALUES (8, '2G3280', 'Деревянный игровой набор JUNION Стройплощадка "Кран-Паркс" с подъёмным, строительным краном и машинками, 18 предметов, подвижные элементы', 1, 1624, 5, 3, 1, 9, 20, 'Игровой набор «Стройплощадка Кран-Паркс Junion» — это большая игрушечная парковка с деревянными машинками и настоящим подъёмным краном, придуманная в Яндексе настоящими родителями.', '8.jpg');
INSERT INTO tovar (id, artikul, name, id_ed_izm, price, id_postavshik, id_proizvoditel, id_kategoria, skidka, kol_vo, opisanie, foto) VALUES (9, 'MIO8YV', 'Музыкальная игрушка интерактивная Пульт, детский прорезыватель для малышей', 1, 305, 5, 2, 3, 9, 31, 'Музыкальная игрушка интерактивная Пульт, детский прорезыватель для малышей', '9.jpg');
INSERT INTO tovar (id, artikul, name, id_ed_izm, price, id_postavshik, id_proizvoditel, id_kategoria, skidka, kol_vo, opisanie, foto) VALUES (10, 'UER2QD', 'Большой набор опытов и экспериментов для детей 14 в 1', 1, 2506, 5, 2, 1, 8, 27, 'Большой набор опытов и экспериментов для детей 14 в 1', '10.jpg');

INSERT INTO zakaz (id, start_date, finish_date, id_punkt_vidachi, id_client, id_status, kod_polucheniya) VALUES (1, DATE '2025-02-27', DATE '2025-04-20', 1, 7, 1, 901);
INSERT INTO zakaz (id, start_date, finish_date, id_punkt_vidachi, id_client, id_status, kod_polucheniya) VALUES (2, DATE '2024-09-28', DATE '2025-04-21', 11, 8, 1, 902);
INSERT INTO zakaz (id, start_date, finish_date, id_punkt_vidachi, id_client, id_status, kod_polucheniya) VALUES (3, DATE '2025-03-21', DATE '2025-04-22', 2, 9, 1, 903);
INSERT INTO zakaz (id, start_date, finish_date, id_punkt_vidachi, id_client, id_status, kod_polucheniya) VALUES (4, DATE '2025-02-20', DATE '2025-04-23', 11, 10, 1, 904);
INSERT INTO zakaz (id, start_date, finish_date, id_punkt_vidachi, id_client, id_status, kod_polucheniya) VALUES (5, DATE '2025-03-17', DATE '2025-04-24', 2, 7, 1, 905);
INSERT INTO zakaz (id, start_date, finish_date, id_punkt_vidachi, id_client, id_status, kod_polucheniya) VALUES (6, DATE '2025-03-01', DATE '2025-04-25', 15, 8, 1, 906);
INSERT INTO zakaz (id, start_date, finish_date, id_punkt_vidachi, id_client, id_status, kod_polucheniya) VALUES (7, DATE '2025-02-28', DATE '2025-04-26', 3, 9, 1, 907);
INSERT INTO zakaz (id, start_date, finish_date, id_punkt_vidachi, id_client, id_status, kod_polucheniya) VALUES (8, DATE '2025-03-31', DATE '2025-04-27', 19, 10, 2, 908);
INSERT INTO zakaz (id, start_date, finish_date, id_punkt_vidachi, id_client, id_status, kod_polucheniya) VALUES (9, DATE '2025-04-02', DATE '2025-04-28', 5, 9, 2, 909);
INSERT INTO zakaz (id, start_date, finish_date, id_punkt_vidachi, id_client, id_status, kod_polucheniya) VALUES (10, DATE '2025-04-03', DATE '2025-04-29', 19, 10, 2, 910);

INSERT INTO zakaz_tovar (id, id_zakaz, id_tovar, kol_vo) VALUES (1, 1, 1, 2);
INSERT INTO zakaz_tovar (id, id_zakaz, id_tovar, kol_vo) VALUES (2, 1, 2, 2);
INSERT INTO zakaz_tovar (id, id_zakaz, id_tovar, kol_vo) VALUES (3, 2, 3, 1);
INSERT INTO zakaz_tovar (id, id_zakaz, id_tovar, kol_vo) VALUES (4, 2, 4, 1);
INSERT INTO zakaz_tovar (id, id_zakaz, id_tovar, kol_vo) VALUES (5, 3, 5, 10);
INSERT INTO zakaz_tovar (id, id_zakaz, id_tovar, kol_vo) VALUES (6, 3, 6, 10);
INSERT INTO zakaz_tovar (id, id_zakaz, id_tovar, kol_vo) VALUES (7, 4, 7, 5);
INSERT INTO zakaz_tovar (id, id_zakaz, id_tovar, kol_vo) VALUES (8, 4, 8, 4);
INSERT INTO zakaz_tovar (id, id_zakaz, id_tovar, kol_vo) VALUES (9, 5, 9, 2);
INSERT INTO zakaz_tovar (id, id_zakaz, id_tovar, kol_vo) VALUES (10, 5, 10, 2);
INSERT INTO zakaz_tovar (id, id_zakaz, id_tovar, kol_vo) VALUES (11, 6, 1, 2);
INSERT INTO zakaz_tovar (id, id_zakaz, id_tovar, kol_vo) VALUES (12, 6, 2, 2);
INSERT INTO zakaz_tovar (id, id_zakaz, id_tovar, kol_vo) VALUES (13, 7, 3, 1);
INSERT INTO zakaz_tovar (id, id_zakaz, id_tovar, kol_vo) VALUES (14, 7, 4, 1);
INSERT INTO zakaz_tovar (id, id_zakaz, id_tovar, kol_vo) VALUES (15, 8, 5, 10);
INSERT INTO zakaz_tovar (id, id_zakaz, id_tovar, kol_vo) VALUES (16, 8, 6, 10);
INSERT INTO zakaz_tovar (id, id_zakaz, id_tovar, kol_vo) VALUES (17, 9, 7, 5);
INSERT INTO zakaz_tovar (id, id_zakaz, id_tovar, kol_vo) VALUES (18, 9, 8, 4);
INSERT INTO zakaz_tovar (id, id_zakaz, id_tovar, kol_vo) VALUES (19, 10, 9, 2);
INSERT INTO zakaz_tovar (id, id_zakaz, id_tovar, kol_vo) VALUES (20, 10, 10, 2);

COMMIT;

<?php

// \$ - экранирование

return [

        'core' => [
                "INSERT INTO settings_pages(
                        id, 
                        name, 
                        title, 
                        description, 
                        h1, 
                        annotation,
                        scripts
                ) VALUES(
                        '1', 
                        'main', 
                        'Главная страница сайта', 
                        'Описание главной страницы сайта', 
                        'Заголовок страницы', 
                        'Добро пожаловать на сайт',
                        'main.js,'
                )"
        ],
        'plugins' => [
                "INSERT INTO settings_plugins(
                        id, 
                        plugin_name, 
                        name, 
                        title, 
                        description, 
                        h1, 
                        annotation, 
                        scripts
                ) VALUES(
                        '1', 
                        'users', 
                        'authorization', 
                        'Страница авторизации', 
                        'Описание страницы авторизации', 
                        'Страница авторизации', 
                        'Добро пожаловать!', 
                        'users.min.js,'
                )",
                "INSERT INTO settings_plugins(
                        id, 
                        plugin_name, 
                        name, 
                        title, 
                        description, 
                        h1, 
                        annotation, 
                        scripts
                ) VALUES(
                        '2', 
                        'users', 
                        'registration', 
                        'Страница регистрации', 
                        'Описание страницы регистрации', 
                        'Страница регистрации', 
                        'Добро пожаловать!', 
                        'users.min.js,'
                )",
                "INSERT INTO plugin_users_registered(
                        id, 
                        login, 
                        password, 
                        password_hash
                ) VALUES(
                        '1', 
                        'admin', 
                        'admin', 
                        '$2y$10\$UxZi4pfbxXAoyiawbL4dteGxxtnrjcUYPiNGf0gEUC5nuCW4JrX16'
                )",
                "INSERT INTO plugin_users_secure(
                        user_id, 
                        secret
                ) VALUES(
                        '1', 
                        'd2315af356f82b6574816d84708e'
                )",
                "INSERT INTO plugin_users_personal(
                        user_id, 
                        name, 
                        mail
                ) VALUES(
                        '1', 
                        'Администратор',
                        'admin@mail.ru'
                )",
                "INSERT INTO plugin_users_registered(
                        id, 
                        login, 
                        password, 
                        password_hash
                ) VALUES(
                        '2', 
                        'koder', 
                        'admin', 
                        '$2y$10\$UxZi4pfbxXAoyiawbL4dteGxxtnrjcUYPiNGf0gEUC5nuCW4JrX16'
                )",
                "INSERT INTO plugin_users_secure(
                        user_id, 
                        secret
                ) VALUES(
                        '2', 
                        'd2315af356f82b6574816d84708e'
                )",
                "INSERT INTO plugin_users_personal(
                        user_id, 
                        name, 
                        mail
                ) VALUES(
                        '2', 
                        'Кодер',
                        'koder@mail.ru'
                )",
                "INSERT INTO plugin_users_registered(
                        id, 
                        login, 
                        password, 
                        password_hash
                ) VALUES(
                        '3', 
                        'mihail78', 
                        'admin', 
                        '$2y$10\$UxZi4pfbxXAoyiawbL4dteGxxtnrjcUYPiNGf0gEUC5nuCW4JrX16'
                )",
                "INSERT INTO plugin_users_secure(
                        user_id, 
                        secret
                ) VALUES(
                        '3', 
                        'd2315af356f82b6574816d84708e'
                )",
                "INSERT INTO plugin_users_personal(
                        user_id, 
                        name, 
                        mail
                ) VALUES(
                        '3', 
                        'Михаил',
                        'mickhail@mail.ru'
                )"
        ],
        'content' => [
                "INSERT INTO articles(
                        id, 
                        text
                ) VALUES(
                        '1', 
                        'Model-View-Controller – это фундаментальный паттерн, который нашел применение во многих технологиях, дал развитие новым технологиям и каждый день облегчает жизнь программистам.'
                )",
                "INSERT INTO articles(
                        id, 
                        text
                ) VALUES(
                        '2', 
                        'MVC— описывает простой способ построения структуры приложения, целью которого является отделение бизнес-логики от пользовательского интерфейса.'
                )",
                "INSERT INTO comments(
                        user_id, 
                        article_id, 
                        text, 
                        date
                ) VALUES(
                        '3', 
                        '1', 
                        'Удобная структура позволяет использовать только те функции, что необходимы в данный момент.', 
                        '2008-01-10'
                )",
                "INSERT INTO comments(
                        user_id, 
                        article_id, 
                        text, 
                        date
                ) VALUES(
                        '2', 
                        '1', 
                        'В отличии от популярных фреймворков, MVC имеет малый размер и поэтому скорость загрузки сайтов выше.', 
                        '2008-02-10'
                )",
                "INSERT INTO comments(
                        user_id, 
                        article_id, 
                        text, 
                        date
                ) VALUES(
                        '3', 
                        '1', 
                        'Удобно реализовывать собственные бэкенд решения.', 
                        '2008-03-10'
                )",
                "INSERT INTO comments(
                        user_id, 
                        article_id, 
                        text, 
                        date
                ) VALUES(
                        '2', 
                        '1', 
                        'Реализация полиморфизма, значительно сокращает код, автоматизируя и оптимизируя процессы.', 
                        '2008-04-10'
                )",
                "INSERT INTO comments(
                        user_id, 
                        article_id, 
                        text, 
                        date
                ) VALUES(
                        '3', 
                        '1', 
                        'Быстрый запуск и удобная настройка.', 
                        '2008-05-10'
                )",
                "INSERT INTO comments(
                        user_id, 
                        article_id, 
                        text, 
                        date
                ) VALUES(
                        '2', 
                        '1', 
                        'Продуманная архитектура и современные технологические решения.', 
                        '2008-06-10'
                )",
                "INSERT INTO comments(
                        user_id, 
                        article_id, 
                        text, 
                        date
                ) VALUES(
                        '3', 
                        '1', 
                        'С помощью MVC можно реализовать самые крутые проекты.', 
                        '2008-07-10'
                )",
                "INSERT INTO comments(
                        user_id, 
                        article_id, 
                        text, 
                        date
                ) VALUES(
                        '3', 
                        '2', 
                        'Фреймворки с MVC заняли заметные позиции по отношению к фреймворкам без MVC.', 
                        '2008-07-10'
                )",
                "INSERT INTO comments(
                        user_id, 
                        article_id, 
                        text, 
                        date
                ) VALUES(
                        '2', 
                        '2', 
                        'Основная цель применения этой концепции состоит в отделении бизнес-логики от её визуализации.', 
                        '2008-07-11'
                )"
        ]
];
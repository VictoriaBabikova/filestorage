-- phpMyAdmin SQL Dump
-- version 5.1.1deb5ubuntu1
-- https://www.phpmyadmin.net/
--
-- Хост: localhost:3306
-- Время создания: Фев 24 2023 г., 17:33
-- Версия сервера: 8.0.32-0ubuntu0.22.04.2
-- Версия PHP: 8.1.2-1ubuntu2.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `filestorage`
--

-- --------------------------------------------------------

--
-- Структура таблицы `Directories`
--

CREATE TABLE `Directories` (
  `id` int NOT NULL,
  `name_dir` varchar(250) NOT NULL,
  `path_dir` varchar(250) NOT NULL,
  `user_id` int NOT NULL,
  `date_create_dir` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `Directories`
--

INSERT INTO `Directories` (`id`, `name_dir`, `path_dir`, `user_id`, `date_create_dir`) VALUES
(5, 'cats', '/var/www/filestorage/storage/cats', 6, '2023-02-22 19:58:52'),
(6, 'pets', '/var/www/filestorage/storage/pets', 6, '2023-02-23 14:51:17');

-- --------------------------------------------------------

--
-- Структура таблицы `Files`
--

CREATE TABLE `Files` (
  `id` int NOT NULL,
  `name_file` varchar(250) NOT NULL,
  `path_file` varchar(250) NOT NULL,
  `user_id` int NOT NULL,
  `date_create` datetime NOT NULL,
  `list_user_access` json NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `Files`
--

INSERT INTO `Files` (`id`, `name_file`, `path_file`, `user_id`, `date_create`, `list_user_access`) VALUES
(5, '111111.jpg', '/var/www/filestorage/storage/pets/111111.jpg', 6, '2023-02-24 15:51:35', '[\"6\"]');

-- --------------------------------------------------------

--
-- Структура таблицы `Users`
--

CREATE TABLE `Users` (
  `id` int NOT NULL,
  `email` varchar(180) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `first_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `Users`
--

INSERT INTO `Users` (`id`, `email`, `password`, `role`, `first_name`) VALUES
(2, 'mail@gmail.com', '$2y$10$RebQ17bi0FiubCiQBouyAOi.b8JFLL47oY.DRpBMVzsZT5wMF.n6O', 'ROLE_USER', 'Vikenty'),
(6, 'babikovav8@gmail.com', '$2y$10$ADsoL2AWJ83dhPCaCLT8P.2Psg4fT7NQZ9UcOwqp6BokUaSzAQiam', 'ROLE_ADMIN', 'Vika'),
(8, 'mail13@gmail.com', '$2y$10$g28Q2oWPz/rPhddcuR7oOeJD63Aw1A4ROaezslTokEDPE6QzmfYQa', 'ROLE_USER', 'pety'),
(10, 'user@mail.com', '$2y$10$aa/WQXxFk1xGXlGpzDQIv.h3teHELVBQnYkk/aOW9MvVJDw9Mxj9O', 'ROLE_USER', 'Ivan');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `Directories`
--
ALTER TABLE `Directories`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `Files`
--
ALTER TABLE `Files`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `Users`
--
ALTER TABLE `Users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `Directories`
--
ALTER TABLE `Directories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT для таблицы `Files`
--
ALTER TABLE `Files`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблицы `Users`
--
ALTER TABLE `Users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

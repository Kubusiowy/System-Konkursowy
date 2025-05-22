-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Maj 23, 2025 at 12:11 AM
-- Wersja serwera: 10.4.32-MariaDB
-- Wersja PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `konkurs`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `login` varchar(50) NOT NULL,
  `haslo` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `login`, `haslo`) VALUES
(1, 'FirekKefirek', '$2y$10$6Fu4MR/8ykCIDGuml8OPNelquEArtur/66Uw9fPwb.UwPZNMYDxh.');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `jury`
--

CREATE TABLE `jury` (
  `id` int(11) NOT NULL,
  `imie` varchar(50) DEFAULT NULL,
  `nazwisko` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jury`
--

INSERT INTO `jury` (`id`, `imie`, `nazwisko`) VALUES
(3, 'Andrzej', 'Kaminski'),
(4, 'Renata', 'Kowalczyk'),
(5, 'Zbigniew', 'Malec');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `kategorie`
--

CREATE TABLE `kategorie` (
  `id` int(11) NOT NULL,
  `nazwa` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kategorie`
--

INSERT INTO `kategorie` (`id`, `nazwa`) VALUES
(2, 'Obsługo goscia w recepcji'),
(3, 'Przygotowanie pokoju Hotelowego'),
(4, 'Wiedza teoretyczna ');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `kryteria`
--

CREATE TABLE `kryteria` (
  `id` int(11) NOT NULL,
  `kategoria_id` int(11) NOT NULL,
  `nazwa` varchar(100) NOT NULL,
  `maks_punkty` int(11) NOT NULL DEFAULT 10
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kryteria`
--

INSERT INTO `kryteria` (`id`, `kategoria_id`, `nazwa`, `maks_punkty`) VALUES
(4, 2, 'Komunikacja i powitanie', 10),
(5, 2, 'szybkosc i poprawnosc', 10),
(6, 2, 'znajomosc jezyka', 10),
(7, 2, 'obsl. sytuacji nietypowych', 10),
(8, 3, 'Czystosc i estetyka', 10),
(9, 3, 'Poprawne ścielenie', 10),
(10, 3, 'Ulozenie recznikow', 10),
(11, 3, 'Ubior', 10),
(12, 4, 'znajomsoc systemow', 10),
(13, 4, 'standardy hotelowe', 10),
(14, 4, 'BHP I RODO', 10),
(15, 4, 'wiedza o obiekcie', 10);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `oceny`
--

CREATE TABLE `oceny` (
  `id` int(11) NOT NULL,
  `uczestnik_id` int(11) NOT NULL,
  `juror_id` int(11) NOT NULL,
  `kryterium_id` int(11) NOT NULL,
  `punkty` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `oceny`
--

INSERT INTO `oceny` (`id`, `uczestnik_id`, `juror_id`, `kryterium_id`, `punkty`) VALUES
(10, 10, 3, 4, 2),
(11, 10, 3, 5, 3),
(12, 10, 3, 6, 4),
(13, 10, 3, 7, 3),
(14, 10, 3, 8, 2),
(15, 10, 3, 9, 3),
(16, 10, 3, 10, 1),
(17, 10, 3, 11, 2),
(18, 10, 3, 12, 1),
(19, 10, 3, 13, 4),
(20, 10, 3, 14, 3),
(21, 10, 3, 15, 2),
(26, 7, 3, 4, 5),
(27, 7, 3, 5, 4),
(28, 7, 3, 6, 8),
(29, 7, 3, 7, 4),
(30, 7, 4, 4, 3),
(31, 7, 4, 5, 4),
(32, 7, 4, 6, 7),
(33, 7, 4, 7, 6);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `uczestnicy`
--

CREATE TABLE `uczestnicy` (
  `id` int(11) NOT NULL,
  `imie` varchar(50) DEFAULT NULL,
  `nazwisko` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `uczestnicy`
--

INSERT INTO `uczestnicy` (`id`, `imie`, `nazwisko`) VALUES
(3, 'kacper', 'Nowicki'),
(4, 'Julia', 'Mazur'),
(5, 'tymek', 'Pawlak'),
(6, 'Zuzia', 'Krajewska'),
(7, 'Oskar', 'Domanski'),
(8, 'Natalia', 'Zielinska'),
(9, 'Bartek', 'Szymanski'),
(10, 'Mateusz', 'Bąk'),
(11, 'Amelia', 'Wozniak'),
(12, 'Igor', 'Malinowski'),
(13, 'Amelia', 'Krajniak');

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login` (`login`);

--
-- Indeksy dla tabeli `jury`
--
ALTER TABLE `jury`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `kategorie`
--
ALTER TABLE `kategorie`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `kryteria`
--
ALTER TABLE `kryteria`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kategoria_id` (`kategoria_id`);

--
-- Indeksy dla tabeli `oceny`
--
ALTER TABLE `oceny`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq` (`uczestnik_id`,`juror_id`,`kryterium_id`),
  ADD KEY `juror_id` (`juror_id`),
  ADD KEY `kryterium_id` (`kryterium_id`);

--
-- Indeksy dla tabeli `uczestnicy`
--
ALTER TABLE `uczestnicy`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `jury`
--
ALTER TABLE `jury`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `kategorie`
--
ALTER TABLE `kategorie`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `kryteria`
--
ALTER TABLE `kryteria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `oceny`
--
ALTER TABLE `oceny`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `uczestnicy`
--
ALTER TABLE `uczestnicy`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `kryteria`
--
ALTER TABLE `kryteria`
  ADD CONSTRAINT `kryteria_ibfk_1` FOREIGN KEY (`kategoria_id`) REFERENCES `kategorie` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `oceny`
--
ALTER TABLE `oceny`
  ADD CONSTRAINT `oceny_ibfk_1` FOREIGN KEY (`uczestnik_id`) REFERENCES `uczestnicy` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `oceny_ibfk_2` FOREIGN KEY (`juror_id`) REFERENCES `jury` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `oceny_ibfk_3` FOREIGN KEY (`kryterium_id`) REFERENCES `kryteria` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

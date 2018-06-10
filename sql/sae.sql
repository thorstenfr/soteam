-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 10. Jun 2018 um 22:46
-- Server-Version: 10.1.28-MariaDB
-- PHP-Version: 7.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: sae
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle sae_aufgabe
--

CREATE TABLE sae_aufgabe (
  auf_id int(11) NOT NULL,
  auf_kurz varchar(5) NOT NULL,
  auf_beschreibung varchar(255) NOT NULL,
  auf_daueraufgabe int(11) NOT NULL COMMENT '1 = Daueraufgabe',
  auf_created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  auf_updated_at timestamp NULL DEFAULT NULL,
  auf_beendet_am timestamp NULL DEFAULT NULL,
  sae_tae_fk int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle sae_buchung
--

CREATE TABLE sae_buchung (
  buc_id int(11) NOT NULL,
  buc_created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  buc_wert int(11) NOT NULL,
  buc_kommentar varchar(255) DEFAULT NULL,
  users_id int(11) UNSIGNED NOT NULL,
  sae_aufgabe_auf_id int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle sae_taetigkeit
--

CREATE TABLE sae_taetigkeit (
  tae_id int(11) NOT NULL,
  tae_kuerzel varchar(5) NOT NULL,
  tae_bezeichnung varchar(45) NOT NULL,
  tae_langbezeichnung varchar(200) DEFAULT NULL,
  tea_created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  tae_updated_at timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle securitytokens
--

CREATE TABLE securitytokens (
  id int(10) UNSIGNED NOT NULL,
  user_id int(10) NOT NULL,
  identifier varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  securitytoken varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle tmp_buchung
--

CREATE TABLE tmp_buchung (
  tmp_user_id int(11) NOT NULL,
  tmp_user_nick varchar(5) NOT NULL,
  tmp_heute int(11) NOT NULL,
  tmp_woche int(11) NOT NULL,
  tmp_monat int(11) NOT NULL,
  tmp_jahr int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle users
--

CREATE TABLE users (
  id int(10) UNSIGNED NOT NULL,
  email varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  passwort varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  vorname varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  nachname varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at timestamp NULL DEFAULT NULL,
  passwortcode varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  passwortcode_time timestamp NULL DEFAULT NULL,
  nick varchar(5) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle users_has_sae_aufgabe
--

CREATE TABLE users_has_sae_aufgabe (
  users_id int(10) UNSIGNED NOT NULL,
  sae_aufgabe_auf_id int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle sae_aufgabe
--
ALTER TABLE sae_aufgabe
  ADD PRIMARY KEY (auf_id),
  ADD KEY gehoert_zu (sae_tae_fk);

--
-- Indizes für die Tabelle sae_buchung
--
ALTER TABLE sae_buchung
  ADD PRIMARY KEY (buc_id),
  ADD KEY user_add (users_id),
  ADD KEY fk_sae_buchung_sae_aufgabe1_idx (sae_aufgabe_auf_id);

--
-- Indizes für die Tabelle sae_taetigkeit
--
ALTER TABLE sae_taetigkeit
  ADD PRIMARY KEY (tae_id);

--
-- Indizes für die Tabelle securitytokens
--
ALTER TABLE securitytokens
  ADD PRIMARY KEY (id);

--
-- Indizes für die Tabelle tmp_buchung
--
ALTER TABLE tmp_buchung
  ADD PRIMARY KEY (tmp_user_id);

--
-- Indizes für die Tabelle users
--
ALTER TABLE users
  ADD PRIMARY KEY (id),
  ADD UNIQUE KEY email (email);

--
-- Indizes für die Tabelle users_has_sae_aufgabe
--
ALTER TABLE users_has_sae_aufgabe
  ADD PRIMARY KEY (users_id,sae_aufgabe_auf_id),
  ADD KEY fk_users_has_sae_aufgabe_sae_aufgabe1_idx (sae_aufgabe_auf_id),
  ADD KEY fk_users_has_sae_aufgabe_users1_idx (users_id);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle sae_aufgabe
--
ALTER TABLE sae_aufgabe
  MODIFY auf_id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT für Tabelle sae_buchung
--
ALTER TABLE sae_buchung
  MODIFY buc_id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;

--
-- AUTO_INCREMENT für Tabelle sae_taetigkeit
--
ALTER TABLE sae_taetigkeit
  MODIFY tae_id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT für Tabelle securitytokens
--
ALTER TABLE securitytokens
  MODIFY id int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT für Tabelle users
--
ALTER TABLE users
  MODIFY id int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle sae_aufgabe
--
ALTER TABLE sae_aufgabe
  ADD CONSTRAINT gehoert_zu FOREIGN KEY (sae_tae_fk) REFERENCES sae_taetigkeit (tae_id);

--
-- Constraints der Tabelle sae_buchung
--
ALTER TABLE sae_buchung
  ADD CONSTRAINT fk_sae_buchung_sae_aufgabe1 FOREIGN KEY (sae_aufgabe_auf_id) REFERENCES sae_aufgabe (auf_id) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT user_add FOREIGN KEY (users_id) REFERENCES `users` (id);

--
-- Constraints der Tabelle users_has_sae_aufgabe
--
ALTER TABLE users_has_sae_aufgabe
  ADD CONSTRAINT fk_users_has_sae_aufgabe_sae_aufgabe1 FOREIGN KEY (sae_aufgabe_auf_id) REFERENCES sae_aufgabe (auf_id) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT fk_users_has_sae_aufgabe_users1 FOREIGN KEY (users_id) REFERENCES `users` (id) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

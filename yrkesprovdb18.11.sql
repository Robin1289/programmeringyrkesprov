-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Värd: 127.0.0.1
-- Tid vid skapande: 18 nov 2025 kl 19:00
-- Serverversion: 10.4.32-MariaDB
-- PHP-version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databas: `yrkesprovdb`
--

-- --------------------------------------------------------

--
-- Tabellstruktur `answer`
--

CREATE TABLE `answer` (
  `a_id` int(11) NOT NULL,
  `a_name` varchar(255) NOT NULL,
  `a_iscorrect` tinyint(1) DEFAULT 0,
  `a_q_fk` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumpning av Data i tabell `answer`
--

INSERT INTO `answer` (`a_id`, `a_name`, `a_iscorrect`, `a_q_fk`) VALUES
(1, 'Stänger av väckarklockan', 1, 43),
(2, 'Borstar tänderna', 0, 43),
(3, 'Tar på sig kläder', 0, 43),
(4, 'En smörgås med ost och juice', 1, 44),
(5, 'En skål gröt', 0, 44),
(6, 'Kaffe och kaka', 0, 44),
(7, 'Klockan åtta', 1, 45),
(8, 'Klockan nio', 0, 45),
(9, 'Halv nio', 0, 45),
(10, 'Lyssnar på musik eller pratar med en vän', 1, 46),
(11, 'Sover', 0, 46),
(12, 'Tittar på videor', 0, 46),
(13, 'vakna', 0, 48),
(14, 'äta frukost', 0, 48),
(15, 'klä på sig', 0, 48),
(16, 'gå till busshållplatsen', 0, 48),
(17, 'Falskt', 1, 49),
(18, 'Sant', 0, 49),
(19, 'Klockan sju', 1, 50),
(20, 'Klockan sex', 0, 50),
(21, 'Klockan åtta', 0, 50),
(22, 'Buss', 1, 52),
(23, 'Tåg', 0, 52),
(24, 'Bil', 0, 52),
(25, 'Läser e-post och planerar möten', 1, 53),
(26, 'Dricker kaffe', 0, 53),
(27, 'Pratar med kollegor', 0, 53),
(28, 'På ett kontor i centrum', 1, 54),
(29, 'I en skola', 0, 54),
(30, 'På ett lager', 0, 54),
(31, 'Äter ute med sina vänner', 1, 55),
(32, 'Jobbar vid datorn', 0, 55),
(33, 'Tar en promenad', 0, 55),
(34, 'Skriver en lista över nästa dag', 1, 56),
(35, 'Går hem direkt', 0, 56),
(36, 'Tränar på gymmet', 0, 56),
(37, 'läsa e-post', 0, 58),
(38, 'telefonsamtal', 0, 58),
(39, 'luncha', 0, 58),
(40, 'göra lista', 0, 58),
(41, 'Falskt', 1, 59),
(42, 'Sant', 0, 59),
(43, 'Hon gillar att planera sin dag', 1, 61),
(44, 'Hon hatar att planera', 0, 61),
(45, 'Hon är alltid sen', 0, 61),
(46, 'Genom telefonsamtal', 1, 62),
(47, 'Genom brev', 0, 62),
(48, 'Genom videosamtal', 0, 62),
(49, 'Arbeta hemifrån och hålla kontakt globalt', 1, 63),
(50, 'Resa billigare', 0, 63),
(51, 'Tjäna mer pengar', 0, 63),
(52, 'Informationsöverflöd och skärmberoende', 1, 64),
(53, 'Högre löner', 0, 64),
(54, 'Mindre stress', 0, 64),
(55, 'Falskt', 1, 67),
(56, 'Sant', 0, 67),
(57, 'digitalisering', 0, 68),
(58, 'nya utmaningar', 0, 68),
(59, 'forskarnas oro', 0, 68),
(60, 'framtidens lösning', 0, 68),
(61, 'Tekniken påverkar både positivt och negativt', 1, 71),
(62, 'Tekniken påverkar bara negativt', 0, 71),
(63, 'Tekniken påverkar bara positivt', 0, 71),
(64, 'Med tåg', 1, 73),
(65, 'Med buss', 0, 73),
(66, 'Med bil', 0, 73),
(67, 'Kaffe och bulle', 1, 74),
(68, 'Glass', 0, 74),
(69, 'Sallad', 0, 74),
(70, 'Båtar', 1, 75),
(71, 'Fåglar', 0, 75),
(72, 'Turister', 0, 75),
(73, 'Helsingfors domkyrka', 1, 76),
(74, 'Nationalmuseet', 0, 76),
(75, 'Musikhuset', 0, 76),
(76, 'En souvenir', 1, 77),
(77, 'En bok', 0, 77),
(78, 'En tröja', 0, 77),
(79, 'Åbo', 1, 78),
(80, 'Tammerfors', 0, 78),
(81, 'Vasa', 0, 78),
(82, 'Falskt', 1, 79),
(83, 'Sant', 0, 79),
(84, 'Lördagsmorgonen', 1, 81),
(85, 'Måndag morgon', 0, 81),
(86, 'Söndag kväll', 0, 81),
(87, 'Läste om företaget och övade frågor', 1, 84),
(88, 'Sov hela dagen', 0, 84),
(89, 'Spelade spel', 0, 84),
(90, 'Nervös och förväntansfull', 1, 85),
(91, 'Arg och stressad', 0, 85),
(92, 'Lugn och obrydd', 0, 85),
(93, 'Bra', 1, 87),
(94, 'Dåligt', 0, 87),
(95, 'Okej', 0, 87),
(96, 'Nej', 1, 88),
(97, 'Ja', 0, 88),
(98, 'Falskt', 1, 89),
(99, 'Sant', 0, 89),
(100, 'Två dagar senare', 1, 90),
(101, 'En vecka senare', 0, 90),
(102, 'Nästa dag', 0, 90),
(103, 'Solkraft och vindkraft', 1, 93),
(104, 'Kol och olja', 0, 93),
(105, 'Kärnkraft', 0, 93),
(106, 'Politiska och ekonomiska problem', 1, 94),
(107, 'Inga hinder', 0, 94),
(108, 'Tekniska förbättringar', 0, 94),
(109, 'Beroendet av fossila bränslen', 1, 96),
(110, 'Beroendet av förnybar energi', 0, 96),
(111, 'Beroendet av elbilar', 0, 96),
(112, 'Falskt', 1, 97),
(113, 'Sant', 0, 97),
(114, 'Hon gick ner mot åstranden', 1, 103),
(115, 'Hon åt frukost hemma', 0, 103),
(116, 'Hon åkte till slottet', 0, 103),
(117, 'Handgjorda träleksaker', 1, 104),
(118, 'Jordgubbar', 0, 104),
(119, 'Smycken', 0, 104),
(120, 'Friluftsliv', 1, 105),
(121, 'Industrialisering', 0, 105),
(122, 'Stadsutveckling', 0, 105);

-- --------------------------------------------------------

--
-- Tabellstruktur `badge`
--

CREATE TABLE `badge` (
  `b_id` int(11) NOT NULL,
  `b_name` varchar(100) NOT NULL,
  `b_description` text DEFAULT NULL,
  `b_condition` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumpning av Data i tabell `badge`
--

INSERT INTO `badge` (`b_id`, `b_name`, `b_description`, `b_condition`) VALUES
(3, 'Första quizet', 'Har klarat ditt första quiz!', 'r_completed = 1'),
(4, 'Toppscore', 'Fått över 90 poäng totalt', 'u_points >= 90'),
(5, 'Snabb läsare', 'Läst alla texter på nivå 1', 'u_level_fk >= 1'),
(6, 'Quiz Novis', 'Genomfört tre quiz.', 'COUNT(r_id) >= 3'),
(7, 'Felfri Hjälte', 'Klarat ett quiz med 100% rätt.', 'r_score = (SELECT SUM(q_points) FROM question WHERE q_id IN (SELECT qq_question_fk FROM quiz_questions WHERE qq_quiz_fk = r_quiz_fk))'),
(8, 'Läsproffset', 'Svarat korrekt på fem textfrågor i rad.', 'sa_iscorrect = 1 AND q_type = \"text\"'),
(9, 'Snabbtänkt', 'Slutfört ett quiz inom 2 minuter.', 'TIMESTAMPDIFF(SECOND, start_time, r_date) < 120'),
(10, 'Stigande Stjärna', 'Nått level 5.', 'u_level_fk >= 5'),
(11, 'Mästare av Läsförståelse', 'Genomfört alla läsförståelsequiz på din nivå.', 'r_completed = 1');

-- --------------------------------------------------------

--
-- Tabellstruktur `level`
--

CREATE TABLE `level` (
  `l_id` int(11) NOT NULL,
  `l_name` varchar(50) NOT NULL,
  `l_min_points` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumpning av Data i tabell `level`
--

INSERT INTO `level` (`l_id`, `l_name`, `l_min_points`) VALUES
(4, 'Beginner', 0),
(5, 'Intermediate', 50),
(6, 'Advanced', 120),
(7, 'Expert', 250),
(8, 'Master', 500),
(9, 'Grandmaster', 900),
(10, 'Legend', 1500),
(11, 'Mythic', 2300),
(12, 'Immortal', 3300),
(13, 'Transcendent', 5000);

-- --------------------------------------------------------

--
-- Tabellstruktur `level_unlocks`
--

CREATE TABLE `level_unlocks` (
  `lu_id` int(11) NOT NULL,
  `lu_level_fk` int(11) NOT NULL,
  `lu_quiz_fk` int(11) DEFAULT NULL,
  `lu_badge_fk` int(11) DEFAULT NULL,
  `lu_description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumpning av Data i tabell `level_unlocks`
--

INSERT INTO `level_unlocks` (`lu_id`, `lu_level_fk`, `lu_quiz_fk`, `lu_badge_fk`, `lu_description`) VALUES
(9, 4, 6, NULL, 'Lås upp detta quiz på nybörjarnivå.'),
(10, 4, 7, NULL, 'Tillgängligt för alla på nybörjarnivån.'),
(11, 5, 8, NULL, 'Lås upp när du når mellannivå.'),
(12, 5, 9, NULL, 'För elever som kommit en bit på vägen.'),
(13, 6, 10, NULL, 'Endast tillgängligt för avancerade studenter.'),
(14, 6, 11, NULL, 'För elever med hög nivå av läsförståelse.'),
(15, 4, 12, NULL, 'Tillgängligt för nybörjarnivå.'),
(16, 5, 13, NULL, 'Lås upp när du når mellannivån.'),
(17, 6, 14, NULL, 'För avancerade elever.'),
(18, 7, 15, NULL, 'Endast tillgängligt för expertstudenter.'),
(19, 8, 16, NULL, 'För masterstudenter.'),
(20, 9, 17, NULL, 'Endast för grandmasters.'),
(21, 10, 18, NULL, 'Tillgängligt på legendnivån.');

-- --------------------------------------------------------

--
-- Tabellstruktur `match_pair`
--

CREATE TABLE `match_pair` (
  `mp_id` int(11) NOT NULL,
  `mp_question_fk` int(11) NOT NULL,
  `mp_left_text` varchar(255) NOT NULL,
  `mp_right_text` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumpning av Data i tabell `match_pair`
--

INSERT INTO `match_pair` (`mp_id`, `mp_question_fk`, `mp_left_text`, `mp_right_text`) VALUES
(1, 106, 'Stänger av väckarklockan', 'Det första som händer'),
(2, 106, 'Äter frukost', 'Smörgås med ost och juice'),
(3, 106, 'Tar bussen', 'Skolan börjar klockan åtta'),
(4, 107, 'Läser e-post', 'På morgonen'),
(5, 107, 'Äter lunch', 'Mitt på dagen'),
(6, 107, 'Skriver lista', 'Efter arbetsdagen'),
(7, 108, 'Digitalisering', 'Arbeta hemifrån'),
(8, 108, 'Skärmberoende', 'Negativ effekt'),
(9, 108, 'Forskarnas oro', 'Påverkar hälsa och koncentration'),
(10, 109, 'Kaffe och bulle', 'Salutorget'),
(11, 109, 'Båtar', 'Hamnen'),
(12, 109, 'Souvenir', 'Till lillebrodern'),
(13, 110, 'Förberedelser', 'Läsa om företaget'),
(14, 110, 'Intervjutillfället', 'Berätta om erfarenheter'),
(15, 110, 'Efter intervjun', 'Får besked två dagar senare'),
(16, 111, 'Fossila bränslen', 'Minskas genom internationellt samarbete'),
(17, 111, 'Solkraft och vindkraft', 'Hållbara lösningar'),
(18, 111, 'Globala utmaningar', 'Politiska och ekonomiska problem'),
(19, 112, 'Frukost', 'Kaffe och bröd'),
(20, 112, 'Kvällsaktivitet', 'Bastu eller promenad'),
(21, 112, 'Helg', 'Stugan och naturen'),
(22, 113, 'Teori', 'Klassrum'),
(23, 113, 'Praktik', 'Verkstad'),
(24, 113, 'LIA', 'Företag'),
(25, 114, 'Phishing', 'Falska länkar'),
(26, 114, 'Tvåfaktorsinloggning', 'Extra säkerhet'),
(27, 114, 'Starka lösenord', 'Skyddar konton'),
(28, 115, 'Återvinna', 'Minska avfall'),
(29, 115, 'Sänka värmen', 'Sparar energi'),
(30, 115, 'Köpa begagnat', 'Minskar konsumtion'),
(31, 116, 'Kalmarunionen', 'Sverige och Danmark'),
(32, 116, 'Storfurstendömet', 'Finland under Ryssland'),
(33, 116, 'Självständighet 1917', 'Finsk identitet'),
(34, 117, 'Digital handel', 'Köpa online'),
(35, 117, 'Köpimpulser', 'Överkonsumtion'),
(36, 117, 'Inflation', 'Minskad köpkraft'),
(37, 118, 'Global konkurrens', 'Internationella företag'),
(38, 118, 'Automatisering', 'Förändrar yrkesroller'),
(39, 118, 'Billigare arbetskraft', 'Jobb flyttas utomlands'),
(40, 119, 'Varm sommardag', 'Ovanligt väder'),
(41, 119, 'Händelserik dag', 'Många aktiviteter'),
(42, 119, 'Åbo', 'Staden'),
(43, 120, 'Vandring', 'Natur och fjäll'),
(44, 120, 'Fiske', 'Traditionell aktivitet'),
(45, 120, 'Naturen som balans', 'Minskar stress');

-- --------------------------------------------------------

--
-- Tabellstruktur `question`
--

CREATE TABLE `question` (
  `q_id` int(11) NOT NULL,
  `q_name` varchar(255) NOT NULL,
  `q_points` int(11) DEFAULT 1,
  `q_type` enum('multiple','single','text','sort','match') DEFAULT NULL,
  `q_correct_text` varchar(255) DEFAULT NULL,
  `q_min_level_fk` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumpning av Data i tabell `question`
--

INSERT INTO `question` (`q_id`, `q_name`, `q_points`, `q_type`, `q_correct_text`, `q_min_level_fk`) VALUES
(43, 'Vad gör personen först på morgonen?', 1, 'multiple', 'Stänger av väckarklockan', 1),
(44, 'Vad äter personen till frukost?', 1, 'multiple', 'En smörgås med ost och juice', 1),
(45, 'När börjar skolan?', 1, 'single', 'Klockan åtta', 1),
(46, 'Vad gör personen på vägen till skolan?', 1, 'multiple', 'Lyssnar på musik eller pratar med en vän', 1),
(47, 'Varför är morgonen viktig för personen?', 1, 'text', 'För att börja dagen lugnt och känna sig redo', 1),
(48, 'Ordna aktiviteterna i rätt kronologisk ordning: vakna, äta frukost, klä på sig, gå till busshållplatsen.', 1, 'sort', 'vakna, äta frukost, klä på sig, gå till busshållplatsen', 1),
(49, 'Sant eller falskt: Personen äter alltid gröt till frukost.', 1, 'single', 'Falskt', 1),
(50, 'Vilken tid vaknar personen?', 1, 'single', 'Klockan sju', 1),
(51, 'Vad innebär uttrycket \"redo för lektionerna\" i textens sammanhang?', 1, 'text', 'Att vara förberedd och vaken', 1),
(52, 'Vilket transportmedel använder personen till skolan?', 1, 'single', 'Buss', 1),
(53, 'Vad gör Anna först på morgonen?', 1, 'multiple', 'Läser e-post och planerar möten', 2),
(54, 'Var arbetar Anna?', 1, 'single', 'På ett kontor i centrum', 2),
(55, 'Vad gör Anna under lunchen?', 1, 'multiple', 'Äter ute med sina vänner', 2),
(56, 'Vad gör Anna efter arbetsdagen?', 1, 'single', 'Skriver en lista över nästa dag', 2),
(57, 'Varför tycker Anna att det är viktigt att vara organiserad?', 1, 'text', 'För att planera bättre och arbeta effektivt', 2),
(58, 'Placera momenten i Annas arbetsdag i rätt ordning: läsa e-post, telefonsamtal, luncha, skriva lista för nästa dag.', 1, 'sort', 'läsa e-post, luncha, telefonsamtal, göra lista', 2),
(59, 'Sant eller falskt: Anna arbetar ensam hela dagen.', 1, 'single', 'Falskt', 2),
(60, 'Vad handlar Annas jobb mest om?', 1, 'text', 'Att samarbeta och kommunicera', 2),
(61, 'Vilket av följande är sant om Anna?', 1, 'multiple', 'Hon gillar att planera sin dag', 2),
(62, 'Hur håller Anna kontakt med kunder?', 1, 'single', 'Genom telefonsamtal', 2),
(63, 'Vad har tekniken gjort möjligt enligt texten?', 1, 'multiple', 'Arbeta hemifrån och hålla kontakt globalt', 3),
(64, 'Vilka problem nämns i texten?', 1, 'multiple', 'Informationsöverflöd och skärmberoende', 3),
(65, 'Vad uttrycker forskarna oro över i texten?', 1, 'text', 'Hur teknik påverkar hälsa, koncentration och samarbete', 3),
(66, 'Förklara vad begreppet \"digitalisering\" innebär enligt texten.', 1, 'text', 'Att samhällets tjänster och arbete flyttas till digitala plattformar', 3),
(67, 'Sant eller falskt: Tekniken har bara positiva effekter.', 1, 'single', 'Falskt', 3),
(68, 'Ordna textens huvuddelar i logisk ordning: digitalisering, nya utmaningar, forskarnas oro, framtida lösningar.', 1, 'sort', 'digitalisering, nya utmaningar, forskarnas oro, framtidens lösning', 3),
(69, 'Vilket socialt område påverkas mest av teknikutvecklingen enligt texten?', 1, 'text', 'Hur människor kommunicerar och samarbetar', 3),
(70, 'Vad vill texten uppmana läsaren att tänka på när det gäller teknikens utveckling?', 1, 'text', 'Att vi bör använda teknik på ett medvetet och balanserat sätt', 3),
(71, 'Vilket av följande stämmer bäst?', 1, 'multiple', 'Tekniken påverkar både positivt och negativt', 3),
(72, 'Vilken övergripande slutsats drar texten om framtidens teknik och ansvar?', 1, 'text', 'Att framtidens teknik kräver ansvar och medvetenhet', 3),
(73, 'Hur reste Sara till Helsingfors?', 1, 'multiple', 'Med tåg', 1),
(74, 'Vad köpte hon på Salutorget?', 1, 'multiple', 'Kaffe och bulle', 1),
(75, 'Vad tittade Sara på i hamnen?', 1, 'single', 'Båtar', 1),
(76, 'Vilken byggnad besökte hon?', 1, 'single', 'Helsingfors domkyrka', 1),
(77, 'Vad köpte hon till sin lillebror?', 1, 'multiple', 'En souvenir', 1),
(78, 'Varifrån åkte Sara?', 1, 'single', 'Åbo', 1),
(79, 'Sant eller falskt: Hon stannade i Helsingfors i en vecka.', 1, 'single', 'Falskt', 1),
(80, 'Hur kände hon efter resan?', 1, 'text', 'Att resan var fantastisk', 1),
(81, 'När åkte hon?', 1, 'single', 'Lördagsmorgonen', 1),
(82, 'Var åt hon lunch?', 1, 'text', 'I Helsingfors (ingen exakt plats nämns)', 1),
(83, 'Hur länge hade Markus sökt jobb?', 1, 'text', 'I flera månader', 2),
(84, 'Vad gjorde Markus för att förbereda sig?', 1, 'multiple', 'Läste om företaget och övade frågor', 2),
(85, 'Hur kände han inför intervjun?', 1, 'multiple', 'Nervös och förväntansfull', 2),
(86, 'Vad fick han prata om på intervjun?', 1, 'text', 'Sina erfarenheter och framtida mål', 2),
(87, 'Hur gick intervjun?', 1, 'single', 'Bra', 2),
(88, 'Var han säker på resultatet?', 1, 'single', 'Nej', 2),
(89, 'Sant eller falskt: Han fick jobbet samma dag.', 1, 'single', 'Falskt', 2),
(90, 'När hörde företaget av sig?', 1, 'single', 'Två dagar senare', 2),
(91, 'Hur reagerade Markus på beskedet?', 1, 'text', 'Han blev överlycklig', 2),
(92, 'Vad symboliserar Markus resa?', 1, 'text', 'Uthållighet och förberedelse leder till framgång', 2),
(93, 'Vilka energikällor nämns som framtidslösningar?', 1, 'multiple', 'Solkraft och vindkraft', 3),
(94, 'Vilka hinder finns för energiomställningen?', 1, 'multiple', 'Politiska och ekonomiska problem', 3),
(95, 'Vad gör forskare enligt texten?', 1, 'text', 'Utvecklar ny hållbar energiteknik', 3),
(96, 'Vad försöker länder minska?', 1, 'single', 'Beroendet av fossila bränslen', 3),
(97, 'Sant eller falskt: Omställningen går snabbt.', 1, 'single', 'Falskt', 3),
(98, 'Vad krävs för att omställningen ska lyckas?', 1, 'text', 'Internationellt samarbete och investeringar', 3),
(99, 'Vilken global påverkan nämns?', 1, 'text', 'Ekonomin och klimatet påverkas', 3),
(100, 'Vad är textens huvudbudskap?', 1, 'text', 'Bättre energilösningar kräver globalt samarbete', 3),
(101, 'Vilken roll spelar forskning enligt texten?', 1, 'text', 'Forskning är avgörande för framtida lösningar', 3),
(102, 'Vilken viktig energikälla nämns i texten utöver solkraft och vindkraft?', 1, 'text', 'Avancerade batterier', 3),
(103, 'Vad gjorde Laura direkt efter att hon vaknade?', 5, 'multiple', 'Hon gick ner mot åstranden', 4),
(104, 'Vad sålde den äldre mannen?', 5, 'multiple', 'Handgjorda träleksaker', 4),
(105, 'Vad beskriver texten som en tradition i Norden sedan 1800-talet?', 5, 'multiple', 'Friluftsliv', 4),
(106, 'Matcha morgonrutinen med rätt aktivitet.', 3, 'match', NULL, 1),
(107, 'Matcha Annas aktiviteter med rätt tidpunkt på dagen.', 3, 'match', NULL, 2),
(108, 'Matcha teknikpåståendena med rätt begrepp.', 3, 'match', NULL, 3),
(109, 'Matcha Saras aktiviteter med rätt plats.', 3, 'match', NULL, 1),
(110, 'Matcha intervjuns delar med rätt beskrivning.', 3, 'match', NULL, 2),
(111, 'Matcha energiproblem med rätt lösning.', 3, 'match', NULL, 3),
(112, 'Matcha finska vardagsrutiner med motsvarande beskrivning.', 3, 'match', NULL, 4),
(113, 'Matcha yrkesskolans moment med rätt plats eller aktivitet.', 3, 'match', NULL, 5),
(114, 'Matcha cybersäkerhetsbegreppen med rätt förklaring.', 3, 'match', NULL, 6),
(115, 'Matcha miljövalen med deras effekt.', 3, 'match', NULL, 7),
(116, 'Matcha historiska händelser med rätt land.', 3, 'match', NULL, 8),
(117, 'Matcha konsumtionsmönstren med rätt förklaring.', 3, 'match', NULL, 9),
(118, 'Matcha globaliseringens effekter med rätt område.', 3, 'match', NULL, 10),
(119, 'Matcha händelser från sommardagen i Åbo.', 3, 'match', NULL, 4),
(120, 'Matcha friluftsaktiviteter med rätt beskrivning.', 3, 'match', NULL, 4);

-- --------------------------------------------------------

--
-- Tabellstruktur `quiz`
--

CREATE TABLE `quiz` (
  `quiz_id` int(11) NOT NULL,
  `quiz_name` varchar(255) NOT NULL,
  `quiz_description` text DEFAULT NULL,
  `quiz_iscompleted` tinyint(1) DEFAULT 0,
  `quiz_teacher_fk` int(11) DEFAULT NULL,
  `quiz_min_level_fk` int(11) DEFAULT NULL,
  `quiz_category` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumpning av Data i tabell `quiz`
--

INSERT INTO `quiz` (`quiz_id`, `quiz_name`, `quiz_description`, `quiz_iscompleted`, `quiz_teacher_fk`, `quiz_min_level_fk`, `quiz_category`) VALUES
(6, 'Min Morgonrutin', 'Jag vaknar varje morgon klockan sju. Först stänger jag av väckarklockan och går till badrummet. Jag tvättar ansiktet och borstar tänderna. Efter det äter jag frukost – oftast en smörgås med ost och ett glas juice. Sedan klär jag på mig och går till busshållplatsen. Jag tar bussen till skolan som börjar klockan åtta. På vägen brukar jag lyssna på musik eller prata med min vän. Det är viktigt för mig att börja dagen lugnt så att jag känner mig redo för lektionerna.', 0, 7, 1, NULL),
(7, 'En dag på kontoret', 'Anna arbetar på ett litet kontor i centrum av staden. Hon börjar sin dag med att läsa e-post och planera möten. Hon arbetar ofta tillsammans med sina kollegor för att lösa problem och utveckla nya idéer. Under lunchen brukar hon gå ut för att äta med sina vänner. Efter lunch har hon ofta viktiga telefonsamtal med kunder. När arbetsdagen är slut, skriver hon en lista över vad hon ska göra nästa dag. Anna tycker att det är viktigt att vara organiserad och att hålla kontakt med människor.', 0, 7, 2, NULL),
(8, 'Teknikens påverkan på samhället', 'Under de senaste decennierna har tekniken förändrat hur människor arbetar, kommunicerar och lever. Digitalisering har gjort det möjligt att arbeta hemifrån, studera på distans och hålla kontakt med människor över hela världen. Samtidigt har den tekniska utvecklingen skapat nya utmaningar, som informationsöverflöd och beroende av skärmar. Många forskare diskuterar hur framtidens teknik kan påverka vår psykiska hälsa, vår koncentration och vårt sätt att samarbeta. Det är viktigt att vi lär oss använda tekniken på ett balanserat och medvetet sätt.', 0, 7, 3, NULL),
(9, 'En resa till Helsingfors', 'Sara åkte till Helsingfors för första gången i helgen. Hon tog tåget från Åbo på lördagsmorgonen. När hon kom fram gick hon direkt till Salutorget för att köpa kaffe och en bulle. Hon älskade att titta på båtarna i hamnen. Efter lunchen besökte hon Helsingfors domkyrka och tog många bilder. Innan hon åkte hem köpte hon en souvenir till sin lillebror. Det var en kort resa, men hon tyckte att den var fantastisk.', 0, 7, 1, NULL),
(10, 'Jobbintervjun', 'Markus hade sökt ett nytt jobb i flera månader. När han äntligen fick en intervju var han både nervös och förväntansfull. Han förberedde sig noggrant genom att läsa om företaget, öva på vanliga frågor och välja rätt kläder. Under intervjun fick han berätta om sina tidigare erfarenheter och vad han ville uppnå i framtiden. Intervjun gick bra, men han var osäker på om han gjort tillräckligt bra ifrån sig. Två dagar senare ringde företaget och erbjöd honom jobbet. Markus blev överlycklig.', 0, 7, 2, NULL),
(11, 'Framtidens energi', 'I takt med att världens energibehov ökar blir det allt viktigare att hitta hållbara lösningar. Forskare arbetar intensivt med att utveckla nya tekniker som solkraft, vindkraft och avancerade batterier. Samtidigt finns det globala utmaningar: politiska konflikter, ekonomiska intressen och miljöpåverkan. Många länder försöker minska sitt beroende av fossila bränslen, men omställningen går långsamt. För att lyckas krävs internationellt samarbete och investeringar i forskning. Framtidens energilösningar kommer påverka både ekonomin och klimatet.', 0, 7, 3, NULL),
(12, 'Vardagsrutiner i Finland', 'I Finland varierar vardagsrutinerna något beroende på region, men många gemensamma drag finns. De flesta finländare börjar dagen tidigt, ofta mellan klockan sex och sju. Frukosten är vanligtvis enkel: kaffe, bröd och ibland gröt. Arbetsdagen eller skoldagen startar ofta vid åtta och pågår till mellan tre och fyra på eftermiddagen.\r\n\r\nEfter jobbet ägnar många tid åt sina fritidsintressen. Promenader, gymträning, bastubad och att umgås med familj är vanliga aktiviteter. Bastun har en särskild plats i finländarnas hjärtan; uppskattningsvis finns över tre miljoner bastur i landet — fler än antalet bilar.\r\n\r\nUnder veckan prioriteras ofta struktur och ordning. Många följer noggrant sina scheman, och punktlighet anses vara en dygd. På kvällarna lagar många en varm måltid hemma. Typiska rätter är makaronilåda, köttbullar eller soppor, särskilt under de kallare månaderna.\r\n\r\nUnder helgerna slappnar finländarna ofta av mer. Det är vanligt att spendera tid i stugan, åka skidor på vintern eller simma på sommaren. Trots det kalla klimatet tenderar många att njuta av naturen året runt, eftersom den erbjuder ett lugn som är svårt att hitta i städerna.', 0, 7, 4, 'Läsförståelse'),
(13, 'En dag på yrkesskolan', 'På en finländsk yrkesskola ser dagarna olika ut beroende på vilket program man går. Eleverna blandar teoretiska lektioner med praktiska moment. En elev på el- och automationsteknik kan till exempel börja dagen i klassrummet med genomgångar av elsäkerhet, för att senare flytta till verkstaden där de får arbeta med riktiga komponenter.\r\n\r\nLunch serveras vanligtvis mellan 10:30 och 12:00. Den ingår i utbildningen och följer de nationella kostrekommendationerna. Efter lunch fortsätter undervisningen, ibland med projekt, grupparbete eller inlärning i arbete (LIA).\r\n\r\nYrkesskolor fokuserar på att eleverna ska uppnå praktiska färdigheter innan examen. Därför samarbetar skolorna ofta med lokala företag som erbjuder praktikplatser. Detta ger eleverna en chans att utveckla sina färdigheter i verkliga arbetsmiljöer.', 0, 7, 5, 'Läsförståelse'),
(14, 'Digital säkerhet i vardagen', 'I dagens digitala samhälle är cybersäkerhet viktigare än någonsin. Finländare använder i allt större grad digitala tjänster för bankärenden, kommunikation och myndighetskontakter. Det innebär att riskerna för bedrägerier, nätfiske och identitetsstöld också ökar.\r\n\r\nEn vanlig metod cyberbrottslingar använder är så kallad \"phishing\", där användaren luras att klicka på en falsk länk. Dessa länkar kan leda till falska sidor som ser identiska ut med exempelvis bankens inloggningssida. Många finländare har blivit bättre på att upptäcka dessa försök, men hoten utvecklas hela tiden.\r\n\r\nFör att skydda sig rekommenderas tvåfaktorsautentisering, starka lösenord, regelbundna uppdateringar och försiktighet när man tar emot oväntade meddelanden. Digital säkerhet är inte bara teknik — det är också ett beteende.', 0, 7, 6, 'Läsförståelse'),
(15, 'Hållbar utveckling och miljöval', 'Hållbar utveckling handlar om att ta ansvar för både naturen och framtida generationer. I Finland diskuteras ofta hur individens val påverkar miljön. Det kan handla om energi, transporter, matvanor och konsumtion.\r\n\r\nMånga finländare försöker minska avfall genom att återvinna, undvika engångsprodukter och köpa mer begagnat. Elförbrukning är ett viktigt tema, särskilt under vintern. Att sänka inomhustemperaturen med en grad kan spara stora mängder energi på nationell nivå.\r\n\r\nSkogar spelar en stor roll i Finlands identitet och ekonomi. Samtidigt behövs en balans mellan skogsbruk och bevarande av biologisk mångfald. Diskussionerna om detta är ofta intensiva, särskilt när klimatet förändras snabbt.', 0, 7, 7, 'Läsförståelse'),
(16, 'Historiska händelser som formade Norden', 'Norden har genom århundradena formats av konflikter, handel, kulturutbyte och politiska unioner. Under Kalmarunionen på 1300–1500-talen styrdes Danmark, Norge och Sverige under samma krona. Unionen var instabil och ledde till flera konflikter, vilket senare bidrog till att Sverige bröt sig loss.\r\n\r\nFinlands historia är starkt kopplad till både Sverige och Ryssland. Landet var en del av Sverige i över 600 år, fram till 1809 då Finland blev ett autonomt storfurstendöme under Ryssland. Det finska språket och nationella identiteten stärktes under 1800-talet, vilket bidrog till självständighetsförklaringen 1917.\r\n\r\nDagens Norden kännetecknas av demokrati, starka välfärdssystem och internationellt samarbete.', 0, 7, 8, 'Läsförståelse'),
(17, 'Ekonomi och konsumtionsmönster i dagens samhälle', 'Konsumtionsmönster har förändrats kraftigt de senaste 30 åren. Digital handel har vuxit snabbt, och många finländare handlar numera kläder, elektronik och tjänster online. Detta har påverkat både den lokala handeln och hur företag planerar sina leveranser.\r\n\r\nEkonomi handlar inte bara om pengar, utan även om beteenden. Psykologer talar om ”köpimpulser” som kan leda till överkonsumtion. För att motverka detta rekommenderas budgetering och medvetna köpbeslut.\r\n\r\nInflationen har de senaste åren blivit en central fråga. När priserna stiger snabbare än lönerna påverkas hushållens köpkraft. Detta leder många att prioritera basvaror framför nöjeskonsumtion.', 0, 7, 9, 'Läsförståelse'),
(18, 'Globaliseringens påverkan på arbetsmarknaden', 'Globaliseringen har öppnat upp arbetsmarknaden på ett sätt som aldrig tidigare. Företag konkurrerar numera inte bara med lokala aktörer, utan med organisationer världen över. Detta har både positiva och negativa effekter.\r\n\r\nFördelarna inkluderar större utbud av varor, internationella samarbeten och möjligheten att rekrytera kompetens globalt. Men globalisering har också lett till att vissa jobb flyttats från Finland till länder där arbetskraften är billigare.\r\n\r\nFöljden är att arbetsmarknaden kräver högre kompetens, mer flexibilitet och livslångt lärande. Digitalisering och automatisering förändrar dessutom yrkesroller i snabb takt.', 0, 7, 10, 'Läsförståelse'),
(19, 'En ovanlig sommardag i Åbo', 'Det var en ovanligt varm sommardag i Åbo ... (HELA LÅNGA TEXTEN HÄR) ... mer händelserik än hon någonsin kunnat föreställa sig.', 0, 7, 4, 'Svenska'),
(20, 'Friluftsliv i Norden', 'I Norden har friluftsliv alltid varit en viktig del av kulturen. \r\n    Redan under 1800-talet började människor söka sig ut i naturen, \r\n    inte bara för arbete utan också för rekreation. I Sverige och Finland\r\n    växte intresset snabbt, och vandring, fiske och jakt blev allt vanligare \r\n    fritidsaktiviteter. I Norge fick fjällvandring en särskild betydelse \r\n    tack vare de dramatiska landskapen och de djupa fjordarna.\r\n\r\n    Under 1900-talet ökade intresset ytterligare när samhällena industrialiserades.\r\n    Många kände ett behov av att komma bort från städerna, och naturen blev \r\n    en plats där man kunde hitta lugn och balans. Idag ses friluftsliv som en del \r\n    av välmåendet, och skolor, föreningar och familjer uppmuntrar unga att vistas \r\n    utomhus. Det finns forskning som visar att tid i naturen minskar stress och \r\n    förbättrar koncentrationen. I hela Norden är Allemansrätten en central del \r\n    av frilufts­kulturen – den ger människor möjlighet att röra sig fritt i naturen \r\n    så länge de visar hänsyn.\r\n\r\n    Trots detta finns det utmaningar. Klimatförändringar påverkar fjällmiljöer, \r\n    snösäsonger och djurliv. Samtidigt växer intresset för naturturism, vilket \r\n    kan leda till slitage på populära leder. Många organisationer arbetar därför \r\n    aktivt för hållbart friluftsliv där både naturen och människan mår bra.', 0, 7, 4, 'Läsförståelse');

-- --------------------------------------------------------

--
-- Tabellstruktur `quiz_questions`
--

CREATE TABLE `quiz_questions` (
  `qq_id` int(11) NOT NULL,
  `qq_quiz_fk` int(11) NOT NULL,
  `qq_question_fk` int(11) NOT NULL,
  `qq_order` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumpning av Data i tabell `quiz_questions`
--

INSERT INTO `quiz_questions` (`qq_id`, `qq_quiz_fk`, `qq_question_fk`, `qq_order`) VALUES
(56, 6, 43, 1),
(57, 6, 44, 2),
(58, 6, 45, 3),
(59, 6, 46, 4),
(60, 6, 47, 5),
(61, 6, 48, 6),
(62, 6, 49, 7),
(63, 6, 50, 8),
(64, 6, 51, 9),
(65, 6, 52, 10),
(71, 7, 53, 1),
(72, 7, 54, 2),
(73, 7, 55, 3),
(74, 7, 56, 4),
(75, 7, 57, 5),
(76, 7, 58, 6),
(77, 7, 59, 7),
(78, 7, 60, 8),
(79, 7, 61, 9),
(80, 7, 62, 10),
(86, 8, 63, 1),
(87, 8, 64, 2),
(88, 8, 65, 3),
(89, 8, 66, 4),
(90, 8, 67, 5),
(91, 8, 68, 6),
(92, 8, 69, 7),
(93, 8, 70, 8),
(94, 8, 71, 9),
(95, 8, 72, 10),
(101, 9, 82, 10),
(102, 9, 81, 9),
(103, 9, 80, 8),
(104, 9, 79, 7),
(105, 9, 78, 6),
(106, 9, 77, 5),
(107, 9, 76, 4),
(108, 9, 75, 3),
(109, 9, 74, 2),
(110, 9, 73, 1),
(116, 10, 92, 10),
(117, 10, 91, 9),
(118, 10, 90, 8),
(119, 10, 89, 7),
(120, 10, 88, 6),
(121, 10, 87, 5),
(122, 10, 86, 4),
(123, 10, 85, 3),
(124, 10, 84, 2),
(125, 10, 83, 1),
(131, 11, 102, 10),
(132, 11, 101, 9),
(133, 11, 100, 8),
(134, 11, 99, 7),
(135, 11, 98, 6),
(136, 11, 97, 5),
(137, 11, 96, 4),
(138, 11, 95, 3),
(139, 11, 94, 2),
(140, 11, 93, 1),
(147, 6, 106, 11),
(148, 7, 107, 11),
(149, 8, 108, 11),
(150, 9, 109, 11),
(151, 10, 110, 11),
(152, 11, 111, 11),
(153, 12, 112, 11),
(154, 13, 113, 11),
(155, 14, 114, 11),
(156, 15, 115, 11),
(157, 16, 116, 11),
(158, 17, 117, 11),
(159, 18, 118, 11),
(160, 19, 119, 1),
(161, 20, 120, 1);

-- --------------------------------------------------------

--
-- Tabellstruktur `result`
--

CREATE TABLE `result` (
  `r_id` int(11) NOT NULL,
  `r_user_fk` int(11) NOT NULL,
  `r_quiz_fk` int(11) NOT NULL,
  `r_score` int(11) DEFAULT 0,
  `r_percentage` decimal(5,2) DEFAULT NULL,
  `r_passed` tinyint(1) NOT NULL DEFAULT 0,
  `r_timestamp` datetime NOT NULL DEFAULT current_timestamp(),
  `r_completed` tinyint(1) DEFAULT 0,
  `r_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumpning av Data i tabell `result`
--

INSERT INTO `result` (`r_id`, `r_user_fk`, `r_quiz_fk`, `r_score`, `r_percentage`, `r_passed`, `r_timestamp`, `r_completed`, `r_date`) VALUES
(9, 5, 6, 7, 64.00, 0, '2025-11-18 17:57:16', 0, '2025-11-18 17:57:16'),
(10, 5, 6, 1, 9.00, 0, '2025-11-18 17:58:55', 0, '2025-11-18 17:58:55');

-- --------------------------------------------------------

--
-- Tabellstruktur `student_answer`
--

CREATE TABLE `student_answer` (
  `sa_id` int(11) NOT NULL,
  `sa_student_fk` int(11) NOT NULL,
  `sa_question_fk` int(11) NOT NULL,
  `sa_answer_fk` int(11) DEFAULT NULL,
  `sa_answer_text` longtext DEFAULT NULL,
  `sa_is_correct` tinyint(1) NOT NULL DEFAULT 0,
  `sa_quiz_fk` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumpning av Data i tabell `student_answer`
--

INSERT INTO `student_answer` (`sa_id`, `sa_student_fk`, `sa_question_fk`, `sa_answer_fk`, `sa_answer_text`, `sa_is_correct`, `sa_quiz_fk`) VALUES
(37, 5, 43, NULL, '[1]', 1, 6),
(38, 5, 44, NULL, '[4]', 1, 6),
(39, 5, 45, 7, NULL, 1, 6),
(40, 5, 46, NULL, '[10]', 1, 6),
(41, 5, 47, NULL, 'asdfdgf', 0, 6),
(42, 5, 48, NULL, '[\"vakna\",\"kl\\u00e4 p\\u00e5 sig\",\"\\u00e4ta frukost\",\"g\\u00e5 till bussh\\u00e5llplatsen\"]', 0, 6),
(43, 5, 49, 17, NULL, 1, 6),
(44, 5, 50, 19, NULL, 1, 6),
(45, 5, 51, NULL, 'asdfdf', 0, 6),
(46, 5, 52, 22, NULL, 1, 6),
(47, 5, 106, NULL, '{\"1\":2,\"2\":1,\"3\":3}', 0, 6),
(48, 5, 43, NULL, '[]', 0, 6),
(49, 5, 44, NULL, '[]', 0, 6),
(50, 5, 45, NULL, NULL, 0, 6),
(51, 5, 46, NULL, '[]', 0, 6),
(52, 5, 47, NULL, '', 0, 6),
(53, 5, 48, NULL, '[\"vakna\",\"\\u00e4ta frukost\",\"kl\\u00e4 p\\u00e5 sig\",\"g\\u00e5 till bussh\\u00e5llplatsen\"]', 1, 6),
(54, 5, 49, NULL, NULL, 0, 6),
(55, 5, 50, NULL, NULL, 0, 6),
(56, 5, 51, NULL, '', 0, 6),
(57, 5, 52, NULL, NULL, 0, 6),
(58, 5, 106, NULL, '[]', 0, 6);

-- --------------------------------------------------------

--
-- Tabellstruktur `student_badge`
--

CREATE TABLE `student_badge` (
  `sb_id` int(11) NOT NULL,
  `sb_student_fk` int(11) NOT NULL,
  `sb_badge_fk` int(11) NOT NULL,
  `sb_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellstruktur `student_quiz`
--

CREATE TABLE `student_quiz` (
  `sq_id` int(11) NOT NULL,
  `sq_student_fk` int(11) NOT NULL,
  `sq_quiz_fk` int(11) NOT NULL,
  `sq_correct` int(11) NOT NULL,
  `sq_total` int(11) NOT NULL,
  `sq_score` int(11) NOT NULL,
  `sq_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellstruktur `user`
--

CREATE TABLE `user` (
  `u_id` int(11) NOT NULL,
  `u_name` varchar(100) NOT NULL,
  `u_password_hash` varchar(255) NOT NULL,
  `u_points` int(11) DEFAULT 0,
  `u_mail` varchar(150) DEFAULT NULL,
  `u_level_fk` int(11) DEFAULT NULL,
  `u_role_fk` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumpning av Data i tabell `user`
--

INSERT INTO `user` (`u_id`, `u_name`, `u_password_hash`, `u_points`, `u_mail`, `u_level_fk`, `u_role_fk`) VALUES
(5, 'testuser', '$2y$10$8QBWYIFG8/WJN7NlylVdQu.uPTFOqNOcpoNDaNx2Puz7VH7a53T6S', 0, 'test@user.fi', 4, 1),
(6, 'testadmin', '$2y$10$o5LcS0gT6k4V5nsfmas4bOJu/ojG0xfDnIEKssSSxaUXEWfAvdxUC', 0, 'test@admin.fi', 5, 3),
(7, 'testTeacher', '$2y$10$uawdxA8FK1mKEwiO/VBNmOp9/OQeewpyRAjUH7DxR9wIzxacarPZ.', 0, 'test@teacher.fi', 6, 2),
(8, 'Testuser2', '$2y$10$bV0ddXGuqMRBVDQV2TzePeI/FsEg3dyyPI9eD2ZPwZmnOYa9fhVz2', 0, 'test@user2.fi', 5, 1);

-- --------------------------------------------------------

--
-- Tabellstruktur `user_role`
--

CREATE TABLE `user_role` (
  `ur_id` int(11) NOT NULL,
  `ur_name` varchar(50) NOT NULL,
  `ur_level` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumpning av Data i tabell `user_role`
--

INSERT INTO `user_role` (`ur_id`, `ur_name`, `ur_level`) VALUES
(1, 'Student', 1),
(2, 'Teacher', 2),
(3, 'Admin', 3);

--
-- Index för dumpade tabeller
--

--
-- Index för tabell `answer`
--
ALTER TABLE `answer`
  ADD PRIMARY KEY (`a_id`),
  ADD KEY `a_q_fk` (`a_q_fk`);

--
-- Index för tabell `badge`
--
ALTER TABLE `badge`
  ADD PRIMARY KEY (`b_id`);

--
-- Index för tabell `level`
--
ALTER TABLE `level`
  ADD PRIMARY KEY (`l_id`);

--
-- Index för tabell `level_unlocks`
--
ALTER TABLE `level_unlocks`
  ADD PRIMARY KEY (`lu_id`),
  ADD KEY `lu_level_fk` (`lu_level_fk`),
  ADD KEY `lu_quiz_fk` (`lu_quiz_fk`),
  ADD KEY `lu_badge_fk` (`lu_badge_fk`);

--
-- Index för tabell `match_pair`
--
ALTER TABLE `match_pair`
  ADD PRIMARY KEY (`mp_id`),
  ADD KEY `mp_question_fk` (`mp_question_fk`);

--
-- Index för tabell `question`
--
ALTER TABLE `question`
  ADD PRIMARY KEY (`q_id`);

--
-- Index för tabell `quiz`
--
ALTER TABLE `quiz`
  ADD PRIMARY KEY (`quiz_id`);

--
-- Index för tabell `quiz_questions`
--
ALTER TABLE `quiz_questions`
  ADD PRIMARY KEY (`qq_id`),
  ADD KEY `qq_quiz_fk` (`qq_quiz_fk`),
  ADD KEY `qq_question_fk` (`qq_question_fk`);

--
-- Index för tabell `result`
--
ALTER TABLE `result`
  ADD PRIMARY KEY (`r_id`),
  ADD KEY `r_user_fk` (`r_user_fk`),
  ADD KEY `r_quiz_fk` (`r_quiz_fk`);

--
-- Index för tabell `student_answer`
--
ALTER TABLE `student_answer`
  ADD PRIMARY KEY (`sa_id`),
  ADD KEY `sa_student_fk` (`sa_student_fk`),
  ADD KEY `sa_question_fk` (`sa_question_fk`),
  ADD KEY `sa_answer_fk` (`sa_answer_fk`),
  ADD KEY `fk_sa_quiz` (`sa_quiz_fk`);

--
-- Index för tabell `student_badge`
--
ALTER TABLE `student_badge`
  ADD PRIMARY KEY (`sb_id`),
  ADD KEY `sb_student_fk` (`sb_student_fk`),
  ADD KEY `sb_badge_fk` (`sb_badge_fk`);

--
-- Index för tabell `student_quiz`
--
ALTER TABLE `student_quiz`
  ADD PRIMARY KEY (`sq_id`),
  ADD KEY `sq_student_fk` (`sq_student_fk`),
  ADD KEY `sq_quiz_fk` (`sq_quiz_fk`);

--
-- Index för tabell `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`u_id`);

--
-- Index för tabell `user_role`
--
ALTER TABLE `user_role`
  ADD PRIMARY KEY (`ur_id`);

--
-- AUTO_INCREMENT för dumpade tabeller
--

--
-- AUTO_INCREMENT för tabell `answer`
--
ALTER TABLE `answer`
  MODIFY `a_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=123;

--
-- AUTO_INCREMENT för tabell `badge`
--
ALTER TABLE `badge`
  MODIFY `b_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT för tabell `level`
--
ALTER TABLE `level`
  MODIFY `l_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT för tabell `level_unlocks`
--
ALTER TABLE `level_unlocks`
  MODIFY `lu_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT för tabell `match_pair`
--
ALTER TABLE `match_pair`
  MODIFY `mp_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT för tabell `question`
--
ALTER TABLE `question`
  MODIFY `q_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=121;

--
-- AUTO_INCREMENT för tabell `quiz`
--
ALTER TABLE `quiz`
  MODIFY `quiz_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT för tabell `quiz_questions`
--
ALTER TABLE `quiz_questions`
  MODIFY `qq_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=162;

--
-- AUTO_INCREMENT för tabell `result`
--
ALTER TABLE `result`
  MODIFY `r_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT för tabell `student_answer`
--
ALTER TABLE `student_answer`
  MODIFY `sa_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT för tabell `student_badge`
--
ALTER TABLE `student_badge`
  MODIFY `sb_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT för tabell `student_quiz`
--
ALTER TABLE `student_quiz`
  MODIFY `sq_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT för tabell `user`
--
ALTER TABLE `user`
  MODIFY `u_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT för tabell `user_role`
--
ALTER TABLE `user_role`
  MODIFY `ur_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restriktioner för dumpade tabeller
--

--
-- Restriktioner för tabell `answer`
--
ALTER TABLE `answer`
  ADD CONSTRAINT `answer_ibfk_1` FOREIGN KEY (`a_q_fk`) REFERENCES `question` (`q_id`) ON DELETE CASCADE;

--
-- Restriktioner för tabell `level_unlocks`
--
ALTER TABLE `level_unlocks`
  ADD CONSTRAINT `level_unlocks_ibfk_1` FOREIGN KEY (`lu_level_fk`) REFERENCES `level` (`l_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `level_unlocks_ibfk_2` FOREIGN KEY (`lu_quiz_fk`) REFERENCES `quiz` (`quiz_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `level_unlocks_ibfk_3` FOREIGN KEY (`lu_badge_fk`) REFERENCES `badge` (`b_id`) ON DELETE CASCADE;

--
-- Restriktioner för tabell `match_pair`
--
ALTER TABLE `match_pair`
  ADD CONSTRAINT `match_pair_ibfk_1` FOREIGN KEY (`mp_question_fk`) REFERENCES `question` (`q_id`) ON DELETE CASCADE;

--
-- Restriktioner för tabell `quiz_questions`
--
ALTER TABLE `quiz_questions`
  ADD CONSTRAINT `quiz_questions_ibfk_1` FOREIGN KEY (`qq_quiz_fk`) REFERENCES `quiz` (`quiz_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `quiz_questions_ibfk_2` FOREIGN KEY (`qq_question_fk`) REFERENCES `question` (`q_id`) ON DELETE CASCADE;

--
-- Restriktioner för tabell `result`
--
ALTER TABLE `result`
  ADD CONSTRAINT `result_ibfk_1` FOREIGN KEY (`r_user_fk`) REFERENCES `user` (`u_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `result_ibfk_2` FOREIGN KEY (`r_quiz_fk`) REFERENCES `quiz` (`quiz_id`) ON DELETE CASCADE;

--
-- Restriktioner för tabell `student_answer`
--
ALTER TABLE `student_answer`
  ADD CONSTRAINT `fk_sa_quiz` FOREIGN KEY (`sa_quiz_fk`) REFERENCES `quiz` (`quiz_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_answer_ibfk_1` FOREIGN KEY (`sa_student_fk`) REFERENCES `user` (`u_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_answer_ibfk_2` FOREIGN KEY (`sa_question_fk`) REFERENCES `question` (`q_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_answer_ibfk_3` FOREIGN KEY (`sa_answer_fk`) REFERENCES `answer` (`a_id`) ON DELETE SET NULL;

--
-- Restriktioner för tabell `student_badge`
--
ALTER TABLE `student_badge`
  ADD CONSTRAINT `student_badge_ibfk_1` FOREIGN KEY (`sb_student_fk`) REFERENCES `user` (`u_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_badge_ibfk_2` FOREIGN KEY (`sb_badge_fk`) REFERENCES `badge` (`b_id`) ON DELETE CASCADE;

--
-- Restriktioner för tabell `student_quiz`
--
ALTER TABLE `student_quiz`
  ADD CONSTRAINT `student_quiz_ibfk_1` FOREIGN KEY (`sq_student_fk`) REFERENCES `user` (`u_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_quiz_ibfk_2` FOREIGN KEY (`sq_quiz_fk`) REFERENCES `quiz` (`quiz_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

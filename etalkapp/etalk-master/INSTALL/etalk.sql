SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE `sounds` (
  `id` varchar(255) NOT NULL,
  `dir` varchar(255) NOT NULL,
  `text` text NOT NULL,
  `type` enum('explanation','quotation') NOT NULL DEFAULT 'explanation',
  `entities` varchar(255) NOT NULL,
  `file` char(26) NOT NULL,
  `file_credits` varchar(255) NOT NULL DEFAULT 'Creative Commons Attribution - Pas d’Utilisation Commerciale - Partage dans les Mêmes Conditions 3.0 Suisse',
  `file_link` varchar(255) NOT NULL DEFAULT 'http://creativecommons.org/licenses/by-nc-sa/3.0/ch/deed.fr',
  `chaptering` enum('continue','paragraph','section') NOT NULL DEFAULT 'continue',
  `section_title` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `talks` (
  `dir` varchar(20) NOT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `theme` varchar(255) NOT NULL,
  PRIMARY KEY (`dir`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


SET FOREIGN_KEY_CHECKS = 1;

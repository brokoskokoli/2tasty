-- --------------------------------------------------------
-- Hôte :                        127.0.0.1
-- Version du serveur:           5.7.19 - MySQL Community Server (GPL)
-- SE du serveur:                Win64
-- HeidiSQL Version:             9.4.0.5125
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Export de la structure de la table 2tasty. ref_ingredient_display_preference
DROP TABLE IF EXISTS `ref_ingredient_display_preference`;
CREATE TABLE IF NOT EXISTS `ref_ingredient_display_preference` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Export de données de la table 2tasty.ref_ingredient_display_preference : ~2 rows (environ)
DELETE FROM `ref_ingredient_display_preference`;
/*!40000 ALTER TABLE `ref_ingredient_display_preference` DISABLE KEYS */;
INSERT INTO `ref_ingredient_display_preference` (`id`, `name`) VALUES
	(1, 'ingredient_preferences.Native'),
	(2, 'ingredient_preferences.German'),
	(3, 'ingredient_preferences.US');
/*!40000 ALTER TABLE `ref_ingredient_display_preference` ENABLE KEYS */;

-- Export de la structure de la table 2tasty. ref_unit
DROP TABLE IF EXISTS `ref_unit`;
CREATE TABLE IF NOT EXISTS `ref_unit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `factor_to_liter` double DEFAULT NULL,
  `factor_to_kg` double DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Export de données de la table 2tasty.ref_unit : ~11 rows (environ)
DELETE FROM `ref_unit`;
/*!40000 ALTER TABLE `ref_unit` DISABLE KEYS */;
INSERT INTO `ref_unit` (`id`, `name`, `factor_to_liter`, `factor_to_kg`) VALUES
	(1, '', NULL, NULL),
	(2, 'kg', NULL, 1),
	(3, 'g', NULL, 0.001),
	(4, 'ml', 0.001, NULL),
	(5, 'l', 1, NULL),
	(6, 'oz', NULL, 0.02834952),
	(7, 'cup', 0.23658823, NULL),
	(8, '1/2 cup', 0.11829411, NULL),
	(9, '1/4 cup', 0.059147059, NULL),
	(10, '1/8 cup', 0.02957352, NULL),
	(11, 'teaspoon_us', 0.0049289, NULL);
/*!40000 ALTER TABLE `ref_unit` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;

INSERT INTO `recipe_tag` (`id`, `name`) VALUES (1, 'ToCook');

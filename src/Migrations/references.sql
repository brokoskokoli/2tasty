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

-- Export de données de la table myfood.ref_ingredient_display_preference : ~3 rows (environ)
DELETE FROM `ref_ingredient_display_preference`;
/*!40000 ALTER TABLE `ref_ingredient_display_preference` DISABLE KEYS */;
INSERT INTO `ref_ingredient_display_preference` (`id`, `name`) VALUES
	(1, 'ingredient_preferences.native'),
	(2, 'ingredient_preferences.german'),
	(3, 'ingredient_preferences.us');
/*!40000 ALTER TABLE `ref_ingredient_display_preference` ENABLE KEYS */;

-- Export de données de la table myfood.ref_unit : ~11 rows (environ)
DELETE FROM `ref_unit`;
/*!40000 ALTER TABLE `ref_unit` DISABLE KEYS */;
INSERT INTO `ref_unit` (`id`, `name`, `factor_to_liter`, `factor_to_kg`) VALUES
	(1, '', NULL, NULL),
	(2, 'kg', NULL, 1),
	(3, 'g', NULL, 0.001),
	(4, 'ml', 0.001, NULL),
	(5, 'l', 1, NULL),
	(6, 'oz', 0.0295735, NULL),
	(7, 'cup', 0.23658823, NULL),
	(8, '1/2 cup', 0.11829411, NULL),
	(9, '1/4 cup', 0.059147059, NULL),
	(10, '1/8 cup', 0.02957352, NULL),
	(11, 'teaspoon_us', 0.0049289, NULL);
/*!40000 ALTER TABLE `ref_unit` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;

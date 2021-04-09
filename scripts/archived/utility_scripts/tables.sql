# Boundaries table
CREATE TABLE `dt_geonames_boundaries` (
  `geonameid` bigint(20) NOT NULL,
  `north_latitude` float DEFAULT NULL,
  `south_latitude` float DEFAULT NULL,
  `west_longitude` float DEFAULT NULL,
  `east_longitude` float DEFAULT NULL,
  PRIMARY KEY (`geonameid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# DT Geonames Table
CREATE TABLE `dt_geonames` (
  `geonameid` bigint(20) unsigned NOT NULL,
  `name` varchar(200) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `asciiname` varchar(200) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `alternatenames` varchar(200) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `latitude` float DEFAULT NULL,
  `longitude` float DEFAULT NULL,
  `feature_class` char(1) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `feature_code` varchar(10) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `country_code` char(2) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `cc2` varchar(200) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `admin1_code` varchar(20) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `admin2_code` varchar(80) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `admin3_code` varchar(20) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `admin4_code` varchar(20) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `population` bigint(20) NOT NULL DEFAULT '0',
  `elevation` int(20) DEFAULT NULL,
  `dem` varchar(20) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `timezone` varchar(40) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `modification_date` date DEFAULT NULL,
  `parent_id` bigint(20) DEFAULT NULL,
  `country_geonameid` bigint(20) DEFAULT NULL,
  `admin1_geonameid` bigint(20) DEFAULT NULL,
  `admin2_geonameid` bigint(20) DEFAULT NULL,
  `admin3_geonameid` bigint(20) DEFAULT NULL,
  `level` varchar(50) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `alt_name` varchar(200) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `alt_name_changed` tinyint(1) NOT NULL DEFAULT '0',
  `alt_population` bigint(20) DEFAULT NULL,
  `is_custom_location` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`geonameid`),
  KEY `feature_code` (`feature_code`),
  KEY `country_code` (`country_code`),
  KEY `population` (`population`),
  KEY `parent_id` (`parent_id`),
  KEY `country_geonameid` (`country_geonameid`),
  KEY `admin1_geonameid` (`admin1_geonameid`),
  KEY `admin2_geonameid` (`admin2_geonameid`),
  KEY `admin3_geonameid` (`admin3_geonameid`),
  KEY `level` (`level`),
  FULLTEXT KEY `name` (`name`),
  FULLTEXT KEY `alt_name` (`alt_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

# Polygons Table
CREATE TABLE `geonames_polygons` (
  `geonameid` bigint(20) DEFAULT NULL,
  `geoJSON` longtext
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




## SQL to limit raw Hierarchy

# Earth
# 1 row
#
SELECT (0) as parent_id, (6295630) as id
UNION ALL

# Continents
# 7 rows
#
SELECT parent_id, id FROM geonames_hierarchy WHERE parent_id = '6295630'
UNION ALL

# Countries and Political Units (excluding Antarctica)
# 297 rows
#
SELECT parent_id, id FROM geonames_hierarchy WHERE parent_id IN (SELECT id FROM geonames_hierarchy WHERE parent_id = '6295630') AND parent_id != '6255152'
UNION ALL

# States
# 4336 rows
#
SELECT parent_id, id FROM geonames_hierarchy WHERE parent_id IN (SELECT id FROM geonames_hierarchy WHERE parent_id IN (SELECT id FROM geonames_hierarchy WHERE parent_id = '6295630') AND parent_id != '6255152' )
UNION ALL

# Counties
# 48569 rows
#
SELECT parent_id, id FROM geonames_hierarchy WHERE parent_id IN (SELECT id FROM geonames_hierarchy WHERE parent_id IN (SELECT id FROM geonames_hierarchy WHERE parent_id IN (SELECT id FROM geonames_hierarchy WHERE parent_id = '6295630') AND parent_id != '6255152' ) )

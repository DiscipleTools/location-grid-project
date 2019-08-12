# Location Grid Project

The Location Grid Project hopes to offer a cross-referenced grid for reporting on movement progress across the planet, 
while at the same time is location sensitive for activity in dangerous or anti-christian locations and compliance with 
increasing privacy laws like GDPR.

The project serves to support the vision of consistently tracking church planting movement efforts globally in a way
 that allows networks and different organizations to share location sensitive reports to visualize and respond to
 areas of disciple making movement and areas where there is no disciple making movement.

The project offers a global grid of unique location ids for countries, states, and counties, 
longitude/latitude, populations for those administrative areas, and the supporting geojson polygon files for 
lightweight application display. 

The polygon data has been collected from both Geonames, Open Street Map, and GADM projects. 

The administrative boundary information (north, south, east, west) has been generated from currently available 
polygons by the Location Grid Project. 

The hierarchy data in columns parent_id, admin0_grid_id, admin1_grid_id, admin2_grid_id, admin3_grid_id, admin4_grid_id, admin5_grid_id has been
generated from the Geonames Hierarchy table by the Location Grid Project.

## DATABASES

### The location_grid table 
The location grid table is a MYSQL table with 380,000 records for the administrative levels of the world. Based
on the GADM polygon set. This table adds hierarchy, geonames cross-reference, bounding boxes, longitude and latitude for centerpoint,
country_codes, and level descriptions.

[Download Location Grid SQL Table](https://storage.googleapis.com/location-grid-source-data/location_grid.sql.zip)
(14.77 MB)



###The location_grid_geometry table 
The location_grid_geometry table has all polygons for each of the 380,000 administrative units cross-referenced
to the location_grid table by grid_id.

[Download Location_Grid_Geometry](https://storage.googleapis.com/location-grid-source-data/location_grid_geometry.sql.zip)
 (1.81 GB)
 
 
### The location-grid-mirror
The location grid mirror is a folder containing three folders: low, high, collection. The low and high
folders contain .geojson files named by grid_id containing the polygon or
multipolygon for the administrative unit and properties containing name, level names, and centerpoints. Those files
in the low folder are compressed for web delivery, the high folder contains full resolution polygons. The 
collection folder contains .geojson files named by grid_id that contain a collection of the next
level administrative polygons. i.e. the {state of colorado}.geojson file contains all the counties for Colorado
in the single .geojson file. Each sub-administrative unit in the collection has properties including name, lng/lat centerpoint,
and admin level data.

[Download Location Grid Mirror](https://storage.googleapis.com/location-grid-source-data/location-grid-mirror.zip)
(4.06 GB)

## Grid_ID Ranges

| Range | Description |
| ------ | ----- |
|100,000,000 - 200,000,000 | One hundred million to two hundred million is the id range for the location grid global admin levels |
|1,000,000,000 + | 1 billion and higher is reserved for custom locations |

## TABLE STRUCTURES

location_grid table:
```apacheconfig
CREATE TABLE `location_grid` (
  `grid_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL DEFAULT '',
  `level` float DEFAULT NULL,
  `level_name` varchar(7) DEFAULT NULL,
  `country_code` varchar(10) DEFAULT NULL,
  `admin0_code` varchar(10) DEFAULT NULL,
  `admin1_code` varchar(20) DEFAULT NULL,
  `admin2_code` varchar(20) DEFAULT NULL,
  `admin3_code` varchar(20) DEFAULT NULL,
  `admin4_code` varchar(20) DEFAULT NULL,
  `admin5_code` varchar(20) DEFAULT NULL,
  `parent_id` bigint(20) DEFAULT NULL,
  `admin0_grid_id` bigint(20) DEFAULT NULL,
  `admin1_grid_id` bigint(20) DEFAULT NULL,
  `admin2_grid_id` bigint(20) DEFAULT NULL,
  `admin3_grid_id` bigint(20) DEFAULT NULL,
  `admin4_grid_id` bigint(20) DEFAULT NULL,
  `admin5_grid_id` bigint(20) DEFAULT NULL,
  `longitude` float DEFAULT NULL,
  `latitude` float DEFAULT NULL,
  `north_latitude` float DEFAULT NULL,
  `south_latitude` float DEFAULT NULL,
  `west_longitude` float DEFAULT NULL,
  `east_longitude` float DEFAULT NULL,
  `population` bigint(20) NOT NULL DEFAULT '0',
  `modification_date` date DEFAULT NULL,
  `geonames_ref` bigint(20) DEFAULT NULL,
  `wikidata_ref` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`grid_id`),
  KEY `level` (`level`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `admin0_code` (`admin0_code`),
  KEY `admin1_code` (`admin1_code`),
  KEY `admin2_code` (`admin2_code`),
  KEY `admin3_code` (`admin3_code`),
  KEY `admin4_code` (`admin4_code`),
  KEY `country_code` (`country_code`),
  KEY `north_latitude` (`north_latitude`),
  KEY `south_latitude` (`south_latitude`),
  KEY `parent_id` (`parent_id`),
  KEY `west_longitude` (`west_longitude`),
  KEY `east_longitude` (`east_longitude`),
  KEY `admin5_code` (`admin5_code`),
  KEY `admin0_grid_id` (`admin0_grid_id`),
  KEY `admin1_grid_id` (`admin1_grid_id`),
  KEY `admin2_grid_id` (`admin2_grid_id`),
  KEY `admin3_grid_id` (`admin3_grid_id`),
  KEY `admin4_grid_id` (`admin4_grid_id`),
  KEY `admin5_grid_id` (`admin5_grid_id`),
  KEY `level_name` (`level_name`),
  FULLTEXT KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=100386738 DEFAULT CHARSET=utf8;
```

location_grid_geometry:
```apacheconfig
CREATE TABLE `location_grid_geometry` (
  `grid_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `geoJSON` longtext,
  PRIMARY KEY (`grid_id`)
) ENGINE=InnoDB AUTO_INCREMENT=100386738 DEFAULT CHARSET=utf8;
```
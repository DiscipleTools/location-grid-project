# New Morocco Divisions

[https://en.wikipedia.org/wiki/Regions_of_Morocco](https://en.wikipedia.org/wiki/Regions_of_Morocco)

![](new-divisions.png)

## Current Levels GADM
| Level | Count |
| --- | --- |
| admin0 | 1 |
| admin1 | 15 |
| admin2 | 54 |
| admin3 | 399 |
| admin4 | 1515 |

## Steps

1. Convert shape file to geojson
2. Import geojson polygons to MySQL
3. 



## Queries
```
INSERT INTO morocco_import (grid_id, name, geoJSON)
SELECT grid_id, name, geoJSON FROM morocco WHERE level < 2 and level != 0;

UPDATE morocco_import SET admin1_grid_id = '100241775' WHERE ADM1_PCODE = 'MA010';

SELECT * FROM location_grid WHERE grid_id = '100386741';




SELECT lg.* FROM morocco_import lg WHERE ADM2_EN = '' OR grid_id IS NOT NULL;

SELECT lg.* FROM morocco_import lg WHERE (ADM2_EN = '' OR grid_id IS NOT NULL) AND admin1_grid_id IS NOT NULL ;


SELECT lg.* FROM morocco_import lg WHERE (ADM2_EN = '' OR grid_id IS NOT NULL) AND name IS NOT NULL;


SELECT lg.* FROM morocco_import lg WHERE (grid_id = '100241767' OR ADM1_PCODE = 'MA005') AND ( ADM2_EN = '' OR ADM2_EN IS NULL);



SELECT * FROM location_grid WHERE grid_id IN (100386757,100386756,100386755,100386754,100386753,100386752,100386751,100386750,100386749,100386748,100386747,100386746,100386745,100386744,100386743,100386742);
SELECT * FROM location_grid_geometry WHERE grid_id IN (100386757,100386756,100386755,100386754,100386753,100386752,100386751,100386750,100386749,100386748,100386747,100386746,100386745,100386744,100386743,100386742);

SELECT * FROM location_grid WHERE grid_id IN (100386741);
SELECT * FROM location_grid_geometry WHERE grid_id IN (100386741);

SELECT * FROM location_grid WHERE grid_id IN (100241762, 100241763, 100241764, 100241765, 100241768, 100241776);
SELECT * FROM location_grid_geometry WHERE grid_id IN (100241762, 100241763, 100241764, 100241765, 100241768, 100241776);
# DELETE FROM location_grid_geometry WHERE grid_id IN (100241762, 100241763, 100241764, 100241765, 100241768, 100241776);
# DELETE FROM location_grid WHERE grid_id IN (100241762, 100241763, 100241764, 100241765, 100241768, 100241776);

SELECT * FROM location_grid WHERE grid_id IN (100241794);
SELECT * FROM location_grid_geometry WHERE grid_id IN (100241794);
# DELETE FROM location_grid_geometry WHERE grid_id IN (100241794);
# DELETE FROM location_grid WHERE grid_id IN (100241794);



UPDATE location_grid 
SELECT * FROM morocco
WHERE location_grid.grid_id=morocco.grid_id;

UPDATE location_grid
INNER JOIN morocco USING (grid_id)
SET 
location_grid.name=morocco.name,
location_grid.admin0_grid_id=morocco.admin0_grid_id,
location_grid.admin1_grid_id=morocco.admin1_grid_id,
location_grid.admin2_grid_id=morocco.admin2_grid_id,
location_grid.parent_id=morocco.parent_id,
location_grid.longitude=morocco.longitude,
location_grid.latitude=morocco.latitude,
location_grid.north_latitude=morocco.north_latitude,
location_grid.south_latitude=morocco.south_latitude,
location_grid.east_longitude=morocco.east_longitude,
location_grid.west_longitude=morocco.west_longitude,
location_grid.modification_date='2021-04-09'
;




// removed

"grid_id","name","level","level_name","country_code","admin0_code","admin1_code","admin2_code","admin3_code","admin4_code","admin5_code","parent_id","admin0_grid_id","admin1_grid_id","admin2_grid_id","admin3_grid_id","admin4_grid_id","admin5_grid_id","longitude","latitude","north_latitude","south_latitude","east_longitude","west_longitude","population","modification_date","geonames_ref","wikidata_ref"
100241762,"Chaouia - Ouardigha",1,"admin1","MA","MAR","MAR.1_1",NULL,NULL,NULL,NULL,100241761,100241761,100241762,NULL,NULL,NULL,NULL,-7.06155,33.0898,33.8304,32.3493,-5.99993,-8.12317,0,"2019-06-21",NULL,NULL
100241763,"Doukkala - Abda",1,"admin1","MA","MAR","MAR.2_1",NULL,NULL,NULL,NULL,100241761,100241761,100241763,NULL,NULL,NULL,NULL,-8.6839,32.5761,33.4585,31.6938,-7.93239,-9.43542,0,"2019-06-21",NULL,NULL
100241764,"Fès - Boulemane",1,"admin1","MA","MAR","MAR.3_1",NULL,NULL,NULL,NULL,100241761,100241761,100241764,NULL,NULL,NULL,NULL,-4.22242,33.4586,34.3254,32.6238,-2.96907,-5.43362,4236892,"2019-06-21",11281876,NULL
100241765,"Gharb - Chrarda - Béni Hssen",1,"admin1","MA","MAR","MAR.4_1",NULL,NULL,NULL,NULL,100241761,100241761,100241765,NULL,NULL,NULL,NULL,-5.92923,34.5418,34.988,34.0956,-5.14666,-6.71181,0,"2019-06-21",NULL,NULL
100241768,"Laâyoune - Boujdour - Sakia El Hamra",1,"admin1","MA","MAR","MAR.7_1",NULL,NULL,NULL,NULL,100241761,100241761,100241768,NULL,NULL,NULL,NULL,-12.2345,27.8667,28.2151,27.6785,-11.4789,-13.1679,367758,"2019-06-21",11281885,NULL
100241776,"Taza - Al Hoceima - Taounate",1,"admin1","MA","MAR","MAR.15_1",NULL,NULL,NULL,NULL,100241761,100241761,100241776,NULL,NULL,NULL,NULL,-4.21734,34.4157,35.2621,33.5694,-2.94591,-5.48878,0,"2019-06-21",NULL,NULL

	// removed
	INSERT INTO `location_grid` (`grid_id`, `name`, `level`, `level_name`, `country_code`, `admin0_code`, `admin1_code`, `admin2_code`, `admin3_code`, `admin4_code`, `admin5_code`, `parent_id`, `admin0_grid_id`, `admin1_grid_id`, `admin2_grid_id`, `admin3_grid_id`, `admin4_grid_id`, `admin5_grid_id`, `longitude`, `latitude`, `north_latitude`, `south_latitude`, `east_longitude`, `west_longitude`, `population`, `modification_date`, `geonames_ref`, `wikidata_ref`)
    VALUES
    	(100241794, 'Laâyoune', 2, 'admin2', 'MA', 'MAR', 'MAR.7_1', 'MAR.7.1_1', NULL, NULL, NULL, 100241768, 100241761, 100241768, 100241794, NULL, NULL, NULL, -12.2345, 27.8667, 28.2151, 27.6785, -11.4789, -13.1679, 13082, '2019-06-21', 2543878, NULL);


// admin 1 removed
    grid_id IN (100241762, 100241763, 100241764, 100241765, 100241768, 100241776)

// admin 2 removed
    grid_id IN (100241794)


// new
INSERT INTO `location_grid` (`grid_id`, `name`, `level`, `level_name`, `country_code`, `admin0_code`, `admin1_code`, `admin2_code`, `admin3_code`, `admin4_code`, `admin5_code`, `parent_id`, `admin0_grid_id`, `admin1_grid_id`, `admin2_grid_id`, `admin3_grid_id`, `admin4_grid_id`, `admin5_grid_id`, `longitude`, `latitude`, `north_latitude`, `south_latitude`, `east_longitude`, `west_longitude`, `population`, `modification_date`, `geonames_ref`, `wikidata_ref`)
VALUES
	(100386757, 'Sidi Ifni Province', 2, 'admin2', 'MA', 'MAR', NULL, NULL, NULL, NULL, NULL, 100241767, 100241761, 100241767, 100386757, NULL, NULL, NULL, -9.84332, 29.3169, 29.6306, 29.0287, -9.15779, -10.4202, 0, '2021-04-08', NULL, NULL),
	(100386756, 'Rhamna Province', 2, 'admin2', 'MA', 'MAR', NULL, NULL, NULL, NULL, NULL, 100241769, 100241761, 100241769, 100386756, NULL, NULL, NULL, -7.93337, 32.1995, 32.8142, 31.6191, -7.53602, -8.48064, 0, '2021-04-08', NULL, NULL),
	(100386755, 'Province de Youssoufia', 2, 'admin2', 'MA', 'MAR', NULL, NULL, NULL, NULL, NULL, 100241769, 100241761, 100241769, 100386755, NULL, NULL, NULL, -8.60853, 31.9967, 32.3312, 31.716, -8.2265, -8.98222, 0, '2021-04-08', NULL, NULL),
	(100386754, 'Province de Tinghir', 2, 'admin2', 'MA', 'MAR', NULL, NULL, NULL, NULL, NULL, 100386741, 100241761, 100386741, 100386754, NULL, NULL, NULL, -5.49354, 31.3244, 32.1708, 30.4639, -4.53083, -6.44715, 0, '2021-04-08', NULL, NULL),
	(100386753, 'Province de Taourirt', 2, 'admin2', 'MA', 'MAR', NULL, NULL, NULL, NULL, NULL, 100241771, 100241761, 100241771, 100386753, NULL, NULL, NULL, -2.81411, 34.1528, 34.746, 33.348, -2.27143, -3.25728, 0, '2021-04-08', NULL, NULL),
	(100386752, 'Province de Sidi Slimane', 2, 'admin2', 'MA', 'MAR', NULL, NULL, NULL, NULL, NULL, 100241772, 100241761, 100241772, 100386752, NULL, NULL, NULL, -6.02619, 34.2791, 34.4889, 34.0843, -5.6843, -6.31576, 0, '2021-04-08', NULL, NULL),
	(100386751, 'Province de Sidi Bennour', 2, 'admin2', 'MA', 'MAR', NULL, NULL, NULL, NULL, NULL, 100241766, 100241761, 100241766, 100386751, NULL, NULL, NULL, -8.48618, 32.6164, 32.8861, 32.2966, -8.04257, -9.05932, 0, '2021-04-08', NULL, NULL),
	(100386750, 'Province de Midelt', 2, 'admin2', 'MA', 'MAR', NULL, NULL, NULL, NULL, NULL, 100386741, 100241761, 100386741, 100386750, NULL, NULL, NULL, -4.81737, 32.4408, 33.0979, 31.8684, -3.78804, -5.88818, 0, '2021-04-08', NULL, NULL),
	(100386749, 'Province de Guercif', 2, 'admin2', 'MA', 'MAR', NULL, NULL, NULL, NULL, NULL, 100241771, 100241761, 100241771, 100386749, NULL, NULL, NULL, -3.51327, 34.1744, 34.7688, 33.5094, -2.93528, -4.12313, 0, '2021-04-08', NULL, NULL),
	(100386748, 'Province de Fquih Ben Saleh', 2, 'admin2', 'MA', 'MAR', NULL, NULL, NULL, NULL, NULL, 100241774, 100241761, 100241774, 100386748, NULL, NULL, NULL, -6.7846, 32.4164, 32.6763, 32.1626, -6.42343, -7.13304, 0, '2021-04-08', NULL, NULL),
	(100386747, 'Province de Driouch', 2, 'admin2', 'MA', 'MAR', NULL, NULL, NULL, NULL, NULL, 100241771, 100241761, 100241771, 100386747, NULL, NULL, NULL, -3.50917, 34.9607, 35.2883, 34.538, -3.16865, -3.82924, 0, '2021-04-08', NULL, NULL),
	(100386746, 'Province d Ouezzane', 2, 'admin2', 'MA', 'MAR', NULL, NULL, NULL, NULL, NULL, 100241775, 100241761, 100241775, 100386746, NULL, NULL, NULL, -5.44959, 34.7967, 35.0429, 34.5148, -5.14494, -5.79486, 0, '2021-04-08', NULL, NULL),
	(100386745, 'Prefecture de M diq Fnideq', 2, 'admin2', 'MA', 'MAR', NULL, NULL, NULL, NULL, NULL, 100241775, 100241761, 100241775, 100386745, NULL, NULL, NULL, -5.37608, 35.7412, 35.9224, 35.5963, -5.26591, -5.46004, 0, '2021-04-08', NULL, NULL),
	(100386744, 'Nouaceur Province', 2, 'admin2', 'MA', 'MAR', NULL, NULL, NULL, NULL, NULL, 100241766, 100241761, 100241766, 100386744, NULL, NULL, NULL, -7.67517, 33.4219, 33.5557, 33.2682, -7.53319, -7.88119, 0, '2021-04-08', NULL, NULL),
	(100386743, 'Mediouna Province', 2, 'admin2', 'MA', 'MAR', NULL, NULL, NULL, NULL, NULL, 100241766, 100241761, 100241766, 100386743, NULL, NULL, NULL, -7.48132, 33.4937, 33.5808, 33.4108, -7.3534, -7.59936, 0, '2021-04-08', NULL, NULL),
	(100386742, 'Berrechid Province', 2, 'admin2', 'MA', 'MAR', NULL, NULL, NULL, NULL, NULL, 100241766, 100241761, 100241766, 100386742, NULL, NULL, NULL, -7.695, 33.2456, 33.5072, 33.0082, -7.21449, -8.14568, 0, '2021-04-08', NULL, NULL),
	(100386741, 'Draa Tafilalet', 1, 'admin1', 'MA', 'MAR', NULL, NULL, NULL, NULL, NULL, 100241761, 100241761, 100386741, NULL, NULL, NULL, NULL, -5.35441, 31.2026, 33.0979, 29.4766, -3.11273, -7.75238, 0, '2021-04-08', NULL, NULL);

// Added admin1
    grid_id IN (100386741)

// added admin2
	grid_id IN (100386757,100386756,100386755,100386754,100386753,100386752,100386751,100386750,100386749,100386748,100386747,100386746,100386745,100386744,100386743,100386742)

	100241762 => 100241766,
    100241763 => 	100241769,




```

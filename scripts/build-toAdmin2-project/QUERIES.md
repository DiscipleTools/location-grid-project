

This query gets all the admin 2 countries, all the countries with admin1 but not admin2, and all those with only admin0
```
-- 
# Only with admin0
-- 
SELECT  a0.grid_id, a0.name, lg0.level, count(lg0.grid_id) as count
FROM location_grid lg0
LEFT JOIN location_grid as a0 ON lg0.admin0_grid_id=a0.grid_id
WHERE lg0.level < 1
AND lg0.country_code NOT IN (
	SELECT lg23.country_code FROM location_grid lg23 WHERE lg23.level_name = 'admin1' GROUP BY lg23.country_code
)
GROUP BY  a0.grid_id, a0.name, lg0.level

UNION ALL
-- 
# Only admin1
-- 
SELECT 
a0.grid_id, a0.name as admin0_name, lg1.level, count(lg1.grid_id) as count
FROM location_grid as lg1 
LEFT JOIN location_grid as a0 ON lg1.admin0_grid_id=a0.grid_id
WHERE lg1.country_code NOT IN (
SELECT lg22.country_code FROM location_grid lg22 WHERE lg22.level_name = 'admin2' GROUP BY lg22.country_code
) AND lg1.level_name != 'admin0'
GROUP BY a0.grid_id, a0.name, lg1.level

UNION ALL
-- 
# Has admin2
-- 
SELECT a0.grid_id, a0.name, lg2.level, count(lg2.grid_id) as count
FROM location_grid lg2 
LEFT JOIN location_grid as a0 ON lg2.admin0_grid_id=a0.grid_id
WHERE lg2.level_name = 'admin2' 
GROUP BY a0.grid_id, a0.name, lg2.level;
```



Total geographies down to admin2
46,780 admin0, admin1, admin2 unique
166 countries with admin2
62 countries with admin1 only
24 countreis with admin0 only



Full list of all areas.
```
-- 
# Only with admin0
-- 
SELECT  a0.name as country, lg0.*
FROM location_grid lg0
LEFT JOIN location_grid as a0 ON lg0.admin0_grid_id=a0.grid_id
WHERE lg0.level < 1
AND lg0.country_code NOT IN (
	SELECT lg23.country_code FROM location_grid lg23 WHERE lg23.level_name = 'admin1' GROUP BY lg23.country_code
)


UNION ALL
-- 
# Only admin1
-- 
SELECT   a0.name as country, lg1.*
FROM location_grid as lg1 
LEFT JOIN location_grid as a0 ON lg1.admin0_grid_id=a0.grid_id
WHERE lg1.country_code NOT IN (
SELECT lg22.country_code FROM location_grid lg22 WHERE lg22.level_name = 'admin2' GROUP BY lg22.country_code
) AND lg1.level_name != 'admin0'


UNION ALL
-- 
# Has admin2
-- 
SELECT  a0.name as country, lg2.*
FROM location_grid lg2 
LEFT JOIN location_grid as a0 ON lg2.admin0_grid_id=a0.grid_id
WHERE lg2.level_name = 'admin2' ;
```

Four Column
```
-- 
# Only with admin0
-- 
SELECT   lg0.grid_id, lg0.name, a0.name as country,  lg0.level_name
FROM location_grid lg0
LEFT JOIN location_grid as a0 ON lg0.admin0_grid_id=a0.grid_id
WHERE lg0.level < 1
AND lg0.country_code NOT IN (
	SELECT lg23.country_code FROM location_grid lg23 WHERE lg23.level_name = 'admin1' GROUP BY lg23.country_code
)


UNION ALL
-- 
# Only admin1
-- 
SELECT  lg1.grid_id,  lg1.name, a0.name as country,  lg1.level_name
FROM location_grid as lg1 
LEFT JOIN location_grid as a0 ON lg1.admin0_grid_id=a0.grid_id
WHERE lg1.country_code NOT IN (
SELECT lg22.country_code FROM location_grid lg22 WHERE lg22.level_name = 'admin2' GROUP BY lg22.country_code
) AND lg1.level_name != 'admin0'


UNION ALL
-- 
# Has admin2
-- 
SELECT lg2.grid_id,  lg2.name, a0.name as country, lg2.level_name
FROM location_grid lg2 
LEFT JOIN location_grid as a0 ON lg2.admin0_grid_id=a0.grid_id
WHERE lg2.level_name = 'admin2' ;
```

WITH GEOMETRY

```
-- 
# Only with admin0
-- 
SELECT   lg0.grid_id, lg0.name, a0.name as country, lg0.level_name, g0.geoJSON
FROM location_grid lg0
LEFT JOIN location_grid as a0 ON lg0.admin0_grid_id=a0.grid_id
LEFT JOIN location_grid_geometry as g0 ON lg0.grid_id=g0.grid_id
WHERE lg0.level < 1
AND lg0.country_code NOT IN (
	SELECT lg23.country_code FROM location_grid lg23 WHERE lg23.level_name = 'admin1' GROUP BY lg23.country_code
)


UNION ALL
-- 
# Only admin1
-- 
SELECT  lg1.grid_id,  lg1.name, a0.name as country,  lg1.level_name, g1.geoJSON
FROM location_grid as lg1 
LEFT JOIN location_grid as a0 ON lg1.admin0_grid_id=a0.grid_id
LEFT JOIN location_grid_geometry as g1 ON lg1.grid_id=g1.grid_id
WHERE lg1.country_code NOT IN (
SELECT lg22.country_code FROM location_grid lg22 WHERE lg22.level_name = 'admin2' GROUP BY lg22.country_code
) AND lg1.level_name != 'admin0'


UNION ALL
-- 
# Has admin2
-- 
SELECT lg2.grid_id,  lg2.name, a0.name as country, lg2.level_name, g2.geoJSON
FROM location_grid lg2 
LEFT JOIN location_grid as a0 ON lg2.admin0_grid_id=a0.grid_id
LEFT JOIN location_grid_geometry as g2 ON lg2.grid_id=g2.grid_id
WHERE lg2.level_name = 'admin2' ;
```


GRid ID ONLY for 46k toAdmin2

```
-- 
# Only with admin0
-- 
SELECT  lg0.grid_id
FROM location_grid lg0
WHERE lg0.level < 1
AND lg0.country_code NOT IN (
	SELECT lg23.country_code FROM location_grid lg23 WHERE lg23.level_name = 'admin1' GROUP BY lg23.country_code
)

UNION ALL
-- 
# Only admin1
-- 
SELECT  lg1.grid_id
FROM location_grid as lg1 
WHERE lg1.country_code NOT IN (
SELECT lg22.country_code FROM location_grid lg22 WHERE lg22.level_name = 'admin2' GROUP BY lg22.country_code
) AND lg1.level_name != 'admin0'

UNION ALL
-- 
# Has admin2
-- 
SELECT  lg2.grid_id
FROM location_grid lg2 
WHERE lg2.level_name = 'admin2';
```
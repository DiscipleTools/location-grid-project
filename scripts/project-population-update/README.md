# Merge Populations from Scraps

Made all values rounded to 100s
```

UPDATE location_grid
SET location_grid.population = CEILING (location_grid.population / 100) * 100;

```


```
SELECT lg.country_code, count(lg.grid_id) 
FROM location_grid lg
LEFT JOIN location_grid as a0 ON lg.admin0_grid_id=a0.grid_id
WHERE  lg.population < 1 
AND (lg.level < 3 OR ( a0.name IN ('China', 'India', 'France', 'Spain', 'Pakistan', 'Bangladesh') AND lg.level < 4 ) )
GROUP BY lg.country_code;
```

```
SELECT * FROM location_grid WHERE population < 1 AND country_code = 'BT';
```

```

# Remaining populations
SELECT lg.country_code, count(lg.grid_id) 
FROM location_grid lg
LEFT JOIN location_grid as a0 ON lg.admin0_grid_id=a0.grid_id
WHERE  lg.population < 1 
AND (lg.level < 3 OR ( a0.name IN ('China', 'India', 'France', 'Spain', 'Pakistan', 'Bangladesh') AND lg.level < 4 ) )
GROUP BY lg.country_code;



# Working populations

SELECT lg.grid_id, lg.name, lg.level_name, a0.name as country, a1.name as state, a1.grid_id, lg.population, s.grid_id as sgrid, s.population as spop, (SELECT count(lgg.grid_id) FROM location_grid lgg WHERE lgg.admin1_grid_id=lg.admin1_grid_id ) as divisions
FROM (
SELECT * FROM location_grid WHERE population < 1 
AND country_code = 'AE' 
) as lg
LEFT JOIN location_grid as a0 ON lg.admin0_grid_id=a0.grid_id
LEFT JOIN location_grid as a1 ON lg.admin1_grid_id=a1.grid_id
LEFT JOIN 50k_scrape s ON s.grid_id=lg.grid_id
ORDER BY population, state ASC
;




#update script
UPDATE location_grid
SET location_grid.population = '3600'
WHERE population < 1 AND country_code = 'BT' AND admin1_grid_id = 100041144;










# Remaining populations
SELECT lg.country_code, count(lg.grid_id) AS count
FROM location_grid lg
LEFT JOIN location_grid as a0 ON lg.admin0_grid_id=a0.grid_id
WHERE  lg.population < 1 
AND (lg.level < 3 OR ( a0.name IN ('China', 'India', 'France', 'Spain', 'Pakistan', 'Bangladesh') AND lg.level < 4 ) )
GROUP BY lg.country_code
ORDER BY count DESC;



# admin level 2
UPDATE location_grid
INNER JOIN (
	SELECT lc.grid_id, lc.parent_id, lc.name, ROUND( ( SELECT lg0.population FROM location_grid lg0 WHERE lg0.grid_id=lc.admin0_grid_id ) / ( SELECT count(lgg.grid_id) FROM location_grid lgg WHERE lgg.admin1_grid_id=lc.admin1_grid_id ) ) as population
	FROM location_grid lc
	WHERE lc.country_code IN (
SELECT lg.country_code
FROM location_grid lg
LEFT JOIN location_grid as a0 ON lg.admin0_grid_id=a0.grid_id
WHERE  lg.population < 1 
AND (lg.level < 3 OR ( a0.name IN ('China', 'India', 'France', 'Spain', 'Pakistan', 'Bangladesh') AND lg.level < 4 ) )
GROUP BY lg.country_code
)  AND lc.level = 2 AND lc.population < 1
) as tb ON location_grid.grid_id = tb.grid_id 
SET location_grid.population = tb.population
;

# admin level 3
UPDATE location_grid
INNER JOIN (
	SELECT lc.grid_id, lc.parent_id, lc.name, ROUND( ( SELECT lg0.population FROM location_grid lg0 WHERE lg0.grid_id=lc.admin1_grid_id ) / ( SELECT count(lgg.grid_id) FROM location_grid lgg WHERE lgg.admin2_grid_id=lc.admin2_grid_id ) ) as population
	FROM location_grid lc
	WHERE lc.country_code IN (
SELECT lg.country_code
FROM location_grid lg
LEFT JOIN location_grid as a0 ON lg.admin0_grid_id=a0.grid_id
WHERE  lg.population < 1 
AND (lg.level < 3 OR ( a0.name IN ('China', 'India', 'France', 'Spain', 'Pakistan', 'Bangladesh') AND lg.level < 4 ) )
GROUP BY lg.country_code
)  AND lc.level = 3 AND population < 1
) as tb ON location_grid.grid_id = tb.grid_id 
SET location_grid.population = tb.population
;

```
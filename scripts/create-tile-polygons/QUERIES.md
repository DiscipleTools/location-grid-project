## Balanced admin2

```
SELECT
            lg1.grid_id, lg1.name, lg1.level, lg1.longitude, lg1.population, lg1.country_code, lg1.admin0_grid_id, lg1.admin1_grid_id, lg1.admin2_grid_id, lg1.admin3_grid_id, lg1.admin4_grid_id, lg1.admin5_grid_id
            FROM location_grid lg1
            WHERE lg1.level = 0
			AND lg1.grid_id NOT IN ( SELECT lg11.admin0_grid_id FROM location_grid lg11 WHERE lg11.level = 1 AND lg11.admin0_grid_id = lg1.grid_id )
 			#'China', 'India', 'France', 'Spain', 'Pakistan', 'Bangladesh'
            AND lg1.admin0_grid_id NOT IN (100050711,100219347,100074576,100259978,100018514)
            #'Romania', 'Estonia', 'Bhutan', 'Croatia', 'Solomon Islands', 'Guyana', 'Iceland', 'Vanuatu', 'Cape Verde', 'Samoa', 'Faroe Islands', 'Norway', 'Uruguay', 'Mongolia', 'United Arab Emirates', 'Slovenia', 'Bulgaria', 'Honduras', 'Columbia', 'Namibia', 'Switzerland', 'Western Sahara'
            AND lg1.admin0_grid_id NOT IN (100314737,100083318,100041128,100133112,100341242,100132648,100222839,100379914,100055707,100379993,100130389,100255271,100363975,100248845,100001527,100342458,100024289,100132795,100054605,100253456,100342975,100074571)

			UNION ALL
            --
            # admin 1 for countries that have no level 2 (3155)
            --
            SELECT
            lg2.grid_id, lg2.name, lg2.level, lg2.longitude,lg2.population, lg2.country_code, lg2.admin0_grid_id, lg2.admin1_grid_id, lg2.admin2_grid_id, lg2.admin3_grid_id, lg2.admin4_grid_id, lg2.admin5_grid_id
            FROM location_grid lg2
            WHERE lg2.level = 1
			AND lg2.grid_id NOT IN ( SELECT lg22.admin1_grid_id FROM location_grid lg22 WHERE lg22.level = 2 AND lg22.admin1_grid_id = lg2.grid_id )
             #'China', 'India', 'France', 'Spain', 'Pakistan', 'Bangladesh'
            AND lg2.admin0_grid_id NOT IN (100050711,100219347,100074576,100259978,100018514)
            #'Romania', 'Estonia', 'Bhutan', 'Croatia', 'Solomon Islands', 'Guyana', 'Iceland', 'Vanuatu', 'Cape Verde', 'Samoa', 'Faroe Islands', 'Norway', 'Uruguay', 'Mongolia', 'United Arab Emirates', 'Slovenia', 'Bulgaria', 'Honduras', 'Columbia', 'Namibia', 'Switzerland', 'Western Sahara'
            AND lg2.admin0_grid_id NOT IN (100314737,100083318,100041128,100133112,100341242,100132648,100222839,100379914,100055707,100379993,100130389,100255271,100363975,100248845,100001527,100342458,100024289,100132795,100054605,100253456,100342975,100074571)

			UNION ALL
			--
            # admin 2 all countries (37196)
            --
			SELECT
            lg3.grid_id, lg3.name, lg3.level,  lg3.longitude,lg3.population,  lg3.country_code, lg3.admin0_grid_id, lg3.admin1_grid_id, lg3.admin2_grid_id, lg3.admin3_grid_id, lg3.admin4_grid_id, lg3.admin5_grid_id
            FROM location_grid lg3
            WHERE lg3.level = 2
            #'China', 'India', 'France', 'Spain', 'Pakistan', 'Bangladesh'
            AND lg3.admin0_grid_id NOT IN (100050711,100219347,100074576,100259978,100018514)
            #'Romania', 'Estonia', 'Bhutan', 'Croatia', 'Solomon Islands', 'Guyana', 'Iceland', 'Vanuatu', 'Cape Verde', 'Samoa', 'Faroe Islands', 'Norway', 'Uruguay', 'Mongolia', 'United Arab Emirates', 'Slovenia', 'Bulgaria', 'Honduras', 'Columbia', 'Namibia', 'Switzerland', 'Western Sahara'
            AND lg3.admin0_grid_id NOT IN (100314737,100083318,100041128,100133112,100341242,100132648,100222839,100379914,100055707,100379993,100130389,100255271,100363975,100248845,100001527,100342458,100024289,100132795,100054605,100253456,100342975,100074571)

			UNION ALL
            --
            # admin 1 for little highly divided countries (352)
            --
            SELECT
            lg4.grid_id, lg4.name, lg4.level, lg4.longitude,lg4.population,  lg4.country_code, lg4.admin0_grid_id, lg4.admin1_grid_id, lg4.admin2_grid_id, lg4.admin3_grid_id, lg4.admin4_grid_id, lg4.admin5_grid_id
            FROM location_grid lg4
            WHERE lg4.level = 1
            #'China', 'India', 'France', 'Spain', 'Pakistan', 'Bangladesh'
            AND lg4.admin0_grid_id NOT IN (100050711,100219347,100074576,100259978,100018514)
            #'Romania', 'Estonia', 'Bhutan', 'Croatia', 'Solomon Islands', 'Guyana', 'Iceland', 'Vanuatu', 'Cape Verde', 'Samoa', 'Faroe Islands', 'Norway', 'Uruguay', 'Mongolia', 'United Arab Emirates', 'Slovenia', 'Bulgaria', 'Honduras', 'Columbia', 'Namibia', 'Switzerland', 'Western Sahara'
            AND lg4.admin0_grid_id IN (100314737,100083318,100041128,100133112,100341242,100132648,100222839,100379914,100055707,100379993,100130389,100255271,100363975,100248845,100001527,100342458,100024289,100132795,100054605,100253456,100342975,100074571)

			UNION ALL

 			--
            # admin 3 for big countries (5803)
            --
            SELECT
            lg5.grid_id, lg5.name, lg5.level, lg5.longitude, lg5.population,  lg5.country_code, lg5.admin0_grid_id, lg5.admin1_grid_id, lg5.admin2_grid_id, lg5.admin3_grid_id, lg5.admin4_grid_id, lg5.admin5_grid_id
            FROM location_grid as lg5
            WHERE
            lg5.level = 3
            #'China', 'India', 'France', 'Spain', 'Pakistan', 'Bangladesh'
            AND lg5.admin0_grid_id IN (100050711,100219347,100074576,100259978,100018514)
            #'Romania', 'Estonia', 'Bhutan', 'Croatia', 'Solomon Islands', 'Guyana', 'Iceland', 'Vanuatu', 'Cape Verde', 'Samoa', 'Faroe Islands', 'Norway', 'Uruguay', 'Mongolia', 'United Arab Emirates', 'Slovenia', 'Bulgaria', 'Honduras', 'Columbia', 'Namibia', 'Switzerland', 'Western Sahara'
            AND lg5.admin0_grid_id NOT IN (100314737,100083318,100041128,100133112,100341242,100132648,100222839,100379914,100055707,100379993,100130389,100255271,100363975,100248845,100001527,100342458,100024289,100132795,100054605,100253456,100342975,100074571)
```










```
SELECT  lg0.grid_id
    FROM location_grid lg0
LEFT JOIN location_grid as a0 ON lg0.admin0_grid_id=a0.grid_id
    WHERE lg0.level < 1
    AND lg0.country_code NOT IN (
        SELECT lg23.country_code FROM location_grid lg23 WHERE lg23.level_name = 'admin1' GROUP BY lg23.country_code
    )
AND a0.name NOT IN ('China', 'India', 'France', 'Spain', 'Pakistan', 'Bangladesh')
    AND a0.name NOT IN ('Romania', 'Estonia', 'Bhutan', 'Croatia', 'Solomon Islands', 'Guyana', 'Iceland', 'Vanuatu', 'Cape Verde', 'Samoa', 'Faroe Islands', 'Norway', 'Uruguay', 'Mongolia', 'United Arab Emirates', 'Slovenia', 'Bulgaria', 'Honduras', 'Columbia', 'Namibia', 'Switzerland', 'Guatemala')

    
    UNION ALL
    -- 
    # Only admin1
    -- 
    SELECT  lg1.grid_id
    FROM location_grid as lg1 
LEFT JOIN location_grid as a0 ON lg1.admin0_grid_id=a0.grid_id
    WHERE lg1.country_code NOT IN (
    SELECT lg22.country_code FROM location_grid lg22 WHERE lg22.level_name = 'admin2' GROUP BY lg22.country_code
    ) AND lg1.level_name != 'admin0'
AND a0.name NOT IN ('China', 'India', 'France', 'Spain', 'Pakistan', 'Bangladesh')
    AND a0.name NOT IN ('Romania', 'Estonia', 'Bhutan', 'Croatia', 'Solomon Islands', 'Guyana', 'Iceland', 'Vanuatu', 'Cape Verde', 'Samoa', 'Faroe Islands', 'Norway', 'Uruguay', 'Mongolia', 'United Arab Emirates', 'Slovenia', 'Bulgaria', 'Honduras', 'Columbia', 'Namibia', 'Switzerland', 'Guatemala')
    
    UNION ALL
    -- 
    # Has admin2
    -- 
    SELECT  lg2.grid_id
    FROM location_grid lg2 
LEFT JOIN location_grid as a0 ON lg2.admin0_grid_id=a0.grid_id
    WHERE lg2.level_name = 'admin2'
AND a0.name NOT IN ('China', 'India', 'France', 'Spain', 'Pakistan', 'Bangladesh')
    AND a0.name NOT IN ('Romania', 'Estonia', 'Bhutan', 'Croatia', 'Solomon Islands', 'Guyana', 'Iceland', 'Vanuatu', 'Cape Verde', 'Samoa', 'Faroe Islands', 'Norway', 'Uruguay', 'Mongolia', 'United Arab Emirates', 'Slovenia', 'Bulgaria', 'Honduras', 'Columbia', 'Namibia', 'Switzerland', 'Guatemala')


UNION ALL

    SELECT  lg3.grid_id
    FROM location_grid as lg3 
	LEFT JOIN location_grid as a0 ON lg3.admin0_grid_id=a0.grid_id
		WHERE lg3.level = 3
AND a0.name IN ('China', 'India', 'France', 'Spain', 'Pakistan', 'Bangladesh')

UNION ALL

    SELECT  lg3.grid_id
    FROM location_grid as lg3 
	LEFT JOIN location_grid as a0 ON lg3.admin0_grid_id=a0.grid_id
		WHERE lg3.level = 1
AND a0.name IN ('Romania', 'Estonia', 'Bhutan', 'Croatia', 'Solomon Islands', 'Guyana', 'Iceland', 'Vanuatu', 'Cape Verde', 'Samoa', 'Faroe Islands', 'Norway', 'Uruguay', 'Mongolia', 'United Arab Emirates', 'Slovenia', 'Bulgaria', 'Honduras', 'Columbia', 'Namibia', 'Switzerland', 'Guatemala')
```
















## All countries with counts and exceptions
```
SELECT 
-- SUM(tbl.count)
*
FROM (
-- 
# Only with admin0
-- 
SELECT  a0.grid_id, a0.name, lg0.level, count(lg0.grid_id) as count, a0.population
FROM location_grid lg0
LEFT JOIN location_grid as a0 ON lg0.admin0_grid_id=a0.grid_id
WHERE lg0.level < 1
AND lg0.country_code NOT IN (
	SELECT lg23.country_code FROM location_grid lg23 WHERE lg23.level_name = 'admin1' GROUP BY lg23.country_code
) 
AND a0.name NOT IN ('China', 'India', 'France', 'Spain', 'Pakistan', 'Bangladesh')
AND a0.name NOT IN ('Romania', 'Estonia', 'Bhutan', 'Croatia', 'Solomon Islands', 'Guyana', 'Iceland', 'Vanuatu', 'Cape Verde', 'Samoa', 'Faroe Islands', 'Norway', 'Uruguay', 'Mongolia', 'United Arab Emirates', 'Slovenia', 'Bulgaria', 'Honduras', 'Columbia', 'Namibia', 'Switzerland', 'Western Sahara')
GROUP BY a0.grid_id, a0.name, a0.population, lg0.level

UNION ALL
-- 
# Only admin1
-- 
SELECT 
a0.grid_id, a0.name as admin0_name, lg1.level, count(lg1.grid_id) as count, a0.population
FROM location_grid as lg1 
LEFT JOIN location_grid as a0 ON lg1.admin0_grid_id=a0.grid_id
WHERE lg1.country_code NOT IN (
SELECT lg22.country_code FROM location_grid lg22 WHERE lg22.level_name = 'admin2' GROUP BY lg22.country_code
) AND lg1.level_name != 'admin0' 
AND a0.name NOT IN ('China', 'India', 'France', 'Spain', 'Pakistan', 'Bangladesh')
AND a0.name NOT IN ('Romania', 'Estonia', 'Bhutan', 'Croatia', 'Solomon Islands', 'Guyana', 'Iceland', 'Vanuatu', 'Cape Verde', 'Samoa', 'Faroe Islands', 'Norway', 'Uruguay', 'Mongolia', 'United Arab Emirates', 'Slovenia', 'Bulgaria', 'Honduras', 'Columbia', 'Namibia', 'Switzerland', 'Western Sahara')
GROUP BY a0.grid_id, a0.name, a0.population, lg1.level

UNION ALL
-- 
# Has admin2
-- 
SELECT a0.grid_id, a0.name, lg2.level, count(lg2.grid_id) as count, a0.population
FROM location_grid lg2 
LEFT JOIN location_grid as a0 ON lg2.admin0_grid_id=a0.grid_id
WHERE lg2.level_name = 'admin2' 
AND a0.name NOT IN ('China', 'India', 'France', 'Spain', 'Pakistan', 'Bangladesh')
AND a0.name NOT IN ('Romania', 'Estonia', 'Bhutan', 'Croatia', 'Solomon Islands', 'Guyana', 'Iceland', 'Vanuatu', 'Cape Verde', 'Samoa', 'Faroe Islands', 'Norway', 'Uruguay', 'Mongolia', 'United Arab Emirates', 'Slovenia', 'Bulgaria', 'Honduras', 'Columbia', 'Namibia', 'Switzerland', 'Western Sahara')
GROUP BY a0.grid_id, a0.name, a0.population, lg2.level

UNION ALL

# Exceptions admin3

SELECT a0.grid_id, a0.name, lge.level, count(lge.grid_id) as count, a0.population
FROM location_grid lge 
LEFT JOIN location_grid as a0 ON lge.admin0_grid_id=a0.grid_id
WHERE a0.name IN ('China', 'India', 'France', 'Spain', 'Pakistan', 'Bangladesh')
	AND lge.level_name = 'admin3' 
GROUP BY a0.grid_id, a0.name, a0.population, lge.level

UNION ALL

# Exceptions admin1

SELECT a0.grid_id, a0.name, lge1.level, count(lge1.grid_id) as count, a0.population
FROM location_grid lge1 
LEFT JOIN location_grid as a0 ON lge1.admin0_grid_id=a0.grid_id
WHERE lge1.level_name = 'admin1' 
AND a0.name IN ('Romania', 'Estonia', 'Bhutan', 'Croatia', 'Solomon Islands', 'Guyana', 'Iceland', 'Vanuatu', 'Cape Verde', 'Samoa', 'Faroe Islands', 'Norway', 'Uruguay', 'Mongolia', 'United Arab Emirates', 'Slovenia', 'Bulgaria', 'Honduras', 'Columbia', 'Namibia', 'Switzerland', 'Western Sahara')
GROUP BY a0.grid_id, a0.name, a0.population, lge1.level

) as tbl
ORDER BY population
```


## Saturation List (grid_id, name, country_name, level, population)
```
SELECT 
-- SUM(tbl.count)
*
FROM (
-- 
# Only with admin0
-- 
SELECT  
lg0.grid_id, lg0.name, a0.grid_id as admin0_grid_id, a0.name as country_name, lg0.level, lg0.population
FROM location_grid lg0
LEFT JOIN location_grid as a0 ON lg0.admin0_grid_id=a0.grid_id
WHERE lg0.level < 1
AND lg0.country_code NOT IN (
	SELECT lg23.country_code FROM location_grid lg23 WHERE lg23.level_name = 'admin1' GROUP BY lg23.country_code
) 
AND a0.name NOT IN ('China', 'India', 'France', 'Spain', 'Pakistan', 'Bangladesh')
AND a0.name NOT IN ('Romania', 'Estonia', 'Bhutan', 'Croatia', 'Solomon Islands', 'Guyana', 'Iceland', 'Vanuatu', 'Cape Verde', 'Samoa', 'Faroe Islands', 'Norway', 'Uruguay', 'Mongolia', 'United Arab Emirates', 'Slovenia', 'Bulgaria', 'Honduras', 'Columbia', 'Namibia', 'Switzerland', 'Western Sahara')

UNION ALL
-- 
# Only admin1
-- 
SELECT 
lg1.grid_id, lg1.name, a0.grid_id as admin0_grid_id, a0.name as country_name, lg1.level,  lg1.population
FROM location_grid as lg1 
LEFT JOIN location_grid as a0 ON lg1.admin0_grid_id=a0.grid_id
WHERE lg1.country_code NOT IN (
SELECT lg22.country_code FROM location_grid lg22 WHERE lg22.level_name = 'admin2' GROUP BY lg22.country_code
) AND lg1.level_name != 'admin0' 
AND a0.name NOT IN ('China', 'India', 'France', 'Spain', 'Pakistan', 'Bangladesh')
AND a0.name NOT IN ('Romania', 'Estonia', 'Bhutan', 'Croatia', 'Solomon Islands', 'Guyana', 'Iceland', 'Vanuatu', 'Cape Verde', 'Samoa', 'Faroe Islands', 'Norway', 'Uruguay', 'Mongolia', 'United Arab Emirates', 'Slovenia', 'Bulgaria', 'Honduras', 'Columbia', 'Namibia', 'Switzerland', 'Western Sahara')


UNION ALL
-- 
# Has admin2
-- 
SELECT 
lg2.grid_id, lg2.name, a0.grid_id as admin0_grid_id, a0.name as country_name, lg2.level, lg2.population
FROM location_grid lg2 
LEFT JOIN location_grid as a0 ON lg2.admin0_grid_id=a0.grid_id
WHERE lg2.level_name = 'admin2' 
AND a0.name NOT IN ('China', 'India', 'France', 'Spain', 'Pakistan', 'Bangladesh')
AND a0.name NOT IN ('Romania', 'Estonia', 'Bhutan', 'Croatia', 'Solomon Islands', 'Guyana', 'Iceland', 'Vanuatu', 'Cape Verde', 'Samoa', 'Faroe Islands', 'Norway', 'Uruguay', 'Mongolia', 'United Arab Emirates', 'Slovenia', 'Bulgaria', 'Honduras', 'Columbia', 'Namibia', 'Switzerland', 'Western Sahara')

UNION ALL

# Exceptions admin3

SELECT 
lge.grid_id, lge.name, a0.grid_id as admin0_grid_id, a0.name as country_name, lge.level, lge.population
FROM location_grid lge 
LEFT JOIN location_grid as a0 ON lge.admin0_grid_id=a0.grid_id
WHERE a0.name IN ('China', 'India', 'France', 'Spain', 'Pakistan', 'Bangladesh')
	AND lge.level_name = 'admin3' 


UNION ALL

# Exceptions admin1

SELECT 
lge1.grid_id, lge1.name, a0.grid_id as admin0_grid_id, a0.name as country_name, lge1.level, lge1.population
FROM location_grid lge1 
LEFT JOIN location_grid as a0 ON lge1.admin0_grid_id=a0.grid_id
WHERE lge1.level_name = 'admin1' 
AND a0.name IN ('Romania', 'Estonia', 'Bhutan', 'Croatia', 'Solomon Islands', 'Guyana', 'Iceland', 'Vanuatu', 'Cape Verde', 'Samoa', 'Faroe Islands', 'Norway', 'Uruguay', 'Mongolia', 'United Arab Emirates', 'Slovenia', 'Bulgaria', 'Honduras', 'Columbia', 'Namibia', 'Switzerland', 'Western Sahara')

) as tbl
ORDER BY country_name, name;
```




```
SELECT 
-- SUM(tbl.count)
*
FROM (
-- 
# Only with admin0
-- 
SELECT  
lg0.grid_id, lg0.name, a0.grid_id as admin0_grid_id, a0.name as country_name, lg0.level, lg0.population
FROM location_grid lg0
LEFT JOIN location_grid as a0 ON lg0.admin0_grid_id=a0.grid_id
WHERE lg0.level < 1
AND lg0.country_code NOT IN (
	SELECT lg23.country_code FROM location_grid lg23 WHERE lg23.level_name = 'admin1' GROUP BY lg23.country_code
) 
AND a0.name NOT IN ('China', 'India', 'France', 'Spain', 'Pakistan', 'Bangladesh')
AND a0.name NOT IN ('Romania', 'Estonia', 'Bhutan', 'Croatia', 'Solomon Islands', 'Guyana', 'Iceland', 'Vanuatu', 'Cape Verde', 'Samoa', 'Faroe Islands', 'Norway', 'Uruguay', 'Mongolia', 'United Arab Emirates', 'Slovenia', 'Bulgaria', 'Honduras', 'Columbia', 'Namibia', 'Switzerland', 'Western Sahara')

UNION ALL
-- 
# Only admin1
-- 
SELECT 
lg1.grid_id, lg1.name, a0.grid_id as admin0_grid_id, a0.name as country_name, lg1.level,  lg1.population
FROM location_grid as lg1 
LEFT JOIN location_grid as a0 ON lg1.admin0_grid_id=a0.grid_id
WHERE lg1.country_code NOT IN (
SELECT lg22.country_code FROM location_grid lg22 WHERE lg22.level_name = 'admin2' GROUP BY lg22.country_code
) AND lg1.level_name != 'admin0' 
AND a0.name NOT IN ('China', 'India', 'France', 'Spain', 'Pakistan', 'Bangladesh')
AND a0.name NOT IN ('Romania', 'Estonia', 'Bhutan', 'Croatia', 'Solomon Islands', 'Guyana', 'Iceland', 'Vanuatu', 'Cape Verde', 'Samoa', 'Faroe Islands', 'Norway', 'Uruguay', 'Mongolia', 'United Arab Emirates', 'Slovenia', 'Bulgaria', 'Honduras', 'Columbia', 'Namibia', 'Switzerland', 'Western Sahara')


UNION ALL
-- 
# Has admin2
-- 
SELECT 
lg2.grid_id, lg2.name, a0.grid_id as admin0_grid_id, a0.name as country_name, lg2.level, lg2.population
FROM location_grid lg2 
LEFT JOIN location_grid as a0 ON lg2.admin0_grid_id=a0.grid_id
WHERE lg2.level_name = 'admin2' 
AND a0.name NOT IN ('China', 'India', 'France', 'Spain', 'Pakistan', 'Bangladesh')
AND a0.name NOT IN ('Romania', 'Estonia', 'Bhutan', 'Croatia', 'Solomon Islands', 'Guyana', 'Iceland', 'Vanuatu', 'Cape Verde', 'Samoa', 'Faroe Islands', 'Norway', 'Uruguay', 'Mongolia', 'United Arab Emirates', 'Slovenia', 'Bulgaria', 'Honduras', 'Columbia', 'Namibia', 'Switzerland', 'Western Sahara')

UNION ALL

# Exceptions admin3

SELECT 
lge.grid_id, lge.name, a0.grid_id as admin0_grid_id, a0.name as country_name, lge.level, lge.population
FROM location_grid lge 
LEFT JOIN location_grid as a0 ON lge.admin0_grid_id=a0.grid_id
WHERE a0.name IN ('China', 'India', 'France', 'Spain', 'Pakistan', 'Bangladesh')
	AND lge.level_name = 'admin3' 


UNION ALL

# Exceptions admin1

SELECT 
lge1.grid_id, lge1.name, a0.grid_id as admin0_grid_id, a0.name as country_name, lge1.level, lge1.population
FROM location_grid lge1 
LEFT JOIN location_grid as a0 ON lge1.admin0_grid_id=a0.grid_id
WHERE lge1.level_name = 'admin1' 
AND a0.name IN ('Romania', 'Estonia', 'Bhutan', 'Croatia', 'Solomon Islands', 'Guyana', 'Iceland', 'Vanuatu', 'Cape Verde', 'Samoa', 'Faroe Islands', 'Norway', 'Uruguay', 'Mongolia', 'United Arab Emirates', 'Slovenia', 'Bulgaria', 'Honduras', 'Columbia', 'Namibia', 'Switzerland', 'Western Sahara')

) as tbl
ORDER BY country_name, name;





SELECT 
-- SUM(tbl.count)
*
FROM (

    -- 
    # Only with admin0
    -- 
    SELECT  
	a0.grid_id, a0.name, lg0.level_name, count(lg0.grid_id) as count
    FROM location_grid lg0
    LEFT JOIN location_grid as a0 ON lg0.admin0_grid_id=a0.grid_id
    WHERE lg0.level < 1
    AND lg0.country_code NOT IN (
        SELECT lg23.country_code FROM location_grid lg23 WHERE lg23.level_name = 'admin1' GROUP BY lg23.country_code
    )
	AND a0.name NOT IN ('China', 'India', 'France', 'Spain', 'Pakistan', 'Bangladesh')
	AND a0.name NOT IN ('Romania', 'Estonia', 'Bhutan', 'Croatia', 'Solomon Islands', 'Guyana', 'Iceland', 'Vanuatu', 'Cape Verde', 'Samoa', 'Faroe Islands', 'Norway', 'Uruguay', 'Mongolia', 'United Arab Emirates', 'Slovenia', 'Bulgaria', 'Honduras', 'Columbia', 'Namibia', 'Switzerland', 'Western Sahara')
    GROUP BY  a0.grid_id, a0.name, lg0.level_name
    
    UNION ALL
    -- 
    # Only admin1
    -- 
    SELECT 
    a0.grid_id, a0.name, lg1.level_name, count(lg1.grid_id) as count
    FROM location_grid as lg1 
    LEFT JOIN location_grid as a0 ON lg1.admin0_grid_id=a0.grid_id
    WHERE lg1.country_code NOT IN (
    SELECT lg22.country_code FROM location_grid lg22 WHERE lg22.level_name = 'admin2' GROUP BY lg22.country_code
    ) AND lg1.level_name != 'admin0'
	AND a0.name NOT IN ('China', 'India', 'France', 'Spain', 'Pakistan', 'Bangladesh')
	AND a0.name NOT IN ('Romania', 'Estonia', 'Bhutan', 'Croatia', 'Solomon Islands', 'Guyana', 'Iceland', 'Vanuatu', 'Cape Verde', 'Samoa', 'Faroe Islands', 'Norway', 'Uruguay', 'Mongolia', 'United Arab Emirates', 'Slovenia', 'Bulgaria', 'Honduras', 'Columbia', 'Namibia', 'Switzerland', 'Western Sahara')
    GROUP BY a0.grid_id, a0.name, lg1.level_name
    
    UNION ALL
    -- 
    # Has admin2
    -- 
    SELECT 
	a0.grid_id, a0.name, lg2.level_name, count(lg2.grid_id)  as count
    FROM location_grid lg2 
    LEFT JOIN location_grid as a0 ON lg2.admin0_grid_id=a0.grid_id
    WHERE lg2.level_name = 'admin2' 
	AND a0.name NOT IN ('China', 'India', 'France', 'Spain', 'Pakistan', 'Bangladesh')
	AND a0.name NOT IN ('Romania', 'Estonia', 'Bhutan', 'Croatia', 'Solomon Islands', 'Guyana', 'Iceland', 'Vanuatu', 'Cape Verde', 'Samoa', 'Faroe Islands', 'Norway', 'Uruguay', 'Mongolia', 'United Arab Emirates', 'Slovenia', 'Bulgaria', 'Honduras', 'Columbia', 'Namibia', 'Switzerland', 'Western Sahara')
    GROUP BY a0.grid_id, a0.name, lg2.level_name


 UNION ALL
	# Exceptions admin3

SELECT 
a0.grid_id, a0.name, lge.level_name, count(lge.grid_id)  as count
FROM location_grid lge 
LEFT JOIN location_grid as a0 ON lge.admin0_grid_id=a0.grid_id
WHERE a0.name IN ('China', 'India', 'France', 'Spain', 'Pakistan', 'Bangladesh')
	AND lge.level_name = 'admin3' 
GROUP BY a0.grid_id, a0.name, lge.level_name


UNION ALL

# Exceptions admin1

SELECT 
a0.grid_id, a0.name, lge1.level_name, count(lge1.grid_id) as count
FROM location_grid lge1 
LEFT JOIN location_grid as a0 ON lge1.admin0_grid_id=a0.grid_id
WHERE lge1.level_name = 'admin1' 
AND a0.name IN ('Romania', 'Estonia', 'Bhutan', 'Croatia', 'Solomon Islands', 'Guyana', 'Iceland', 'Vanuatu', 'Cape Verde', 'Samoa', 'Faroe Islands', 'Norway', 'Uruguay', 'Mongolia', 'United Arab Emirates', 'Slovenia', 'Bulgaria', 'Honduras', 'Columbia', 'Namibia', 'Switzerland', 'Western Sahara')
GROUP BY a0.grid_id, a0.name, lge1.level_name

) as tbl
ORDER BY name;

;
```





## All the admin 2, and all admin1 without admin2, and all with only admin0

Total geographies down to admin2:

- 46,780 admin0, admin1, admin2 unique
- 166 countries with admin2
- 62 countries with admin1 only
- 24 countreis with admin0 only

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


## Full list of all areas.
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

## Four Column
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

## WITH GEOMETRY

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


## Grid ID ONLY for 46k toAdmin2

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



# Build toAdmin2 Project

1. Build world/countries outline `build-world-outlines.php`
1. Reduce to 0.01 `simplify-polygons.php 0.01`
1. Build countries `build-geojson-for-countries.php`
1. Move all files lower than 300k to simplify-polygons folder
1. Reduce to 5percent `simplify-polygons.php 5`. 
1. Delete all files 300k+ from simplify folder
1. Reduce to 1percent `simplify-polygons.php 1`. 
1. Delete all files 300k+ from simplify folder
1. Reduce to 0.5percent `simplify-polygons.php 0.5`. 
1. Delete all files 300k+ from simplify folder
1. Reduce to 0.1percent `simplify-polygons.php 0.1`. 
1. Delete all files 300k+ from simplify folder
1. Reduce to 0.01percent `simplify-polygons.php 0.01`. 



````
Northern Africa	015
Sub-Saharan Africa	202
Latin America and the Caribbean	419
Northern America	021
Central Asia	143
Eastern Asia	030
South-eastern Asia	035
Southern Asia	034
Western Asia	145
Eastern Europe	151
Northern Europe	154
Southern Europe	039
Western Europe	155
Australia and New Zealand	053
Melanesia	054
Micronesia	057
Polynesia	061
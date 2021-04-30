# Merge Populations from Scraps

## Make all values rounded to 100s
```

UPDATE location_grid
SET location_grid.population = CEILING (location_grid.population / 100) * 100;

```

## Queries to select countries at their saturation levels (elements included)

```
#
#. These three select the new saturation model.
#

# all admin 2 without special
SELECT a0.name, count(*)
FROM dt_location_grid lg
JOIN dt_location_grid as a0 ON lg.admin0_grid_id=a0.grid_id
WHERE
	lg.level < 3
	AND a0.name NOT IN ('China', 'India', 'France', 'Spain', 'Pakistan', 'Bangladesh')
    AND a0.name NOT IN ('Romania', 'Estonia', 'Bhutan', 'Croatia', 'Solomon Islands', 'Guyana', 'Iceland', 'Vanuatu', 'Cape Verde', 'Samoa', 'Faroe Islands', 'Norway', 'Uruguay', 'Mongolia', 'United Arab Emirates', 'Slovenia', 'Bulgaria', 'Honduras', 'Columbia', 'Namibia', 'Switzerland', 'Western Sahara')
GROUP BY a0.name
;



# only admin3
SELECT a0.name, count(*)
FROM dt_location_grid lg
JOIN dt_location_grid as a0 ON lg.admin0_grid_id=a0.grid_id
WHERE
	lg.level < 4
	AND a0.name IN ('China', 'India', 'France', 'Spain', 'Pakistan', 'Bangladesh')
    AND a0.name NOT IN ('Romania', 'Estonia', 'Bhutan', 'Croatia', 'Solomon Islands', 'Guyana', 'Iceland', 'Vanuatu', 'Cape Verde', 'Samoa', 'Faroe Islands', 'Norway', 'Uruguay', 'Mongolia', 'United Arab Emirates', 'Slovenia', 'Bulgaria', 'Honduras', 'Columbia', 'Namibia', 'Switzerland', 'Western Sahara')
GROUP BY a0.name
;


# only admin 1
SELECT a0.name, count(*)
FROM dt_location_grid lg
JOIN dt_location_grid as a0 ON lg.admin0_grid_id=a0.grid_id
WHERE
	lg.level < 2
	AND a0.name NOT IN ('China', 'India', 'France', 'Spain', 'Pakistan', 'Bangladesh')
    AND a0.name IN ('Romania', 'Estonia', 'Bhutan', 'Croatia', 'Solomon Islands', 'Guyana', 'Iceland', 'Vanuatu', 'Cape Verde', 'Samoa', 'Faroe Islands', 'Norway', 'Uruguay', 'Mongolia', 'United Arab Emirates', 'Slovenia', 'Bulgaria', 'Honduras', 'Columbia', 'Namibia', 'Switzerland', 'Western Sahara')
GROUP BY a0.name
;

```
## DELETE QUERIES dt_location_grid

```
DELETE FROM dt_location_grid
WHERE level > 2
	#'China', 'India', 'France', 'Spain', 'Pakistan', 'Bangladesh'
	AND admin0_grid_id NOT IN (100050711,100219347,100074576,100259978,100018514) 
	#'Romania', 'Estonia', 'Bhutan', 'Croatia', 'Solomon Islands', 'Guyana', 'Iceland', 'Vanuatu', 'Cape Verde', 'Samoa', 'Faroe Islands', 'Norway', 'Uruguay', 'Mongolia', 'United Arab Emirates', 'Slovenia', 'Bulgaria', 'Honduras', 'Columbia', 'Namibia', 'Switzerland', 'Western Sahara'
	AND admin0_grid_id NOT IN (100314737,100083318,100041128,100133112,100341242,100132648,100222839,100379914,100055707,100379993,100130389,100255271,100363975,100248845,100001527,100342458,100024289,100132795,100054605,100253456,100342975,100074571)
;

DELETE FROM dt_location_grid
WHERE level > 3
	#'China', 'India', 'France', 'Spain', 'Pakistan', 'Bangladesh'
	AND admin0_grid_id IN (100050711,100219347,100074576,100259978,100018514) 
	#'Romania', 'Estonia', 'Bhutan', 'Croatia', 'Solomon Islands', 'Guyana', 'Iceland', 'Vanuatu', 'Cape Verde', 'Samoa', 'Faroe Islands', 'Norway', 'Uruguay', 'Mongolia', 'United Arab Emirates', 'Slovenia', 'Bulgaria', 'Honduras', 'Columbia', 'Namibia', 'Switzerland', 'Western Sahara'
	AND admin0_grid_id NOT IN (100314737,100083318,100041128,100133112,100341242,100132648,100222839,100379914,100055707,100379993,100130389,100255271,100363975,100248845,100001527,100342458,100024289,100132795,100054605,100253456,100342975,100074571)
;


DELETE FROM dt_location_grid
WHERE level > 1
	#'China', 'India', 'France', 'Spain', 'Pakistan', 'Bangladesh'
	AND admin0_grid_id NOT IN (100050711,100219347,100074576,100259978,100018514) 
	#'Romania', 'Estonia', 'Bhutan', 'Croatia', 'Solomon Islands', 'Guyana', 'Iceland', 'Vanuatu', 'Cape Verde', 'Samoa', 'Faroe Islands', 'Norway', 'Uruguay', 'Mongolia', 'United Arab Emirates', 'Slovenia', 'Bulgaria', 'Honduras', 'Columbia', 'Namibia', 'Switzerland', 'Western Sahara'
	AND admin0_grid_id IN (100314737,100083318,100041128,100133112,100341242,100132648,100222839,100379914,100055707,100379993,100130389,100255271,100363975,100248845,100001527,100342458,100024289,100132795,100054605,100253456,100342975,100074571)
;

SELECT count(*) FROM dt_location_grid;
```

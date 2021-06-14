# Saturation Tiles

46528 records
```
--
# Total records (46528)
--
--
# admin 0 for a few country states and islands that have no level 1 (27)
--
SELECT
lg1.grid_id, lg1.name, lg1.level,lg1.population, ROUND( lg1.population / 5000 ) as needby5k, ROUND( lg1.population / 50000 ) as needby50k, lg1.country_code, lg1.admin0_grid_id, lg1.admin1_grid_id, lg1.admin2_grid_id, lg1.admin3_grid_id, lg1.admin4_grid_id, lg1.admin5_grid_id
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
lg2.grid_id, lg2.name, lg2.level,lg2.population, ROUND( lg2.population / 5000 ) as needby5k, ROUND( lg2.population / 50000 ) as needby50k, lg2.country_code, lg2.admin0_grid_id, lg2.admin1_grid_id, lg2.admin2_grid_id, lg2.admin3_grid_id, lg2.admin4_grid_id, lg2.admin5_grid_id
FROM location_grid lg2
WHERE lg2.level = 1
AND lg2.grid_id NOT IN ( SELECT lg22.admin0_grid_id FROM location_grid lg22 WHERE lg22.level = 2 AND lg22.admin0_grid_id = lg2.grid_id )
 #'China', 'India', 'France', 'Spain', 'Pakistan', 'Bangladesh'
AND lg2.admin0_grid_id NOT IN (100050711,100219347,100074576,100259978,100018514)
#'Romania', 'Estonia', 'Bhutan', 'Croatia', 'Solomon Islands', 'Guyana', 'Iceland', 'Vanuatu', 'Cape Verde', 'Samoa', 'Faroe Islands', 'Norway', 'Uruguay', 'Mongolia', 'United Arab Emirates', 'Slovenia', 'Bulgaria', 'Honduras', 'Columbia', 'Namibia', 'Switzerland', 'Western Sahara'
AND lg2.admin0_grid_id NOT IN (100314737,100083318,100041128,100133112,100341242,100132648,100222839,100379914,100055707,100379993,100130389,100255271,100363975,100248845,100001527,100342458,100024289,100132795,100054605,100253456,100342975,100074571)

UNION ALL
--
# admin 2 all countries (37196)
--
SELECT
lg3.grid_id, lg3.name, lg3.level, lg3.population, ROUND( lg3.population / 5000 ) as needby5k, ROUND( lg3.population / 50000) as needby50k, lg3.country_code, lg3.admin0_grid_id, lg3.admin1_grid_id, lg3.admin2_grid_id, lg3.admin3_grid_id, lg3.admin4_grid_id, lg3.admin5_grid_id
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
lg4.grid_id, lg4.name, lg4.level,lg4.population, ROUND( lg4.population / 5000 ) as needby5k, ROUND( lg4.population / 50000 ) as needby50k, lg4.country_code, lg4.admin0_grid_id, lg4.admin1_grid_id, lg4.admin2_grid_id, lg4.admin3_grid_id, lg4.admin4_grid_id, lg4.admin5_grid_id
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
lg5.grid_id, lg5.name, lg5.level, lg5.population, ROUND( lg5.population / 5000 ) as needby5k, ROUND( lg5.population / 50000 ) as needby50k, lg5.country_code, lg5.admin0_grid_id, lg5.admin1_grid_id, lg5.admin2_grid_id, lg5.admin3_grid_id, lg5.admin4_grid_id, lg5.admin5_grid_id
FROM location_grid as lg5
WHERE
lg5.level = 3
#'China', 'India', 'France', 'Spain', 'Pakistan', 'Bangladesh'
AND lg5.admin0_grid_id IN (100050711,100219347,100074576,100259978,100018514)
#'Romania', 'Estonia', 'Bhutan', 'Croatia', 'Solomon Islands', 'Guyana', 'Iceland', 'Vanuatu', 'Cape Verde', 'Samoa', 'Faroe Islands', 'Norway', 'Uruguay', 'Mongolia', 'United Arab Emirates', 'Slovenia', 'Bulgaria', 'Honduras', 'Columbia', 'Namibia', 'Switzerland', 'Western Sahara'
AND lg5.admin0_grid_id NOT IN (100314737,100083318,100041128,100133112,100341242,100132648,100222839,100379914,100055707,100379993,100130389,100255271,100363975,100248845,100001527,100342458,100024289,100132795,100054605,100253456,100342975,100074571)
```


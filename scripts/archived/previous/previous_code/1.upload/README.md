1. Download `allCountries.zip` file from https://geonames.org 

1. http://download.geonames.org/export/dump/

1. Unzip and upload to a mysql database. This is the full source of the geonames data.

1. Next use the build script to create .csv files from this raw source.

#### Geonames Master Complete Records DB
This includes all admin areas, continents, and earth. Both as feature_class 'A' and 'P'.

```$xslt
SELECT * FROM dt_geonames 
WHERE 
	( feature_class = 'A' OR feature_class = 'P' OR feature_class = 'L' )
	AND ( feature_code LIKE 'ADM%' OR feature_code LIKE 'PLC%' OR feature_code LIKE 'PPLA%' OR feature_code = 'PPLC' OR feature_code = 'CONT' OR geonameid = '6295630' ) 
	AND feature_code NOT LIKE '%D' 
	AND feature_code NOT LIKE '%H'
```
master_geonames.csv
`445740` (size: 103.7mb installed)


#### Geonames Polygons
```$xslt
SELECT gp.geonamesid, gp.geoJSON 
FROM dt_geonames as g 
    INNER JOIN dt_geonames_polygons as gp 
    ON g.geonameid=gp.geonamesid 
```
master_polygons.csv
`236619`(size: 6GB installed)


#### Geonames Hierarchy
```$xslt
DELETE FROM dt_geonames_hierarchy WHERE id NOT IN ( SELECT geonameid FROM dt_geonames ) OR parent_id NOT IN ( SELECT geonameid FROM dt_geonames )
```

master_hierarchy.csv `296897` (16kb installed)

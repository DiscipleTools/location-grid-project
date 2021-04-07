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
```

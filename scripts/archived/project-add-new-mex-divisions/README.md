### Add new admin divisions to update location grid

This example is adding two new counties in a state in Mexico. The original 
data set has three counties in Baja California Sur, but there are now
5 divisions.

I'm attempting to document the process in case any part of it is reproducible.

1. Find the grid_ids for the current state. 100245406 Mulegé, 100245404 Comondú, 100245405 La Paz inside
the state of 100245360 Baja California Sur. 

2. Collect high quality public shape files. Found these at https://data.humdata.org/dataset/mexican-administrative-level-0-country-1-estado-and-2-municipio-boundary-polygons

3. Identify admin 2 shape file. ( It does not come with any properties so the shape
geometrics cannot directly be identified. This will require pulling the shape file apart
giving an id to each of the polygons and then figuring out which polygons to abstract.)

4. Used MapShaper to convert .shp file to .geojson.

5. Then add ids to the geojson layer and output a new geojson. Used script 
add-id-to-geojson.php 

6. With the new layer I used mapshaper to find the polygons that I wanted for the upgrade.
Ids 1068 Mulege, 433 Comundo, 925 Loreto, 887 La Paz, 928 Los Cabos

7. Exported all polygons individually using script export-individual-polygons.php

8. The missing municipalities are Loreto and Los Cabos. Add these two new lines to the database
at in the next avalible grid_id numbers in the 100,000,000 - 200,000,000 range.

9. Added all the admin_0, admin_1, and admin_2 codes by hand to match the other records in that state.

10. Added population from wikipedia and census date to last_modified date.

11. Renamed the geojson files with the incremental numbers to the new grid_id numbers. i.e. 
928.geojson to 100386738.geojson

12. 

1068 - 
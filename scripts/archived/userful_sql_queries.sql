# Transfer to saturation_grid
UPDATE saturation_grid_geonames
    INNER JOIN sg_missing_polygons ON (saturation_grid_geonames.geonameid = sg_missing_polygons.geonameid) AND sg_missing_polygons.north_latitude IS NOT NULL
SET
    saturation_grid_geonames.north_latitude = sg_missing_polygons.north_latitude,
    saturation_grid_geonames.south_latitude = sg_missing_polygons.south_latitude,
    saturation_grid_geonames.west_longitude = sg_missing_polygons.west_longitude,
    saturation_grid_geonames.east_longitude = sg_missing_polygons.east_longitude;
DELETE FROM sg_missing_polygons WHERE north_latitude IS NOT NULL;



INSERT INTO saturation_grid_polygons (geonameid, geoJSON)
    SELECT geonameid, geoJSON FROM bosnia WHERE geoJSON IS NOT NULL;
DELETE FROM bosnia WHERE geoJSON IS NOT NULL;


LOAD DATA LOCAL INFILE '/Users/chris/Downloads/allShapes.txt'
INTO TABLE geonames_polygons
FIELDS TERMINATED BY '\t'
LINES TERMINATED BY '\n'
(geonameid,geoJSON);


# check for new polygons
INSERT INTO saturation_grid_polygons (geonameid, geoJSON)
    SELECT gp.geonameid, gp.geoJSON
    FROM geonames_polygons as gp
        JOIN saturation_grid_geonames as sg ON gp.geonameid=sg.geonameid
        LEFT JOIN saturation_grid_polygons as sgp ON gp.geonameid=sgp.geonameid
    WHERE sgp.geoJSON IS NULL;




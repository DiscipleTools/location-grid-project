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
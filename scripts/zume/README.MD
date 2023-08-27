Build the state level tiles in this order

### Step 1
```
$ php 1-geojson.php world.geojson
```
Notes: php (script language) 1-geojson.php (script file name) world.geojson (file name to create)

### Step 2
```
$ php 2-simplify.php world.geojson .1
```
Notes: php (script language) 2-simplify.php (script file name) world.geojson (file name to create) .1 (amount to simplify. 10 = 10%, 1 = 1%, .1 = .1%, etc.)

### Step 3
```
$ php 3-make-tiles.php world.geojson 500
```
Notes: php (script language) 3-make-tiles.php (script file name) world.geojson (file name to create) 500 (features per tile to create)

This creates 1.geojson, 2.geojson, etc.
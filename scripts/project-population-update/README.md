# Merge Populations from Scraps

Made all values rounded to 100s
```

UPDATE location_grid
SET location_grid.population = CEILING (location_grid.population / 100) * 100;

```


[]
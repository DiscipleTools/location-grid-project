# Create json_db
The json_db folder is a static file export of the location_grid. It can return a row in the location grid or row with 
children in json format without accessing a database. This amounts to speed in response.

## URLS for ACCESS
`https://{mirror-location}/json_db/`
`https://{mirror-location}/json_db/children/`

Example: [https://locationgridproject.ddns.net/location-grid-mirror/json_db/1.json](https://locationgridproject.ddns.net/location-grid-mirror/json_db/1.json)

## Build Process

1. Run `$ php convert-db-to-json_db.php {output dir}` // output directory optional. should end with .. json_db/


## Samples

Sample of the json

[https://locationgridproject.ddns.net/location-grid-mirror/json_db/1.json](https:///location-grid-mirror/json_db/1.json)
```
{
    grid_id: 1,
    name: "World",
    level: -3,
    level_name: "world",
    country_code: null,
    admin0_code: null,
    parent_id: null,
    admin0_grid_id: null,
    admin1_grid_id: null,
    admin2_grid_id: null,
    admin3_grid_id: null,
    admin4_grid_id: null,
    admin5_grid_id: null,
    longitude: 0,
    latitude: 0,
    north_latitude: null,
    south_latitude: null,
    east_longitude: null,
    west_longitude: null,
    population: 7600000000,
    geonames_ref: "6295630",
    wikidata_ref: null,
    geoJSON: false,
    admin0_name: null,
    admin1_name: null,
    admin2_name: null,
    admin3_name: null,
    admin4_name: null,
    admin5_name: null,
    population_formatted: "7,600,000,000",
    full_name: "World",
}
```

Sample of the children json
```
{
    grid_id: 1,
    name: "World",
    level: -3,
    level_name: "world",
    country_code: null,
    admin0_code: null,
    parent_id: null,
    admin0_grid_id: null,
    admin1_grid_id: null,
    admin2_grid_id: null,
    admin3_grid_id: null,
    admin4_grid_id: null,
    admin5_grid_id: null,
    longitude: 0,
    latitude: 0,
    north_latitude: null,
    south_latitude: null,
    east_longitude: null,
    west_longitude: null,
    population: 7600000000,
    geonames_ref: "6295630",
    wikidata_ref: null,
    geoJSON: false,
    admin0_name: null,
    admin1_name: null,
    admin2_name: null,
    admin3_name: null,
    admin4_name: null,
    admin5_name: null,
    population_formatted: "7,600,000,000",
    full_name: "World",
    children_total: 255,
    children: {
        100000000: {
            grid_id: 100000000,
            name: "Aruba",
            level: 0,
            level_name: "admin0",
            country_code: "AW",
            admin0_code: "ABW",
            parent_id: 1,
            admin0_grid_id: 100000000,
            admin1_grid_id: null,
            admin2_grid_id: null,
            admin3_grid_id: null,
            admin4_grid_id: null,
            admin5_grid_id: null,
            longitude: -69.9703,
            latitude: 12.5093,
            north_latitude: 12.624,
            south_latitude: 12.4124,
            east_longitude: -69.8654,
            west_longitude: -70.0635,
            population: 71566,
            geonames_ref: "3577279",
            wikidata_ref: null,
            geoJSON: true,
            admin0_name: "Aruba",
            admin1_name: null,
            admin2_name: null,
            admin3_name: null,
            admin4_name: null,
            admin5_name: null,
            population_formatted: "71,566",
            full_name: "Aruba",
        },
        100000001: {
            grid_id: 100000001,
            name: "Afghanistan",
            level: 0,
            level_name: "admin0",
            country_code: "AF",
            admin0_code: "AFG",
            parent_id: 1,
            admin0_grid_id: 100000001,
            admin1_grid_id: null,
            admin2_grid_id: null,
            admin3_grid_id: null,
            admin4_grid_id: null,
            admin5_grid_id: null,
            longitude: 66.0296,
            latitude: 33.8284,
            north_latitude: 38.4909,
            south_latitude: 29.3616,
            east_longitude: 74.8941,
            west_longitude: 60.5049,
            population: 29121286,
            geonames_ref: "1149361",
            wikidata_ref: null,
            geoJSON: true,
            admin0_name: "Afghanistan",
            admin1_name: null,
            admin2_name: null,
            admin3_name: null,
            admin4_name: null,
            admin5_name: null,
            population_formatted: "29,121,286",
            full_name: "Afghanistan",
        },...
```




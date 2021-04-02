# Create JP CSV

1. Download new JP data in csv and import all columns to new mysql table called 'jp_people_groups'. `https://joshuaproject.net/resources/datasets` "All people groups by country"

1. Setup table 
    ```
    ALTER TABLE `jp_people_groups`ADD `id` INT(11)  UNSIGNED  NOT NULL  AUTO_INCREMENT  PRIMARY KEY;
    ALTER TABLE `jp_people_groups` CHANGE `Longitude` `longitude` FLOAT NULL  DEFAULT NULL;
    ALTER TABLE `jp_people_groups` CHANGE `Latitude` `latitude` FLOAT NULL  DEFAULT NULL;
    ALTER TABLE `jp_people_groups` ADD `level` VARCHAR(255)  NULL  DEFAULT NULL;
    ALTER TABLE `jp_people_groups` ADD `label` VARCHAR(255)  NULL  DEFAULT NULL;
    ALTER TABLE `jp_people_groups` ADD `grid_id` BIGINT(22)  NULL  DEFAULT NULL;
    ALTER TABLE `jp_people_groups` ADD `parent_id` BIGINT(22)  NULL  DEFAULT NULL;
    ALTER TABLE `jp_people_groups` ADD `admin0_grid_id` BIGINT(22)  NULL  DEFAULT NULL;
    ALTER TABLE `jp_people_groups` ADD `admin1_grid_id` BIGINT(22)  NULL  DEFAULT NULL;
    ALTER TABLE `jp_people_groups` ADD `admin2_grid_id` BIGINT(22)  NULL  DEFAULT NULL;
    ```
   
   This table statement adds level, label, grid_id, parent_id, admin0_grid_id, admin1_grid_id, admin2_grid_id.
   And it adds a primary key to the list
  
1. Run `add-geocode-to-jp-table.php` script.

1. Remove columns
```
ALTER TABLE `jp_unreached_people_groups` DROP `PeopNameInCountry`;
ALTER TABLE `jp_unreached_people_groups` DROP `JPScale`;
ALTER TABLE `jp_unreached_people_groups` DROP `LeastReached`;
ALTER TABLE `jp_unreached_people_groups` DROP `ROL3`;
ALTER TABLE `jp_unreached_people_groups` DROP `PrimaryLanguageName`;
ALTER TABLE `jp_unreached_people_groups` DROP `BibleStatus`;
ALTER TABLE `jp_unreached_people_groups` DROP `RLG3`;
ALTER TABLE `jp_unreached_people_groups` DROP `PrimaryReligion`;
ALTER TABLE `jp_unreached_people_groups` DROP `PercentAdherents`;
ALTER TABLE `jp_unreached_people_groups` DROP `PercentEvangelical`;
ALTER TABLE `jp_unreached_people_groups` DROP `PeopleID1`;
ALTER TABLE `jp_unreached_people_groups` DROP `ROP1`;
ALTER TABLE `jp_unreached_people_groups` DROP `AffinityBloc`;
ALTER TABLE `jp_unreached_people_groups` DROP `PeopleID2`;
ALTER TABLE `jp_unreached_people_groups` DROP `ROP2`;
ALTER TABLE `jp_unreached_people_groups` DROP `PeopleCluster`;
ALTER TABLE `jp_unreached_people_groups` DROP `CountOfCountries`;
ALTER TABLE `jp_unreached_people_groups` DROP `RegionCode`;
ALTER TABLE `jp_unreached_people_groups` DROP `RegionName`;
ALTER TABLE `jp_unreached_people_groups` DROP `ROG2`;
ALTER TABLE `jp_unreached_people_groups` DROP `Continent`;
ALTER TABLE `jp_unreached_people_groups` DROP `10_40Window`;
ALTER TABLE `jp_unreached_people_groups` DROP `IndigenousCode`;
ALTER TABLE `jp_unreached_people_groups` DROP `WorkersNeeded`;
ALTER TABLE `jp_unreached_people_groups` DROP `Frontier`;
```




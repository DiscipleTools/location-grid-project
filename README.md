
# LOCATION GRID PROJECT

The Location Grid Project hopes to offer a cross-referenced grid for reporting on movement progress across the planet, 
while at the same time is location sensitive for activity in dangerous or anti-christian locations and compliance with 
increasing privacy laws like GDPR.

The project serves to support the vision of consistently tracking church planting movement efforts globally in a way
 that allows networks and different organizations to share location sensitive reports to visualize and respond to
 areas of disciple making movement and areas where there is no disciple making movement.

The project offers a global grid of unique location ids for countries, states, and counties, 
longitude/latitude, populations for those administrative areas, and the supporting geojson polygon files for 
lightweight application display. 

The polygon source data began with GADM public polygon sets and then the geojson files were generated and keyed to the grid system. 

The administrative boundary information (north, south, east, west) has been generated from currently available 
polygons by the Location Grid Project. 

Longitude and latitude centerpoint generated from the polygon data.

The hierarchy data in columns parent_id, admin0_grid_id, admin1_grid_id, admin2_grid_id, admin3_grid_id, admin4_grid_id, admin5_grid_id has been
generated from the Geonames Hierarchy table by the Location Grid Project.

## DATABASES

### The location_grid table 
The location grid table is a MYSQL table with 380,000 records for the administrative levels of the world. Based
on the GADM polygon set. This table adds hierarchy, geonames cross-reference, bounding boxes, longitude and latitude for centerpoint,
country_codes, and level descriptions.

[Download Location Grid SQL Table](https://storage.googleapis.com/location-grid-source-data/location_grid.sql.zip)
(14.77 MB)



### The location_grid_geometry table 
The location_grid_geometry table has all polygons for each of the 380,000 administrative units cross-referenced
to the location_grid table by grid_id.

[Download Location_Grid_Geometry](https://storage.googleapis.com/location-grid-source-data/location_grid_geometry.sql.zip)
 (1.81 GB)
 
 
### The location-grid-mirror
The location grid mirror is a folder containing three folders: low, high, collection. The low and high
folders contain .geojson files named by grid_id containing the polygon or
multipolygon for the administrative unit and properties containing name, level names, and centerpoints. Those files
in the low folder are compressed for web delivery, the high folder contains full resolution polygons. The 
collection folder contains .geojson files named by grid_id that contain a collection of the next
level administrative polygons. i.e. the {state of colorado}.geojson file contains all the counties for Colorado
in the single .geojson file. Each sub-administrative unit in the collection has properties including name, lng/lat centerpoint,
and admin level data.

[Download Location Grid Mirror](https://storage.googleapis.com/location-grid-source-data/location-grid-mirror.zip)
(4.06 GB)

## Grid_ID Ranges

| Range | Description |
| ------ | ----- |
|100,000,000 - 200,000,000 | One hundred million to two hundred million is the id range for the location grid global admin levels |
|1,000,000,000 + | 1 billion and higher is reserved for custom locations |

## TABLE STRUCTURES

location_grid table:
```apacheconfig
CREATE TABLE `location_grid` (
  `grid_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL DEFAULT '',
  `level` float DEFAULT NULL,
  `level_name` varchar(7) DEFAULT NULL,
  `country_code` varchar(10) DEFAULT NULL,
  `admin0_code` varchar(10) DEFAULT NULL,
  `admin1_code` varchar(20) DEFAULT NULL,
  `admin2_code` varchar(20) DEFAULT NULL,
  `admin3_code` varchar(20) DEFAULT NULL,
  `admin4_code` varchar(20) DEFAULT NULL,
  `admin5_code` varchar(20) DEFAULT NULL,
  `parent_id` bigint(20) DEFAULT NULL,
  `admin0_grid_id` bigint(20) DEFAULT NULL,
  `admin1_grid_id` bigint(20) DEFAULT NULL,
  `admin2_grid_id` bigint(20) DEFAULT NULL,
  `admin3_grid_id` bigint(20) DEFAULT NULL,
  `admin4_grid_id` bigint(20) DEFAULT NULL,
  `admin5_grid_id` bigint(20) DEFAULT NULL,
  `longitude` float DEFAULT NULL,
  `latitude` float DEFAULT NULL,
  `north_latitude` float DEFAULT NULL,
  `south_latitude` float DEFAULT NULL,
  `west_longitude` float DEFAULT NULL,
  `east_longitude` float DEFAULT NULL,
  `population` bigint(20) NOT NULL DEFAULT '0',
  `modification_date` date DEFAULT NULL,
  `geonames_ref` bigint(20) DEFAULT NULL,
  `wikidata_ref` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`grid_id`),
  KEY `level` (`level`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `admin0_code` (`admin0_code`),
  KEY `admin1_code` (`admin1_code`),
  KEY `admin2_code` (`admin2_code`),
  KEY `admin3_code` (`admin3_code`),
  KEY `admin4_code` (`admin4_code`),
  KEY `country_code` (`country_code`),
  KEY `north_latitude` (`north_latitude`),
  KEY `south_latitude` (`south_latitude`),
  KEY `parent_id` (`parent_id`),
  KEY `west_longitude` (`west_longitude`),
  KEY `east_longitude` (`east_longitude`),
  KEY `admin5_code` (`admin5_code`),
  KEY `admin0_grid_id` (`admin0_grid_id`),
  KEY `admin1_grid_id` (`admin1_grid_id`),
  KEY `admin2_grid_id` (`admin2_grid_id`),
  KEY `admin3_grid_id` (`admin3_grid_id`),
  KEY `admin4_grid_id` (`admin4_grid_id`),
  KEY `admin5_grid_id` (`admin5_grid_id`),
  KEY `level_name` (`level_name`),
  FULLTEXT KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=100386738 DEFAULT CHARSET=utf8;
```

location_grid_geometry:
```apacheconfig
CREATE TABLE `location_grid_geometry` (
  `grid_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `geoJSON` longtext,
  PRIMARY KEY (`grid_id`)
) ENGINE=InnoDB AUTO_INCREMENT=100386738 DEFAULT CHARSET=utf8;
```


| Name | Admin0 Code | Level Name | Number of Records |
| ---- | ---- | ---- | ---- |
|||world|1|
|Aruba|ABW|admin0|1|
|Afghanistan|AFG|admin0|1|
|Afghanistan|AFG|admin1|34|
|Afghanistan|AFG|admin2|328|
|Angola|AGO|admin0|1|
|Angola|AGO|admin1|18|
|Angola|AGO|admin2|163|
|Angola|AGO|admin3|527|
|Anguilla|AIA|admin0|1|
|Åland|ALA|admin0|1|
|Åland|ALA|admin1|16|
|Albania|ALB|admin0|1|
|Albania|ALB|admin1|12|
|Albania|ALB|admin2|37|
|Albania|ALB|admin3|378|
|Andorra|AND|admin0|1|
|Andorra|AND|admin1|7|
|United Arab Emirates|ARE|admin0|1|
|United Arab Emirates|ARE|admin1|7|
|United Arab Emirates|ARE|admin2|195|
|United Arab Emirates|ARE|admin3|530|
|Argentina|ARG|admin0|1|
|Argentina|ARG|admin1|24|
|Argentina|ARG|admin2|503|
|Armenia|ARM|admin0|1|
|Armenia|ARM|admin1|11|
|American Samoa|ASM|admin0|1|
|American Samoa|ASM|admin1|4|
|American Samoa|ASM|admin2|16|
|American Samoa|ASM|admin3|74|
|Antarctica|ATA|admin0|1|
|French Southern Territories|ATF|admin0|1|
|French Southern Territories|ATF|admin1|4|
|Antigua and Barbuda|ATG|admin0|1|
|Antigua and Barbuda|ATG|admin1|8|
|Australia|AUS|admin0|1|
|Australia|AUS|admin1|11|
|Australia|AUS|admin2|569|
|Austria|AUT|admin0|1|
|Austria|AUT|admin1|9|
|Austria|AUT|admin2|96|
|Austria|AUT|admin3|2126|
|Azerbaijan|AZE|admin0|1|
|Azerbaijan|AZE|admin1|10|
|Azerbaijan|AZE|admin2|79|
|Burundi|BDI|admin0|1|
|Burundi|BDI|admin1|17|
|Burundi|BDI|admin2|133|
|Burundi|BDI|admin3|2644|
|Burundi|BDI|admin4|8756|
|Belgium|BEL|admin0|1|
|Belgium|BEL|admin1|3|
|Belgium|BEL|admin2|11|
|Belgium|BEL|admin3|43|
|Belgium|BEL|admin4|589|
|Benin|BEN|admin0|1|
|Benin|BEN|admin1|12|
|Benin|BEN|admin2|76|
|Bonaire, Sint Eustatius and Saba|BES|admin0|1|
|Bonaire, Sint Eustatius and Saba|BES|admin1|3|
|Burkina Faso|BFA|admin0|1|
|Burkina Faso|BFA|admin1|13|
|Burkina Faso|BFA|admin2|45|
|Burkina Faso|BFA|admin3|351|
|Bangladesh|BGD|admin0|1|
|Bangladesh|BGD|admin1|7|
|Bangladesh|BGD|admin2|64|
|Bangladesh|BGD|admin3|545|
|Bangladesh|BGD|admin4|5158|
|Bulgaria|BGR|admin0|1|
|Bulgaria|BGR|admin1|28|
|Bulgaria|BGR|admin2|263|
|Bahrain|BHR|admin0|1|
|Bahrain|BHR|admin1|5|
|Bahamas|BHS|admin0|1|
|Bahamas|BHS|admin1|32|
|Bosnia and Herzegovina|BIH|admin0|1|
|Bosnia and Herzegovina|BIH|admin1|3|
|Bosnia and Herzegovina|BIH|admin2|18|
|Bosnia and Herzegovina|BIH|admin3|141|
|Saint-Barthélemy|BLM|admin0|1|
|Belarus|BLR|admin0|1|
|Belarus|BLR|admin1|6|
|Belarus|BLR|admin2|118|
|Belize|BLZ|admin0|1|
|Belize|BLZ|admin1|6|
|Bermuda|BMU|admin0|1|
|Bermuda|BMU|admin1|11|
|Bolivia|BOL|admin0|1|
|Bolivia|BOL|admin1|9|
|Bolivia|BOL|admin2|95|
|Bolivia|BOL|admin3|319|
|Brazil|BRA|admin0|1|
|Brazil|BRA|admin1|27|
|Brazil|BRA|admin2|5504|
|Brazil|BRA|admin3|10195|
|Barbados|BRB|admin0|1|
|Barbados|BRB|admin1|11|
|Brunei|BRN|admin0|1|
|Brunei|BRN|admin1|4|
|Brunei|BRN|admin2|32|
|Bhutan|BTN|admin0|1|
|Bhutan|BTN|admin1|20|
|Bhutan|BTN|admin2|205|
|Bouvet Island|BVT|admin0|1|
|Botswana|BWA|admin0|1|
|Botswana|BWA|admin1|16|
|Botswana|BWA|admin2|30|
|Central African Republic|CAF|admin0|1|
|Central African Republic|CAF|admin1|17|
|Central African Republic|CAF|admin2|51|
|Canada|CAN|admin0|1|
|Canada|CAN|admin1|13|
|Canada|CAN|admin2|293|
|Canada|CAN|admin3|5582|
|Cocos Islands|CCK|admin0|1|
|Switzerland|CHE|admin0|1|
|Switzerland|CHE|admin1|26|
|Switzerland|CHE|admin2|169|
|Switzerland|CHE|admin3|2781|
|Chile|CHL|admin0|1|
|Chile|CHL|admin1|16|
|Chile|CHL|admin2|54|
|Chile|CHL|admin3|302|
|China|CHN|admin0|1|
|China|CHN|admin1|31|
|China|CHN|admin2|344|
|China|CHN|admin3|2408|
|Côte d'Ivoire|CIV|admin0|1|
|Côte d'Ivoire|CIV|admin1|14|
|Côte d'Ivoire|CIV|admin2|33|
|Côte d'Ivoire|CIV|admin3|113|
|Côte d'Ivoire|CIV|admin4|191|
|Cameroon|CMR|admin0|1|
|Cameroon|CMR|admin1|10|
|Cameroon|CMR|admin2|58|
|Cameroon|CMR|admin3|360|
|Democratic Republic of the Congo|COD|admin0|1|
|Democratic Republic of the Congo|COD|admin1|26|
|Democratic Republic of the Congo|COD|admin2|240|
|Republic of Congo|COG|admin0|1|
|Republic of Congo|COG|admin1|12|
|Republic of Congo|COG|admin2|48|
|Cook Islands|COK|admin0|1|
|Colombia|COL|admin0|1|
|Colombia|COL|admin1|32|
|Colombia|COL|admin2|1065|
|Comoros|COM|admin0|1|
|Comoros|COM|admin1|3|
|Cape Verde|CPV|admin0|1|
|Cape Verde|CPV|admin1|22|
|Costa Rica|CRI|admin0|1|
|Costa Rica|CRI|admin1|7|
|Costa Rica|CRI|admin2|81|
|Cuba|CUB|admin0|1|
|Cuba|CUB|admin1|16|
|Cuba|CUB|admin2|168|
|Curaçao|CUW|admin0|1|
|Christmas Island|CXR|admin0|1|
|Cayman Islands|CYM|admin0|1|
|Cayman Islands|CYM|admin1|7|
|Cyprus|CYP|admin0|1|
|Cyprus|CYP|admin1|5|
|Czech Republic|CZE|admin0|1|
|Czech Republic|CZE|admin1|14|
|Czech Republic|CZE|admin2|98|
|Germany|DEU|admin0|1|
|Germany|DEU|admin1|16|
|Germany|DEU|admin2|403|
|Germany|DEU|admin3|4680|
|Germany|DEU|admin4|11302|
|Djibouti|DJI|admin0|1|
|Djibouti|DJI|admin1|5|
|Djibouti|DJI|admin2|11|
|Dominica|DMA|admin0|1|
|Dominica|DMA|admin1|10|
|Denmark|DNK|admin0|1|
|Denmark|DNK|admin1|5|
|Denmark|DNK|admin2|99|
|Dominican Republic|DOM|admin0|1|
|Dominican Republic|DOM|admin1|32|
|Dominican Republic|DOM|admin2|155|
|Algeria|DZA|admin0|1|
|Algeria|DZA|admin1|48|
|Algeria|DZA|admin2|1504|
|Ecuador|ECU|admin0|1|
|Ecuador|ECU|admin1|24|
|Ecuador|ECU|admin2|223|
|Ecuador|ECU|admin3|1039|
|Egypt|EGY|admin0|1|
|Egypt|EGY|admin1|27|
|Egypt|EGY|admin2|343|
|Eritrea|ERI|admin0|1|
|Eritrea|ERI|admin1|6|
|Eritrea|ERI|admin2|50|
|Western Sahara|ESH|admin0|1|
|Western Sahara|ESH|admin1|4|
|Spain|ESP|admin0|1|
|Spain|ESP|admin1|18|
|Spain|ESP|admin2|52|
|Spain|ESP|admin3|369|
|Spain|ESP|admin4|8302|
|Estonia|EST|admin0|1|
|Estonia|EST|admin1|16|
|Estonia|EST|admin2|223|
|Estonia|EST|admin3|4684|
|Ethiopia|ETH|admin0|1|
|Ethiopia|ETH|admin1|11|
|Ethiopia|ETH|admin2|79|
|Ethiopia|ETH|admin3|690|
|Finland|FIN|admin0|1|
|Finland|FIN|admin1|5|
|Finland|FIN|admin2|21|
|Finland|FIN|admin3|80|
|Finland|FIN|admin4|437|
|Fiji|FJI|admin0|1|
|Fiji|FJI|admin1|5|
|Fiji|FJI|admin2|15|
|Falkland Islands|FLK|admin0|1|
|France|FRA|admin0|1|
|France|FRA|admin1|13|
|France|FRA|admin2|96|
|France|FRA|admin3|350|
|France|FRA|admin4|3728|
|France|FRA|admin5|36612|
|Faroe Islands|FRO|admin0|1|
|Faroe Islands|FRO|admin1|6|
|Faroe Islands|FRO|admin2|30|
|Micronesia|FSM|admin0|1|
|Micronesia|FSM|admin1|4|
|Gabon|GAB|admin0|1|
|Gabon|GAB|admin1|9|
|Gabon|GAB|admin2|37|
|United Kingdom|GBR|admin0|1|
|United Kingdom|GBR|admin1|4|
|United Kingdom|GBR|admin2|183|
|United Kingdom|GBR|admin3|406|
|Georgia|GEO|admin0|1|
|Georgia|GEO|admin1|12|
|Georgia|GEO|admin2|69|
|Guernsey|GGY|admin0|1|
|Guernsey|GGY|admin1|15|
|Ghana|GHA|admin0|1|
|Ghana|GHA|admin1|10|
|Ghana|GHA|admin2|137|
|Gibraltar|GIB|admin0|1|
|Guinea|GIN|admin0|1|
|Guinea|GIN|admin1|8|
|Guinea|GIN|admin2|34|
|Guinea|GIN|admin3|336|
|Guadeloupe|GLP|admin0|1|
|Guadeloupe|GLP|admin1|2|
|Guadeloupe|GLP|admin2|32|
|Gambia|GMB|admin0|1|
|Gambia|GMB|admin1|6|
|Gambia|GMB|admin2|37|
|Guinea-Bissau|GNB|admin0|1|
|Guinea-Bissau|GNB|admin1|9|
|Guinea-Bissau|GNB|admin2|37|
|Equatorial Guinea|GNQ|admin0|1|
|Equatorial Guinea|GNQ|admin1|7|
|Equatorial Guinea|GNQ|admin2|32|
|Greece|GRC|admin0|1|
|Greece|GRC|admin1|8|
|Greece|GRC|admin2|14|
|Greece|GRC|admin3|326|
|Grenada|GRD|admin0|1|
|Grenada|GRD|admin1|7|
|Greenland|GRL|admin0|1|
|Greenland|GRL|admin1|5|
|Guatemala|GTM|admin0|1|
|Guatemala|GTM|admin1|22|
|Guatemala|GTM|admin2|354|
|French Guiana|GUF|admin0|1|
|French Guiana|GUF|admin1|2|
|French Guiana|GUF|admin2|21|
|Guam|GUM|admin0|1|
|Guam|GUM|admin1|19|
|Guyana|GUY|admin0|1|
|Guyana|GUY|admin1|10|
|Guyana|GUY|admin2|116|
|Hong Kong|HKG|admin0|1|
|Hong Kong|HKG|admin1|18|
|Heard Island and McDonald Islands|HMD|admin0|1|
|Honduras|HND|admin0|1|
|Honduras|HND|admin1|18|
|Honduras|HND|admin2|298|
|Croatia|HRV|admin0|1|
|Croatia|HRV|admin1|21|
|Croatia|HRV|admin2|560|
|Haiti|HTI|admin0|1|
|Haiti|HTI|admin1|10|
|Haiti|HTI|admin2|41|
|Haiti|HTI|admin3|134|
|Haiti|HTI|admin4|542|
|Hungary|HUN|admin0|1|
|Hungary|HUN|admin1|20|
|Hungary|HUN|admin2|168|
|Indonesia|IDN|admin0|1|
|Indonesia|IDN|admin1|33|
|Indonesia|IDN|admin2|502|
|Indonesia|IDN|admin3|6696|
|Indonesia|IDN|admin4|77474|
|Isle of Man|IMN|admin0|1|
|Isle of Man|IMN|admin1|6|
|Isle of Man|IMN|admin2|24|
|India|IND|admin0|1|
|India|IND|admin1|36|
|India|IND|admin2|666|
|India|IND|admin3|2340|
|British Indian Ocean Territory|IOT|admin0|1|
|Ireland|IRL|admin0|1|
|Ireland|IRL|admin1|26|
|Iran|IRN|admin0|1|
|Iran|IRN|admin1|31|
|Iran|IRN|admin2|268|
|Iraq|IRQ|admin0|1|
|Iraq|IRQ|admin1|18|
|Iraq|IRQ|admin2|102|
|Iceland|ISL|admin0|1|
|Iceland|ISL|admin1|8|
|Iceland|ISL|admin2|119|
|Israel|ISR|admin0|1|
|Israel|ISR|admin1|7|
|Italy|ITA|admin0|1|
|Italy|ITA|admin1|20|
|Italy|ITA|admin2|110|
|Italy|ITA|admin3|8100|
|Jamaica|JAM|admin0|1|
|Jamaica|JAM|admin1|14|
|Jersey|JEY|admin0|1|
|Jersey|JEY|admin1|12|
|Jordan|JOR|admin0|1|
|Jordan|JOR|admin1|12|
|Jordan|JOR|admin2|52|
|Japan|JPN|admin0|1|
|Japan|JPN|admin1|47|
|Japan|JPN|admin2|1811|
|Kazakhstan|KAZ|admin0|1|
|Kazakhstan|KAZ|admin1|14|
|Kazakhstan|KAZ|admin2|174|
|Kenya|KEN|admin0|1|
|Kenya|KEN|admin1|47|
|Kenya|KEN|admin2|301|
|Kenya|KEN|admin3|1446|
|Kyrgyzstan|KGZ|admin0|1|
|Kyrgyzstan|KGZ|admin1|9|
|Kyrgyzstan|KGZ|admin2|44|
|Cambodia|KHM|admin0|1|
|Cambodia|KHM|admin1|25|
|Cambodia|KHM|admin2|178|
|Cambodia|KHM|admin3|1576|
|Cambodia|KHM|admin4|1580|
|Kiribati|KIR|admin0|1|
|Saint Kitts and Nevis|KNA|admin0|1|
|Saint Kitts and Nevis|KNA|admin1|14|
|South Korea|KOR|admin0|1|
|South Korea|KOR|admin1|17|
|South Korea|KOR|admin2|229|
|Kuwait|KWT|admin0|1|
|Kuwait|KWT|admin1|6|
|Laos|LAO|admin0|1|
|Laos|LAO|admin1|18|
|Laos|LAO|admin2|142|
|Lebanon|LBN|admin0|1|
|Lebanon|LBN|admin1|8|
|Lebanon|LBN|admin2|30|
|Lebanon|LBN|admin3|1568|
|Liberia|LBR|admin0|1|
|Liberia|LBR|admin1|15|
|Liberia|LBR|admin2|66|
|Liberia|LBR|admin3|305|
|Libya|LBY|admin0|1|
|Libya|LBY|admin1|22|
|Saint Lucia|LCA|admin0|1|
|Saint Lucia|LCA|admin1|10|
|Liechtenstein|LIE|admin0|1|
|Liechtenstein|LIE|admin1|11|
|Sri Lanka|LKA|admin0|1|
|Sri Lanka|LKA|admin1|25|
|Sri Lanka|LKA|admin2|323|
|Lesotho|LSO|admin0|1|
|Lesotho|LSO|admin1|10|
|Lithuania|LTU|admin0|1|
|Lithuania|LTU|admin1|10|
|Lithuania|LTU|admin2|48|
|Luxembourg|LUX|admin0|1|
|Luxembourg|LUX|admin1|3|
|Luxembourg|LUX|admin2|12|
|Luxembourg|LUX|admin3|116|
|Luxembourg|LUX|admin4|139|
|Latvia|LVA|admin0|1|
|Latvia|LVA|admin1|5|
|Latvia|LVA|admin2|26|
|Macao|MAC|admin0|1|
|Macao|MAC|admin1|2|
|Macao|MAC|admin2|8|
|Saint-Martin|MAF|admin0|1|
|Morocco|MAR|admin0|1|
|Morocco|MAR|admin1|15|
|Morocco|MAR|admin2|54|
|Morocco|MAR|admin3|399|
|Morocco|MAR|admin4|1515|
|Monaco|MCO|admin0|1|
|Moldova|MDA|admin0|1|
|Moldova|MDA|admin1|37|
|Madagascar|MDG|admin0|1|
|Madagascar|MDG|admin1|6|
|Madagascar|MDG|admin2|22|
|Madagascar|MDG|admin3|110|
|Madagascar|MDG|admin4|1433|
|Maldives|MDV|admin0|1|
|Mexico|MEX|admin0|1|
|Mexico|MEX|admin1|32|
|Mexico|MEX|admin2|1854|
|Marshall Islands|MHL|admin0|1|
|Macedonia|MKD|admin0|1|
|Macedonia|MKD|admin1|85|
|Mali|MLI|admin0|1|
|Mali|MLI|admin1|9|
|Mali|MLI|admin2|50|
|Mali|MLI|admin3|289|
|Mali|MLI|admin4|704|
|Malta|MLT|admin0|1|
|Malta|MLT|admin1|5|
|Malta|MLT|admin2|68|
|Myanmar|MMR|admin0|1|
|Myanmar|MMR|admin1|15|
|Myanmar|MMR|admin2|63|
|Myanmar|MMR|admin3|286|
|Montenegro|MNE|admin0|1|
|Montenegro|MNE|admin1|21|
|Mongolia|MNG|admin0|1|
|Mongolia|MNG|admin1|22|
|Mongolia|MNG|admin2|327|
|Northern Mariana Islands|MNP|admin0|1|
|Northern Mariana Islands|MNP|admin1|4|
|Mozambique|MOZ|admin0|1|
|Mozambique|MOZ|admin1|11|
|Mozambique|MOZ|admin2|129|
|Mozambique|MOZ|admin3|413|
|Mauritania|MRT|admin0|1|
|Mauritania|MRT|admin1|13|
|Mauritania|MRT|admin2|44|
|Montserrat|MSR|admin0|1|
|Montserrat|MSR|admin1|3|
|Martinique|MTQ|admin0|1|
|Martinique|MTQ|admin1|4|
|Martinique|MTQ|admin2|32|
|Mauritius|MUS|admin0|1|
|Mauritius|MUS|admin1|12|
|Malawi|MWI|admin0|1|
|Malawi|MWI|admin1|28|
|Malawi|MWI|admin2|256|
|Malawi|MWI|admin3|3126|
|Malaysia|MYS|admin0|1|
|Malaysia|MYS|admin1|16|
|Malaysia|MYS|admin2|144|
|Mayotte|MYT|admin0|1|
|Mayotte|MYT|admin1|17|
|Namibia|NAM|admin0|1|
|Namibia|NAM|admin1|13|
|Namibia|NAM|admin2|107|
|New Caledonia|NCL|admin0|1|
|New Caledonia|NCL|admin1|3|
|New Caledonia|NCL|admin2|35|
|Niger|NER|admin0|1|
|Niger|NER|admin1|8|
|Niger|NER|admin2|36|
|Niger|NER|admin3|131|
|Norfolk Island|NFK|admin0|1|
|Nigeria|NGA|admin0|1|
|Nigeria|NGA|admin1|37|
|Nigeria|NGA|admin2|775|
|Nicaragua|NIC|admin0|1|
|Nicaragua|NIC|admin1|18|
|Nicaragua|NIC|admin2|139|
|Niue|NIU|admin0|1|
|Netherlands|NLD|admin0|1|
|Netherlands|NLD|admin1|14|
|Netherlands|NLD|admin2|491|
|Norway|NOR|admin0|1|
|Norway|NOR|admin1|19|
|Norway|NOR|admin2|438|
|Nepal|NPL|admin0|1|
|Nepal|NPL|admin1|5|
|Nepal|NPL|admin2|14|
|Nepal|NPL|admin3|75|
|Nepal|NPL|admin4|3983|
|Nauru|NRU|admin0|1|
|Nauru|NRU|admin1|14|
|New Zealand|NZL|admin0|1|
|New Zealand|NZL|admin1|19|
|New Zealand|NZL|admin2|75|
|Oman|OMN|admin0|1|
|Oman|OMN|admin1|11|
|Oman|OMN|admin2|49|
|Pakistan|PAK|admin0|1|
|Pakistan|PAK|admin1|8|
|Pakistan|PAK|admin2|32|
|Pakistan|PAK|admin3|141|
|Panama|PAN|admin0|1|
|Panama|PAN|admin1|13|
|Panama|PAN|admin2|79|
|Panama|PAN|admin3|598|
|Pitcairn Islands|PCN|admin0|1|
|Peru|PER|admin0|1|
|Peru|PER|admin1|26|
|Peru|PER|admin2|195|
|Peru|PER|admin3|1815|
|Philippines|PHL|admin0|1|
|Philippines|PHL|admin1|81|
|Philippines|PHL|admin2|1647|
|Philippines|PHL|admin3|41948|
|Palau|PLW|admin0|1|
|Palau|PLW|admin1|16|
|Papua New Guinea|PNG|admin0|1|
|Papua New Guinea|PNG|admin1|22|
|Papua New Guinea|PNG|admin2|87|
|Poland|POL|admin0|1|
|Poland|POL|admin1|16|
|Poland|POL|admin2|380|
|Poland|POL|admin3|2479|
|Puerto Rico|PRI|admin0|1|
|Puerto Rico|PRI|admin1|78|
|North Korea|PRK|admin0|1|
|North Korea|PRK|admin1|14|
|North Korea|PRK|admin2|186|
|Portugal|PRT|admin0|1|
|Portugal|PRT|admin1|20|
|Portugal|PRT|admin2|308|
|Portugal|PRT|admin3|4260|
|Paraguay|PRY|admin0|1|
|Paraguay|PRY|admin1|18|
|Paraguay|PRY|admin2|218|
|Palestina|PSE|admin0|1|
|Palestina|PSE|admin1|2|
|Palestina|PSE|admin2|16|
|French Polynesia|PYF|admin0|1|
|French Polynesia|PYF|admin1|5|
|Qatar|QAT|admin0|1|
|Qatar|QAT|admin1|7|
|Reunion|REU|admin0|1|
|Reunion|REU|admin1|4|
|Reunion|REU|admin2|24|
|Romania|ROU|admin0|1|
|Romania|ROU|admin1|42|
|Romania|ROU|admin2|2939|
|Russia|RUS|admin0|1|
|Russia|RUS|admin1|83|
|Russia|RUS|admin2|2445|
|Russia|RUS|admin3|2562|
|Rwanda|RWA|admin0|1|
|Rwanda|RWA|admin1|5|
|Rwanda|RWA|admin2|30|
|Rwanda|RWA|admin3|422|
|Rwanda|RWA|admin4|2169|
|Rwanda|RWA|admin5|14815|
|Saudi Arabia|SAU|admin0|1|
|Saudi Arabia|SAU|admin1|13|
|Sudan|SDN|admin0|1|
|Sudan|SDN|admin1|18|
|Sudan|SDN|admin2|80|
|Sudan|SDN|admin3|237|
|Senegal|SEN|admin0|1|
|Senegal|SEN|admin1|14|
|Senegal|SEN|admin2|45|
|Senegal|SEN|admin3|123|
|Senegal|SEN|admin4|433|
|Singapore|SGP|admin0|1|
|Singapore|SGP|admin1|5|
|South Georgia and the South Sandwich Islands|SGS|admin0|1|
|Saint Helena|SHN|admin0|1|
|Saint Helena|SHN|admin1|3|
|Saint Helena|SHN|admin2|10|
|Svalbard and Jan Mayen|SJM|admin0|1|
|Svalbard and Jan Mayen|SJM|admin1|2|
|Solomon Islands|SLB|admin0|1|
|Solomon Islands|SLB|admin1|10|
|Solomon Islands|SLB|admin2|183|
|Sierra Leone|SLE|admin0|1|
|Sierra Leone|SLE|admin1|4|
|Sierra Leone|SLE|admin2|14|
|Sierra Leone|SLE|admin3|153|
|El Salvador|SLV|admin0|1|
|El Salvador|SLV|admin1|14|
|El Salvador|SLV|admin2|266|
|San Marino|SMR|admin0|1|
|San Marino|SMR|admin1|9|
|Somalia|SOM|admin0|1|
|Somalia|SOM|admin1|18|
|Somalia|SOM|admin2|74|
|Saint Pierre and Miquelon|SPM|admin0|1|
|Saint Pierre and Miquelon|SPM|admin1|2|
|Serbia|SRB|admin0|1|
|Serbia|SRB|admin1|25|
|Serbia|SRB|admin2|161|
|South Sudan|SSD|admin0|1|
|South Sudan|SSD|admin1|10|
|South Sudan|SSD|admin2|45|
|South Sudan|SSD|admin3|49|
|São Tomé and Príncipe|STP|admin0|1|
|São Tomé and Príncipe|STP|admin1|2|
|São Tomé and Príncipe|STP|admin2|7|
|Suriname|SUR|admin0|1|
|Suriname|SUR|admin1|10|
|Suriname|SUR|admin2|62|
|Slovakia|SVK|admin0|1|
|Slovakia|SVK|admin1|8|
|Slovakia|SVK|admin2|79|
|Slovenia|SVN|admin0|1|
|Slovenia|SVN|admin1|12|
|Slovenia|SVN|admin2|192|
|Sweden|SWE|admin0|1|
|Sweden|SWE|admin1|21|
|Sweden|SWE|admin2|290|
|Swaziland|SWZ|admin0|1|
|Swaziland|SWZ|admin1|4|
|Swaziland|SWZ|admin2|55|
|Sint Maarten|SXM|admin0|1|
|Seychelles|SYC|admin0|1|
|Seychelles|SYC|admin1|26|
|Syria|SYR|admin0|1|
|Syria|SYR|admin1|14|
|Syria|SYR|admin2|60|
|Turks and Caicos Islands|TCA|admin0|1|
|Turks and Caicos Islands|TCA|admin1|6|
|Chad|TCD|admin0|1|
|Chad|TCD|admin1|23|
|Chad|TCD|admin2|55|
|Chad|TCD|admin3|348|
|Togo|TGO|admin0|1|
|Togo|TGO|admin1|5|
|Togo|TGO|admin2|21|
|Thailand|THA|admin0|1|
|Thailand|THA|admin1|77|
|Thailand|THA|admin2|928|
|Thailand|THA|admin3|5926|
|Tajikistan|TJK|admin0|1|
|Tajikistan|TJK|admin1|5|
|Tajikistan|TJK|admin2|59|
|Tajikistan|TJK|admin3|360|
|Tokelau|TKL|admin0|1|
|Tokelau|TKL|admin1|3|
|Turkmenistan|TKM|admin0|1|
|Turkmenistan|TKM|admin1|6|
|Timor-Leste|TLS|admin0|1|
|Timor-Leste|TLS|admin1|13|
|Timor-Leste|TLS|admin2|62|
|Timor-Leste|TLS|admin3|493|
|Tonga|TON|admin0|1|
|Tonga|TON|admin1|5|
|Trinidad and Tobago|TTO|admin0|1|
|Trinidad and Tobago|TTO|admin1|15|
|Tunisia|TUN|admin0|1|
|Tunisia|TUN|admin1|24|
|Tunisia|TUN|admin2|268|
|Turkey|TUR|admin0|1|
|Turkey|TUR|admin1|81|
|Turkey|TUR|admin2|928|
|Tuvalu|TUV|admin0|1|
|Tuvalu|TUV|admin1|9|
|Taiwan|TWN|admin0|1|
|Taiwan|TWN|admin1|7|
|Taiwan|TWN|admin2|22|
|Tanzania|TZA|admin0|1|
|Tanzania|TZA|admin1|30|
|Tanzania|TZA|admin2|183|
|Tanzania|TZA|admin3|3661|
|Uganda|UGA|admin0|1|
|Uganda|UGA|admin1|58|
|Uganda|UGA|admin2|166|
|Uganda|UGA|admin3|966|
|Uganda|UGA|admin4|5341|
|Ukraine|UKR|admin0|1|
|Ukraine|UKR|admin1|27|
|Ukraine|UKR|admin2|629|
|United States Minor Outlying Islands|UMI|admin0|1|
|United States Minor Outlying Islands|UMI|admin1|9|
|Uruguay|URY|admin0|1|
|Uruguay|URY|admin1|19|
|Uruguay|URY|admin2|204|
|United States|USA|admin0|1|
|United States|USA|admin1|51|
|United States|USA|admin2|3148|
|Uzbekistan|UZB|admin0|1|
|Uzbekistan|UZB|admin1|14|
|Uzbekistan|UZB|admin2|161|
|Vatican City|VAT|admin0|1|
|Saint Vincent and the Grenadines|VCT|admin0|1|
|Saint Vincent and the Grenadines|VCT|admin1|6|
|Venezuela|VEN|admin0|1|
|Venezuela|VEN|admin1|25|
|Venezuela|VEN|admin2|338|
|British Virgin Islands|VGB|admin0|1|
|British Virgin Islands|VGB|admin1|5|
|Virgin Islands, U.S.|VIR|admin0|1|
|Virgin Islands, U.S.|VIR|admin1|3|
|Virgin Islands, U.S.|VIR|admin2|20|
|Vietnam|VNM|admin0|1|
|Vietnam|VNM|admin1|63|
|Vietnam|VNM|admin2|710|
|Vietnam|VNM|admin3|11163|
|Vanuatu|VUT|admin0|1|
|Vanuatu|VUT|admin1|6|
|Vanuatu|VUT|admin2|63|
|Wallis and Futuna|WLF|admin0|1|
|Wallis and Futuna|WLF|admin1|3|
|Wallis and Futuna|WLF|admin2|5|
|Samoa|WSM|admin0|1|
|Samoa|WSM|admin1|11|
|Samoa|WSM|admin2|43|
|Akrotiri and Dhekelia|XAD|admin0|1|
|Akrotiri and Dhekelia|XAD|admin1|2|
|Caspian Sea|XCA|admin0|1|
|Clipperton Island|XCL|admin0|1|
|Kosovo|XKO|admin0|1|
|Kosovo|XKO|admin1|7|
|Kosovo|XKO|admin2|30|
|Northern Cyprus|XNC|admin0|1|
|Northern Cyprus|XNC|admin1|5|
|Paracel Islands|XPI|admin0|1|
|Spratly Islands|XSP|admin0|1|
|Yemen|YEM|admin0|1|
|Yemen|YEM|admin1|21|
|Yemen|YEM|admin2|333|
|South Africa|ZAF|admin0|1|
|South Africa|ZAF|admin1|9|
|South Africa|ZAF|admin2|52|
|South Africa|ZAF|admin3|234|
|South Africa|ZAF|admin4|4277|
|Zambia|ZMB|admin0|1|
|Zambia|ZMB|admin1|10|
|Zambia|ZMB|admin2|72|
|Zimbabwe|ZWE|admin0|1|
|Zimbabwe|ZWE|admin1|10|
|Zimbabwe|ZWE|admin2|60|



| Level Name | Number of Records |
| ---- | ---- | 
|admin0|256|
|admin1|3610|
|admin2|45962|
|admin3|147427|
|admin4|138053|
|admin5|51427|
|world|1|


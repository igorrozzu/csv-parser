CSV parser
==============================
Simple Symfony application with csv parser with possibility to parse, validate and write data to database.

App extension example
------------
To parse new data from file with, for example, cart data:

1. Create an entity `Cart` to describe the data and its validation rules
2. Put mapping between data and the entity fields into `csv-parser-mapping.yml` with parameter name `mapping.cart`
3. Create import service with name like `app.import.cart` by implementing interface `src/AppBundle/Service/ImportInterface.php`
4. Run command `app:parse-csv` with arguments `fileName='path_to_file_with_cart_data''` and `entityName='cart'`. 
   Option `testMode` let you to run test parsing without insert to database

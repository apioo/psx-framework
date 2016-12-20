
### 2.0.0 (2016-12-20)

* Remove soap and wsdl controller
* Update psx schema and data to 2.0

### 1.1.3 (2016-11-20)

* Use psx_dispatch configuration to provide a way to change the dispatch file

### 1.1.2 (2016-11-20)

* Request factory handle normal and index.php/ calls independent of the
  psx_dispatch configuration

### 1.1.1 (2016-11-01)

* Add invalidate resource method to api cached listing

### 1.1.0 (2016-10-30)

* Cache implementation use doctrine cache handler from factory
* Add cache and log config factory
* Switch to symfony dependency injection 3.0
* Allow symfony 3.0 components

### 1.0.9 (2016-10-05)

* Fix schema command constructor call

### 1.0.8 (2016-09-10)

* Test environment use improve memory sqlite database connection

### 1.0.7 (2016-09-10)

* Test environment handle psx_connection config

### 1.0.6 (2016-09-10)

* Added psx_connection config
* Config return null if value does not exist

### 1.0.5 (2016-07-10)

* ApiAbstract return only supported writers
* Adjusted schema tests

### 1.0.4 (2016-07-10)

* Add psx_supported_writer config option

### 1.0.3 (2016-07-07)

* Add CORS filter

### 1.0.2 (2016-06-09)

* Update test cases
* Ignore deprecated errors

### 1.0.1 (2016-05-21)

* Added sql generate and migrate command

### 1.0.0 (2016-05-08)

* Initial release

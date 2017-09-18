
### 2.2.1 (2017-09-18)

* Add documentation generation command
* Add OpenAPI to discovery controller

### 2.2.0 (2017-09-09)

* Use Throwable instead of Exception
* Add factory support for DI container
* Use server REQUEST_TIME_FLOAT
* Set PHP 7.0 as minimum version

### 2.1.5 (2017-07-30)

* Add OpenAPI controller

### 2.1.4 (2017-06-04)

* Add http body wrapper

### 2.1.3 (2017-05-04)

* Fix documentation schema reference
* Use class notation

### 2.1.2 (2017-05-04)

* Documentation controller use class notation

### 2.1.1 (2017-04-18)

* Revert buffered stream usage

### 2.1.0 (2017-04-15)

* Implemented PSR-11 container interface and removed symfony di dependency
* Added upload file from environment method
* Removed buffered stream

### 2.0.6 (2017-04-04)

* Object builder cache annotations in production mode

### 2.0.5 (2017-02-08)

* Use writer factory to get writer by format

### 2.0.4 (2016-12-25)

* Fix documentation test

### 2.0.3 (2016-12-24)

* Increased version
* Fixed code style

### 2.0.2 (2016-12-24)

* Increase version
* Fix code style

### 2.0.1 (2016-12-21)

* Remove wsdl from discovery controller

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

# Money Exchange Application in PHP 8.2 and Symfony 5.4 #

Author Maciej Dubiel <munich55555@gmail.com>

## Requirements

* PHP >=8.2.*
* XDebug 3.* for generating code coverage

## Installation

* After unpacking the zip file, run the following command to install packages `php composer.phar install`


## Usage

Execute `bin/console app:exchange-money <amount> <source_currency> <target_currency>` to run the application

Sample uses:
* bin/console app:exchange-money 100 GBP EUR
* bin/console app:exchange-money 100 EUR GBP

Task Use Cases are covered by `tests/Integration/Application/ExchangeServiceTest.php`

 ## Run Tests and generate Code Coverage
 
 * Execute `vendor/bin/phpunit`to run tests
 * Execute `XDEBUG_MODE=coverage vendor/bin/phpunit --coverage-html var/log/reports`to generate code coverage
 
Generated code coverage is already available and can be found under `var/logs/reports/index.html`
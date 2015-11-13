# Hosola-data-logger
===================

Hosola-data-logger is a small PHP script to fetch, parse and upload data from your Hosola PV inverter. Currently it supports upload to PVOutput and/or a MySQL database

----------


# Installation and Setup
----------

* Install php (sudo apt-get install php5-cli for example)
* Install composer (see https://getcomposer.org/download/)
* Git clone the source with `git clone https://github.com/Mattie112/hosola-data-logger.git`
* Copy the example.ini to config.ini and edit this file
* Checkout the "example.php" and "export_data.php" file to get you started!
* If you want to use MySQL don't forget to import the SQL file

----------


# Automatic upload to PVOut
----------
Simply create a cronjob (or use the Windows Task Scheduler) like:

`* * * * * php /home/username/hosola-data-logger/export_data.php`


----------


# Special thanks
----------
Special thanks to these repositories I consulted when developing this script:

* https://github.com/Woutrrr/Omnik-Data-Logger
* https://github.com/micromys/Omnik
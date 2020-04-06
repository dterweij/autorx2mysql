# autorx2mysql
sonde auto_rx to MySQL logger via UDP packets

* UDP to MySQL logger (Perl)
* Website interface to show (live) data and statistics (PHP 7.2+/MySQL 5.5+)


You can run the Perl script anywhere in your own network on any computer. Run it in a terminal or fork it to the background (dont forget the dev null). Run the MySQL server anywhere in your network. Run the website anywhere on your network.

My setup:
* System A - Server - CentOS 6: MySQL + Apache + PHP  -> autorx2mysql website interface
* System B - PC - Ubuntu: Auto_Rx station 1 + UDP to MySQL Perl script
* System C - Raspberry PI 2 - Raspbian: Auto_Rx station 2


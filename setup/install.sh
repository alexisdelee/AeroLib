#!/usr/bin/env bash

if [ $# != 1 ]
then
	echo "error: an expected parameter"
	exit
fi

APT="apt-get -y"

execPath=$(readlink -f $(dirname $0))

# extract and decompress the file containing all the sources
tar -zxf package.tar.gz

# install global mysql
$APT install mysql-server mysql-client

# create the parent folder
mkdir -p /var/www/aerodrome

Q1="CREATE USER 'staff'@'localhost' IDENTIFIED BY 'staff';"
Q2="CREATE DATABASE IF NOT EXISTS aerodrome;"
Q3="GRANT ALL PRIVILEGES ON aerodrome.* TO 'staff'@'localhost' WITH GRANT OPTION;"
Q4="CREATE USER 'staff'@'%' IDENTIFIED BY 'staff';"
Q5="GRANT ALL PRIVILEGES ON aerodrome.* TO 'staff'@'%' WITH GRANT OPTION;"

mysql -uroot -p -e "${Q1}${Q2}${Q3}${Q4}${Q5}";
mysql -ustaff -pstaff aerodrome < package/aerodrome.sql

# edit configuration file and restart mysql server
sed -i "s/^\(bind-address.*\)/# \1/g" /etc/mysql/my.cnf
/etc/init.d/mysql restart

if [ $1 = "global" ] || [ $1 = "weather" ]
then
	cd $execPath

	# install curl
	$APT install curl

	# import cron configuration
	crontab package/crontab.bak

	# transfer weather application
	cp -ar package/bin /var/www/aerodrome/
	
	# create log folder
	mkdir /var/www/aerodrome/bin/log

	# run the program for the first time
	cd /var/www/aerodrome/bin/
	chmod +x weather
	chmod +x daemonWeather.sh
	./daemonWeather.sh
fi

if [ $1 = "global" ] || [ $1 = "web" ]
then
	cd $execPath

	# install apache server
	$APT install apache2

	# site creation
	cp package/aen.conf /etc/apache2/sites-available/
	cd /etc/apache2/sites-available && a2ensite aen
	mkdir /var/log/apache2/aen
	echo "127.0.0.1 aen.fr" >> /etc/hosts
	/etc/init.d/apache2 restart

	# install php and restart apache
	$APT install php5-common libapache2-mod-php5 php5-cli
	/etc/init.d/apache2 restart

	# install PDO driver on mysql and restart
	$APT install php5-mysql
	/etc/init.d/apache2 restart

	# transfer web files
	cd $execPath
	cp -ar package/web/* /var/www/aerodrome/

	# configure smtp
	curl http://www.jetmore.org/john/code/swaks/files/swaks-20130209.0/swaks -o /var/www/aerodrome/bin/swaks
	chmod +x /var/www/aerodrome/bin/swaks
	$APT install perl
fi

if [ $1 = "global" ] || [ $1 = "export" ]
then
	# install and jdk
	$APT install default-jdk
fi

cd $execPath
rm -rf package

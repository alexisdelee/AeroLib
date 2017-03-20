#!/usr/bin/env bash

CITY="3019265"
# KEYAPI="d989ebb968daabe2af65b9577ef61eba"
CONF="weather.conf"

mapfile KEYAPI < /var/www/aerodrome/config/openweather_api.key
curl -s "http://api.openweathermap.org/data/2.5/weather?id=${CITY}&appid=${KEYAPI}&units=metric&lang=fr" 2>&1 | ./weather ${CONF}

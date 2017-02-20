#include <stdio.h>
#include <stdlib.h>
#include <string.h>

#include "sources/h/traitment.h"

int main(int argc, char **argv)
{
    char city[10] = "3019265";
    char appid[33] = "d989ebb968daabe2af65b9577ef61eba";
    char command[135] = "";
    char path[1035] = "";
    FILE *fcurl = NULL;

    sprintf(command, "curl -s \"http://api.openweathermap.org/data/2.5/weather?id=%s&appid=%s&units=metric\"", city, appid); // &lang=fr

    fcurl = popen(command, "r");
    if(fcurl == NULL){
        exit(-1);
    }

    fgets(path, sizeof(path) - 1, fcurl);
    if(!json_analyze(path)){
        pclose(fcurl);
        exit(-1);
    }

    pclose(fcurl);

    return 0;
}

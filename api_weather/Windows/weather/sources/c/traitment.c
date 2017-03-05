#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <time.h>
#include <limits.h>
#include <errno.h>

#include <windows.h>
#include <winsock.h>
#include <MYSQL/mysql.h>

#include "../../ext/jsmn/lib/jsmn.c"
#include "../h/traitment.h"

#define USER "root"
#define PASSWORD ""
#define DATABASE "aerodrome"

long select_db()
{
    long id;
    char *endptr = NULL;
    MYSQL *mysql;
    MYSQL_RES *result;
    MYSQL_ROW row;

    mysql = mysql_init(NULL);
    mysql_options(mysql, MYSQL_READ_DEFAULT_GROUP, "");

    if(!mysql_real_connect(mysql, "localhost", USER, PASSWORD, DATABASE, 0, NULL, 0)
       || mysql_query(mysql, "SELECT id FROM meteo ORDER BY id DESC LIMIT 1 ") // requête MYSQL : récupérer le dernier id
       || (result = mysql_store_result(mysql)) == NULL){
        return -1;
    }

    if((row = mysql_fetch_row(result)) == NULL){
        return -2;
    }

    id = strtol(row[0], &endptr, 10); // convertir en entier (long)

    if((errno == ERANGE && (id == LONG_MIN || id == LONG_MAX))
       || (errno != 0 && id == 0)
       || (endptr == row[0])){
        return -1;
    }

    mysql_free_result(result); // libérer la mémoire
    mysql_close(mysql); // et ferme la connection à MySQL

    return id;
}

int json_analyze(const char *json)
{
    int error;
    char keywords[10][12] = {"description", "temp", "pressure", "humidity", "temp_min", "temp_max", "visibility", "speed", "sunrise", "sunset"};
    jsmn_parser p;
    jsmntok_t tokens[128];

    jsmn_init(&p);
    error = jsmn_parse(&p, json, strlen(json), tokens, sizeof(tokens) / sizeof(tokens[0])); // parse le JSON
    if(error < 0){
        return 0;
    } else if(error < 1 || tokens[0].type != JSMN_OBJECT){
        return 0;
    }

    return json_detect(json, tokens, error, keywords);
}

int jsoneq(const char *json, jsmntok_t *tok, const char *keyword)
{
    if(tok->type == JSMN_STRING && (int)strlen(keyword) == tok->end - tok->start && strncmp(json + tok->start, keyword, tok->end - tok->start) == 0){
        return 1;
    } else {
        return 0;
    }
}

int json_detect(const char *json, jsmntok_t *tokens, int frequency, char (*keywords)[12])
{
    long id;

    if((id = select_db()) == -1){
        return 0;
    } else if(id == -2){
        return insert_db(json, tokens, frequency, keywords); // insère si table vide
    } else {
        return update_db(json, tokens, frequency, keywords, id); // sinon on update juste
    }
}

int insert_db(const char *json, jsmntok_t *tokens, int frequency, char (*keywords)[12])
{
    char response[100] = "";
    char keys[1000] = "";
    char values[1000] = "";
    char request[1000] = "";
    int index;
    int counter;
    int singleDescription = 0;

    for(index = 0; index < 10; index++){
        for(counter = 0; counter < frequency; counter++){
            if(jsoneq(json, &tokens[counter], keywords[index])){
                sprintf(response, "%.*s", tokens[counter + 1].end - tokens[counter + 1].start, json + tokens[counter + 1].start);

                if(!strcmp("description", keywords[index])){ // traitement pour la description étant donnée que le résultat est une chaine de caractères
                    if(!singleDescription){
                        strcat(keys, keywords[index]);
                        strcat(keys, ",");

                        strcat(values, "'");
                        strcat(values, response);
                        strcat(values, "'");

                        singleDescription = 1;
                    } else {
                        continue;
                    }
                } else {
                    strcat(keys, keywords[index]); // alors que les autres ce sont des entiers
                    strcat(keys, ",");

                    strcat(values, response);
                }

                strcat(values, ",");
                counter++;
            }
        }
    }

    strcat(keys, "date");
    sprintf(response, "%ld", (long)time(NULL));
    strcat(values, response);

    sprintf(request, "INSERT INTO meteo(%s) VALUES(%s)", keys, values); // requête
    return inject_db(request);
}

int update_db(const char *json, jsmntok_t *tokens, int frequency, char (*keywords)[12], long id)
{
    char response[100] = "";
    char values[1000] = "";
    char request[1000] = "";
    int index;
    int counter;
    int singleDescription = 0;

    for(index = 0; index < 10; index++){
        for(counter = 0; counter < frequency; counter++){
            if(jsoneq(json, &tokens[counter], keywords[index])){
                sprintf(response, "%.*s", tokens[counter + 1].end - tokens[counter + 1].start, json + tokens[counter + 1].start);

                if(!strcmp("description", keywords[index])){
                    if(!singleDescription){
                        strcat(values, keywords[index]);
                        strcat(values, "=");

                        strcat(values, "'");
                        strcat(values, response);
                        strcat(values, "'");

                        singleDescription = 1;
                    } else {
                        continue;
                    }
                } else {
                    strcat(values, keywords[index]);
                    strcat(values, "=");

                    strcat(values, response);
                }

                strcat(values, ",");
                counter++;
            }
        }
    }

    strcat(values, "date");
    sprintf(response, "=%ld", (long)time(NULL));
    strcat(values, response);

    sprintf(request, "UPDATE meteo SET %s WHERE id=%ld", values, id); // requête
    return inject_db(request);
}

int inject_db(const char *request)
{
    MYSQL *mysql;

    mysql = mysql_init(NULL);
    mysql_options(mysql, MYSQL_READ_DEFAULT_GROUP, "");

    if(!mysql_real_connect(mysql, "localhost", USER, PASSWORD, DATABASE, 0, NULL, 0)){
        return 0;
    }

    if(mysql_query(mysql, request)){
        return 0;
    }

    mysql_close(mysql);

    return 1;
}

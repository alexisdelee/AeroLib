#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <errno.h>

#include <mysql/mysql.h>
#include "../h/traitment.h"

// Analyse du flux en JSON
int json_analyze(const char *conf, const char *json)
{
    int error;
    char keywords[10][12] = {"description", "temp", "pressure", "humidity", "temp_min", "temp_max", "visibility", "speed", "sunrise", "sunset"};
    jsmn_parser p;
    jsmntok_t tokens[128];

    jsmn_init(&p);
    error = jsmn_parse(&p, json, strlen(json), tokens, sizeof(tokens) / sizeof(tokens[0]));
    if(error < 0){
        return logfile(0, NOWDATE, "failed to parse JSON");
    } else if(error < 1 || tokens[0].type != JSMN_OBJECT){
        return logfile(0, NOWDATE, "object expected");
    }

    return insert_db(conf, json, tokens, error, keywords);
}

int jsoneq(const char *json, jsmntok_t *tok, const char *keyword)
{
    if(tok->type == JSMN_STRING && (int)strlen(keyword) == tok->end - tok->start && strncmp(json + tok->start, keyword, tok->end - tok->start) == 0){
        return 1;
    } else {
        return logfile(0, NOWDATE, "unexpected key");
    }
}

// Fonction pour analyse avant insertion dans la BDD
int insert_db(const char *conf, const char *json, jsmntok_t *tokens, int frequency, char (*keywords)[12])
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

                if(!strcmp("description", keywords[index])){
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
                    strcat(keys, keywords[index]);
                    strcat(keys, ",");

                    strcat(values, response);
                }

                strcat(values, ",");
                counter++;
            }
        }
    }

    strcat(keys, "date");
    sprintf(response, "%ld", NOWDATE);
    strcat(values, response);

    sprintf(request, "INSERT INTO weather(%s) VALUES(%s)", keys, values);
    return readConf(conf, request);
}

// Fonction pour lire le fichier de configuration de la BDD
int readConf(const char *path, const char *request)
{
    FILE *fconf = NULL;
    char content[256];
    char host[256] = "";
    char database[256] = "";
    char user[256] = "";
    char password[256] = "";
    int end;
    int index = 0;

    fconf = fopen(path, "r");
    if(fconf != NULL) {
        while(fgets(content, 256, fconf) != NULL) {
            end = strlen(content);

            if(content[end - 1] == '\n') {
                content[end - 1] = '\0';
            }

            switch(index) {
                case 0:
                    strcat(host, content);
                    break;
                case 1:
                    strcat(database, content);
                    break;
                case 2:
                    strcat(user, content);
                    break;
                case 3:
                    strcat(password, content);
                    break;
            }

            index++;
        }

        fclose(fconf);
    } else {
        return logfile(0, NOWDATE, "can not access configuration file");
    }

    if(index == 4) {
        return inject_db(request, host, database, user, password);
    } else {
        return logfile(0, NOWDATE, "configuration file is incorrectly configured");
    }
}

// Fonction pour injection en BDD
int inject_db(const char *request, const char *host, const char *database, const char *user, const char *password)
{
    MYSQL *mysql;

    mysql = mysql_init(NULL);
    mysql_options(mysql, MYSQL_SET_CHARSET_NAME, "utf8");
    mysql_options(mysql, MYSQL_INIT_COMMAND, "SET NAMES utf8");

    if(!mysql_real_connect(mysql, host, user, password, database, 0, NULL, 0)){
        return logfile(0, NOWDATE, "database connection error");
    }

    mysql_query(mysql, request);
    mysql_close(mysql);

    return 1;
}

// Fonction de création du fichier log
int logfile(int status, long date, const char *message)
{
    FILE *flog = NULL;
    char format[32];
    char path[26];
    struct tm lt;
    time_t t = (time_t)date;

    sprintf(path, "log/archive-%ld.log", date);
    flog = fopen(path, "w");

    if(flog != NULL) {
        localtime_r(&t, &lt);

        if(strftime(format, sizeof(format), "%a %b %d %Y %H:%M", &lt)) {
            fprintf(flog, "%s log status\n", format);
            fprintf(flog, "[ %s ] %s\n", status == 0 ? "ERROR" : "OK", message);
        }

        fclose(flog);
    }

    return status;
}

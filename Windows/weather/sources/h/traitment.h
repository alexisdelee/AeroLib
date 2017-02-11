#ifndef TRAITMENT_H_INCLUDED
#define TRAITMENT_H_INCLUDED

#include "../../ext/jsmn/include/jsmn.h"

long select_db();
int json_analyze(const char *);
int jsoneq(const char *, jsmntok_t *, const char *);
int json_detect(const char *, jsmntok_t *, int, char (*)[12]);
int insert_db(const char *, jsmntok_t *, int, char (*)[12]);
int update_db(const char *, jsmntok_t *, int, char (*)[12], long);
int inject_db(const char *);

#endif // TRAITMENT_H_INCLUDED

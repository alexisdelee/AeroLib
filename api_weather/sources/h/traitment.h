#ifndef TRAITMENT_H_INCLUDED
#define TRAITMENT_H_INCLUDED

#include <time.h>
#include "../../ext/jsmn/include/jsmn.h"

#define NOWDATE (long)time(NULL)

int json_analyze(const char *, const char *);
int jsoneq(const char *, jsmntok_t *, const char *);
int insert_db(const char *, const char *, jsmntok_t *, int, char (*)[12]);
int readConf(const char *, const char *);
int inject_db(const char *, const char *, const char *, const char *, const char *);
int logfile(int, long, const char *);

#endif // TRAITMENT_H_INCLUDED

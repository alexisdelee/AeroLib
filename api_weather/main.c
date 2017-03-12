#include <stdio.h>
#include <stdlib.h>
#include <string.h>

#include "sources/h/traitment.h"

int main(int argc, char **argv)
{
    char buff[1035];

    if(argc != 2) {
        return logfile(0, (long)time(NULL), "an expected parameter: missing configuration file");
    }

    if(fgets(buff, sizeof buff, stdin) != NULL) {
        if(!json_analyze(argv[1], buff)){
            return 0;
        }
    }

    return logfile(1, NOWDATE, "weather application completed correctly");
}

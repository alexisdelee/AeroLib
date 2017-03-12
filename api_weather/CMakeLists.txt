cmake_minimum_required(VERSION 3.6)
project(api_weather)

set(CMAKE_CXX_STANDARD 11)

set(SOURCE_FILES
        ext/jsmn/include/jsmn.h
        ext/jsmn/lib/jsmn.c
        sources/c/traitment.c
        sources/h/traitment.h
        main.c)

add_executable(api_weather ${SOURCE_FILES})
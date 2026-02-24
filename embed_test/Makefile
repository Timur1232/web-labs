CFLAGS= \
	-I/usr/include/php \
    -I/usr/include/php/main \
    -I/usr/include/php/Zend \
    -I/usr/include/php/TSRM \
    -I/usr/include/php/sapi/embed \
    -I/usr/include/php/php

LIBS= \
	-lphp

main: main.c
	clang main.c $(CFLAGS) $(LIBS) -o main

run: main main.c
	./main

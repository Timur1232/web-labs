#!/bin/bash

export REQUEST_METHOD="POST"
export SCRIPT_FILENAME=test.php
export QUERY_STRING="test_query=true&urmom=69"
export REDIRECT_STATUS=200
export SCRIPT_NAME="script_name"
export PATH_INFO="/path_info/urmom"
export REQUEST_URI="/path_info/urmom"
export CONTENT_LENGTH="17"
export CONTENT_TYPE="application/x-www-form-urlencoded"

cat test_post.txt | php-cgi

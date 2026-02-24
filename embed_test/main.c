#include <sapi/embed/php_embed.h>
#include <stdio.h>

PHP_FUNCTION(hello)
{
    char* name;
    if (zend_parse_parameters(ZEND_NUM_ARGS(), "s", &name) == FAILURE) {
        return;
    }
    printf("Hello from C!\n");
    printf("Hello %s!\n", name);
    RETURN_LONG(69);
}

ZEND_BEGIN_ARG_INFO(arginfo_hello, 0)
    ZEND_ARG_INFO(0, name)
ZEND_END_ARG_INFO()

static zend_function_entry my_functions[] = {
    PHP_FE(hello, arginfo_hello)
    PHP_FE_END
};

zend_module_entry hello_mod_module_entry = {
    STANDARD_MODULE_HEADER,
    "hello_mod",
    my_functions,
    NULL, NULL, NULL, NULL, NULL,
    "1.0.0",
    STANDARD_MODULE_PROPERTIES
};

int main() {
    PHP_EMBED_START_BLOCK(0, NULL);

    zend_register_internal_module(&hello_mod_module_entry);

    putenv("REQUEST_METHOD=POST");
    putenv("REQUEST_URI=/api/user?id=123");
    putenv("HTTP_USER_AGENT=MyCustomServer/1.0");

    zend_file_handle file_handle;
    file_handle.type = ZEND_HANDLE_FILENAME;
    file_handle.filename = ZSTR_INIT_LITERAL("./test.php", false);
    file_handle.opened_path = NULL;

    php_execute_script(&file_handle);

    PHP_EMBED_END_BLOCK();
    return 0;
}

<?php

namespace DoliCore;

$dolibase_path = get_dolibase_path();

return array(
    'main' => array(
        'version' => '3.0.7', // possible values: a.b.c-alpha, a.b.c-beta, a.b.c-rcX or a.b.c
        'env' => 'prod', // environment: dev or prod
        'link' => 'https://github.com/AXeL-dev/dolibase',
        'path' => $dolibase_path,
        'tables' => array('logs'),
        'use_ajax_on_confirm' => true
    ),
    'langs' => array(
        'path' => $dolibase_path // for backward compatibility
    )
);

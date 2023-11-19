<?php

namespace AZahraoui\DoliCore\Js;

header('Content-Type: application/javascript');

?>

$(document).ready(function () {
    $('.dolibase_select').select2({
        dir: 'ltr',
        width: 'resolve',       /* off or resolve */
        minimumInputLength: 0
    });
});

<?php

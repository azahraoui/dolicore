<?php

namespace AZahraoui\DoliCore\Js;

header('Content-Type: application/javascript');

?>

$(document).ready(function () {

    $(document).click(function () {
        $('.dropdown-click .dropdown-content').removeClass('show');
    });

    $('.drop-btn').click(function (e) {
        e.stopPropagation();
        $('.dropdown-click .dropdown-content').removeClass('show');
        $(this).next().addClass('show');
    });
});

<?php

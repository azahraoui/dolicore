<?php

namespace AZahraoui\DoliCore\Css;

header('Content-Type: text/css');

?>

.disabled {
    cursor: not-allowed !important;
    opacity: 0.4;
    filter: alpha(opacity=40);
    box-shadow: none;
    -webkit-box-shadow: none;
    -moz-box-shadow: none;
}

.nopointerevents {
    pointer-events: none;
}

<?php

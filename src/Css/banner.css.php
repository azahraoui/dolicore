<?php

namespace AZahraoui\DoliCore\Css;

header('Content-Type: text/css');

?>

/*--- Hide Banner second picture ---*/

.arearef .floatleft .divphotoref:nth-child(2) {
    display: none !important;
}

/*--- Set default image size ---*/

.divphotoref .photoref img {
    width: 40px;
    height: 40px;
}

<?php

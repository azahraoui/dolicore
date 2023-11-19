<?php

namespace AZahraoui\DoliCore\Css;

header('Content-Type: text/css');

?>

.release-details {
    padding: 5px 0;
}

.badge {
    margin-right: 5px;
    border-radius: 2px;
    min-width: 50px;
    padding: 4px;
}

.badge.new {
    background-color: #2ecc71;
}

.badge.upgrade, .badge.experimental {
    background-color: #f1c40f;
}

.badge.fix {
    background-color: #e74c3c;
}

.badge.other {
    background-color: #3498db;
}

<?php

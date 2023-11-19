<?php

namespace AZahraoui\DoliCore\Js;

header('Content-Type: application/javascript');

?>

$(document).ready(function () {

    // Save as PDF
    $("#save_as_pdf").on('click', function (event) {
        var table = $('table').get(0);
        var filename = $(document).find('title').text() + '.pdf';
        var doc = new jsPDF('p', 'pt'); // 'p' for a vertical orientation & 'l' for an horizontal orientation
        var res = doc.autoTableHtmlToJson(table);
        $('div.pagination').hide();
        doc.autoTable(res.columns, res.data, {
            theme: 'plain',
            styles: {
                fontSize: 8,
                overflow: 'linebreak'
            }
        });
        $('div.pagination').show();
        doc.save(filename);
    });

    // Save as CSV
    $("#save_as_csv").on('click', function (event) {
        var table = $('table').first();
        var filename = $(document).find('title').text() + '.csv';
        table.table2csv('download', {
            filename: filename,
            excludeTags: 'div.pagination'
        });
    });
});

<?php

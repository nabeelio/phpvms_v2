<?php
include '../core/codon.config.php';
?>
<html>
<head>
<title>phpVMS Install Checker</title>
<style>
body { font-family: "Lucida Grande" , Verdana, Geneva, Sans-serif; font-size: 11px; line-height: 1.8em; } 
span { font-weight: bold; }
.style1 { color: #F60; font-size: x-large; filter: DropShadow(Color=#000, OffX=5, OffY=5, Positive=10); }
		.style2 { font-size: small; }
	</style>
</head>
<body>
<strong><span class="style1">phpVMS</span> <span class="style2">Virtual Airline Administration Software</span></strong><br />
<strong>Database Check</strong>
<br /><br />
<?php

function error($title, $txt) {
    echo "<span style=\"color: red\">[{$title}]</span> {$txt}";
}

function success($title, $txt) {
    echo "<span style=\"color: #006600\">[{$title}]</span> {$txt}";
}

/* Check database */

$db = simplexml_load_file(dirname(__file__) . '/sql/structure.xml');
foreach ($db->database->table_structure as $table) {
    $tablename = str_replace('phpvms_', TABLE_PREFIX, $table['name']);
    echo "<strong>Checking {$tablename}...</strong>";

    DB::query('SELECT * FROM ' . $tablename . ' WHERE 1=1 LIMIT 1');

    if (DB::$errno == '1146') {
        echo '<br >';
        error('ERROR', "The table <strong>{$tablename}</strong> is missing!<br /><br />");
        continue;
    }

    /* loop through all the columns returned by the above query and all the columns
    from the fields in the xml file, and make sure they all match up, with the
    fieldlist from the xml being the "master" outside loop which it looks up against */
    $anyerrors = false;
    $colinfo = DB::$DB->col_info;
    foreach ($table->field as $field) {
        
        $found = false;
        foreach ($colinfo as $column) {
            if ($column->name == $field['Field']) {
                $found = true;
                break;
            }
        }

        if ($found == false) {
            echo '<br > ';
            error('ERROR', "The column <strong>{$field['Field']}</strong> is missing!");
            $anyerrors = true;
        }
    }

    if ($anyerrors == false) {
        echo 'OK';
    }

    echo '<br />';
}

?>

</body>
</html>
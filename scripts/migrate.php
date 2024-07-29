<?php
require_once __DIR__ . '/../src/Core/autoload.php';

use Core\Database;

$db = Database::getInstance()->getConnection();

$sqlFile = __DIR__ . '/../database/schema.sql';

$sql = file_get_contents($sqlFile);

if ($sql === false) {
    die("Error reading SQL file.");
}

if ($db->multi_query($sql)) {
    do {
        if ($result = $db->store_result()) {
            $result->free();
        }
    } while ($db->more_results() && $db->next_result());
    echo "Tables created successfully.";
} else {
    echo "Error creating tables: " . $db->error;
}

$db->close();

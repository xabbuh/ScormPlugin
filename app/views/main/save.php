<?php
if (count($errors) > 0):
    echo "<ul>";
    foreach ($errors as $error):
        echo "<li>" . htmlReady($error) . "</li>";
    endforeach;
    echo "</ul>";
endif;

include __DIR__ . "/add.php";

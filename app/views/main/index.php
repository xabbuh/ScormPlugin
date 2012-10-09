<h1>Aktive Lerneinheiten</h1>

<?php
if(count($learningUnits) == 0):
    printf("<p>%s</p>", _("Keine aktiven Lerneinheiten"));
else:
    echo "<ul>";
    foreach($learningUnits as $learningUnit):
        printf('<li><a href="%s">%s</a></li>',
                PluginEngine::getLink($GLOBALS["plugin"], array("intro" => "yes"),
                        "main/player/{$learningUnit["id"]}"),
                $learningUnit["name"]);
    endforeach;
    echo "</ul>";
endif;
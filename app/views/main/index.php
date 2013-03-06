<h1>Aktive Lerneinheiten</h1>

<?php
if(count($learningUnits) == 0):
    printf("<p>%s</p>", _("Keine aktiven Lerneinheiten"));
else:
    echo "<table width='100%'>";
    echo "<tbody>";
    foreach($learningUnits as $learningUnit):
        echo "<tr>";
        printf('<td><a href="%s">%s</a></td>',
                PluginEngine::getLink($GLOBALS["plugin"], array("intro" => "yes"),
                        "main/player/{$learningUnit["id"]}"),
                $learningUnit["name"]);
        printf(
            '<td><a href="%s">%s</a></td>',
            PluginEngine::getLink(
                $GLOBALS["plugin"],
                array(),
                "main/delete/{$learningUnit["id"]}"
            ),
            _("löschen")
        );
        echo "</tr>";
    endforeach;
    echo "</tbody>";
    echo "</table>";
endif;
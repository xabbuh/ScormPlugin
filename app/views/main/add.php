<?php
$actionUrl = PluginEngine::getURL($GLOBALS["plugin"], array(), "main/save");
?>
<form method="post" action="<?=$actionUrl?>" enctype="multipart/form-data">
    <fieldset>
        <legend><?php echo _("Grundeinstellungen") ?></legend>
        <div>
            <label for="scorm_name">
                <?php echo _("Name") ?>
            </label>
        </div>
        <div>
            <input type="text" name="name" id="scorm_name"/>
        </div>

        <div>
            <label for="scorm_editor">
                <?php echo _("Beschreibung") ?>
            </label>
        </div>
        <div>
            <textarea name="introeditor[text]" id="scorm_editor"></textarea>
        </div>

        <div>
            <label for="scorm_file">
                <?php echo _("Lernpaketdatei") ?>
            </label>
        </div>
        <div>
            <input type="file" name="packagefilechoose" id="scorm_file"/>
        </div>
    </fieldset>
    
    <fieldset>
        <legend><?php echo _("Zeitbeschränkungen") ?></legend>
        <div>
            <?php echo _("Offen von") ?>
        </div>
        <div>
            <select name="timeopen[day]" disabled="disabled">
                <?php
                for ($i = 1; $i <= 31; $i++):
                    printf('<option value="%d">%02d</option>', $i, $i);
                endfor;
                ?>
            </select>
            <select name="timeopen[month]" disabled="disabled">
                <option value="1"><?php echo _("Januar") ?></option>
                <option value="2"><?php echo _("Februar") ?></option>
                <option value="3"><?php echo _("März") ?></option>
                <option value="4"><?php echo _("April") ?></option>
                <option value="5"><?php echo _("Mai") ?></option>
                <option value="6"><?php echo _("Juni") ?></option>
                <option value="7"><?php echo _("Juli") ?></option>
                <option value="8"><?php echo _("August") ?></option>
                <option value="9"><?php echo _("September") ?></option>
                <option value="10"><?php echo _("Oktober") ?></option>
                <option value="11"><?php echo _("November") ?></option>
                <option value="12"><?php echo _("Dezember") ?></option>
            </select>
            <select name="timeopen[year]" disabled="disabled">
                <?php
                $currentYear = date("Y");
                for ($i = 0; $i < 10; $i++):
                    printf('<option value="%d">%d</option>', $currentYear + $i,
                            $currentYear + $i);
                endfor;
                ?>
            </select>
            <select name="timeopen[hour]" disabled="disabled">
                
            </select>
            <select name="timeopen[minute]" disabled="disabled">
                <?php
                for ($i = 0; $i < 60; $i += 5):
                    printf('<option value="%d">%02d</option>', $i, $i);
                endfor;
                ?>
            </select>
            <input type="checkbox" name="timeopen[enabled]" id="scorm_timeopen_active"/>
            <label for="scorm_timeopen_active">
                <?php echo _("Aktiviert") ?>
            </label>
        </div>

        <div>
            <?php echo _("Bis") ?>
        </div>
        <div>
            <select name="timeclose[day]" disabled="disabled">
                <?php
                for ($i = 1; $i <= 31; $i++):
                    printf('<option value="%d">%02d</option>', $i, $i);
                endfor;
                ?>
            </select>
            <select name="timeclose[month]" disabled="disabled">
                <option value="1"><?php echo _("Januar") ?></option>
                <option value="2"><?php echo _("Februar") ?></option>
                <option value="3"><?php echo _("März") ?></option>
                <option value="4"><?php echo _("April") ?></option>
                <option value="5"><?php echo _("Mai") ?></option>
                <option value="6"><?php echo _("Juni") ?></option>
                <option value="7"><?php echo _("Juli") ?></option>
                <option value="8"><?php echo _("August") ?></option>
                <option value="9"><?php echo _("September") ?></option>
                <option value="10"><?php echo _("Oktober") ?></option>
                <option value="11"><?php echo _("November") ?></option>
                <option value="12"><?php echo _("Dezember") ?></option>
            </select>
            <select name="timeclose[year]" disabled="disabled">
                <?php
                $currentYear = date("Y");
                for ($i = 0; $i < 10; $i++):
                    printf('<option value="%d">%d</option>', $currentYear + $i,
                            $currentYear + $i);
                endfor;
                ?>
            </select>
            <select name="timeclose[hour]" disabled="disabled">
                
            </select>
            <select name="timeclose[minute]" disabled="disabled">
                <?php
                for ($i = 0; $i < 60; $i += 5):
                    printf('<option value="%d">%02d</option>', $i, $i);
                endfor;
                ?>
            </select>
            <input type="checkbox" name="timeclose[enabled]" id="scorm_timeclose_active"/>
            <label for="scorm_timeclose_active">
                <?php echo _("Aktiviert") ?>
            </label>
        </div>
    </fieldset>
    
    <fieldset>
        <legend><?php echo _("Anzeigeeinstellungen") ?></legend>
        <div>
            <label for="scorm_popup">
                <?php echo _("Lernpaket anzeigen") ?>
            </label>
        </div>
        <div>
            <select name="popup" id="scorm_popup">
                <option value="0"><?php echo _("Aktuelles Fenster") ?></option>
                <option value="1"><?php echo _("In neuem Fenster") ?></option>
            </select>
        </div>
    </fieldset>
    
    <fieldset>
        <legend><?php echo _("Bewertungseinstellungen") ?></legend>
        <div>
            <label for="scorm_grademethod">
                <?php echo _("Bewertungsmethode") ?>
            </label>
        </div>
        <div>
            <select name="grademethod" id="scorm_grademethod">
                <option value="0"><?php echo _("Zahl der Lernobjekte") ?></option>
                <option value="1"><?php echo _("Höchstnote") ?></option>
                <option value="2"><?php echo _("Durchschnittsnote") ?></option>
                <option value="3"><?php echo _("Summe der Bewertungen") ?></option>
            </select>
        </div>

        <div>
            <label for="scorm_maxgrade">
                <?php echo _("Beste Bewertung") ?>
            </label>
        </div>
        <div>
            <select name="popup" id="scorm_maxgrade">
                <?php
                for ($i = 0; $i <= 100; $i++):
                    printf('<option value="%d">%d</option>', $i, $i);
                endfor;
                ?>
            </select>
        </div>

        <div>
            <label for="scorm_maxattempt">
                <?php echo _("Zahl der Versuche") ?>
            </label>
        </div>
        <div>
            <select name="maxattempt" id="scorm_maxattempt">
                <option value="0"><?php echo _("Unbegrenzte Zahl der Versuche") ?></option>
                <option value="1">1 <?php echo _("Versuch") ?></option>
                <option value="2">2 <?php echo _("Versuche") ?></option>
                <option value="3">3 <?php echo _("Versuche") ?></option>
                <option value="4">4 <?php echo _("Versuche") ?></option>
                <option value="5">5 <?php echo _("Versuche") ?></option>
                <option value="6">6 <?php echo _("Versuche") ?></option>
            </select>
        </div>
            
        <div>
            <label for="scorm_whatgrade">
                <?php echo _("Bewertung der Versuche") ?>
            </label>
        </div>
        <div>
            <select name="whatgrade" id="scorm_whatgrade">
                <option value="0"><?php echo _("Bester Versuch") ?></option>
                <option value="1">1 <?php echo _("Durchschnitt") ?></option>
                <option value="2">2 <?php echo _("Erster Versuch") ?></option>
                <option value="3">3 <?php echo _("Letzter vollständiger Versuch") ?></option>
            </select>
        </div>
    </fieldset>
</form>
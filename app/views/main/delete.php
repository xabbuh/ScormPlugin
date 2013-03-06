<p>
    <?php echo _("Soll das Lernmodul wirklich gelöscht werden?") ?>
</p>

<?php
$url = PluginEngine::getLink($GLOBALS["plugin"], array(), "main/delete/$id");
?>
<form method="get" action="<?php echo $url ?>">
    <input type="hidden" name="cid" value="<?php echo $cid ?>"/>
    <?php
    echo Studip\Button::createAccept(_("Ja"), null, array("value" => "yes"));
    echo Studip\Button::createCancel(_("Nein"));
    ?>
</form>

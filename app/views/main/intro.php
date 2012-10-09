<h1><?=$learningUnit["name"]?></h1>

<?php
if($learningUnit["introduction_text"]):
    printf('<p>%s</p>', $learningUnit["introduction_text"]);
endif;
?>

<p>
  <?php
  echo _("Zahl zulässiger Versuche") . ": ";
  if($learningUnit["maxattempts"] == 0):
      echo _("unbegrenzt");
  else:
      echo $learningUnit["maxattempts"];
  endif;
  ?>
  <br/>
  
  <?php
  echo _("Zahl Ihrer Versuche") . ": ";
  ?>
  <br/>
  
  <?php
  echo _("Bewertungsmethode") . ": ";
  switch($learningUnit["whatgrade"]):
      case "best":
          echo _("Bester Versuch");
          break;
      case "average":
          echo _("Durchschnitt");
          break;
      case "first":
          echo _("Erster Versuch");
          break;
      case "last":
          echo _("Letzter vollständiger Versuch");
          break;
  endswitch;
  ?>
  <br/>
  
  <?php
  echo _("Bewertung veröffentlicht") . ": ";
  ?>
  <br/>
</p>

<p>
  <?php
  printf('<a href="%s"%s>Start</a>',
          PluginEngine::getURL($GLOBALS["plugin"], array(),
                  "main/player/{$learningUnit["id"]}"),
          $learningUnit["popup"] ? " target='_blank'" : "");
  ?>
</p>
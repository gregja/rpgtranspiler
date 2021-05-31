<?php

require_once ('../inc/config.php');

echo HtmlToolbox::entetePage($appname, array(), 1);

?>
<br/>
<fieldset class="ui-widget ui-widget-content ui-corner-all">
<legend class="ui-widget ui-widget-header ui-corner-all"> Présentation de RPG Transpiler </legend>
<div>
    <br><br>
    <p><?php echo $appname;?> convertit le code RPG au format fixe vers son équivalent en format RPG Free.</p>
    <p>Lien vers <a href="cnv_rpg2free.php"><?php echo $appname;?></a></p>
</div>
<br/><br/>
</fieldset>
<br/><br/>

<?php
echo HtmlToolbox::piedPage();




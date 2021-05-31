<?php
require_once '../inc/config.php';
require_once '../inc/class/CvtsrcRPG2Free.php';
            
$param_js = array();
$param_js['css_specif_code'] = <<<BLOC_JS
<style type="text/css">
#convertForm { width: 600px; }
#convertForm label { width: 250px; }
#convertForm label.error, #convertForm button.submit { margin-left: 253px; }
</style>
BLOC_JS;
$param_js['jquery_specif'] = <<<BLOC_JS
    $('.loading').hide() ;
    // validate signup form on keyup and submit

    $("#convertForm").on('submit', function(e){
        $('#resultat').hide() ;
        $("#convertForm").validate();
        if ($("#convertForm").valid()) {
            $('.loading').show() ;
        } else {
            $('#resultat').show() ;
            e.preventDefault() ;
        }
    })
BLOC_JS;

echo HtmlToolbox::entetePage('Conversion de code source RPG', $param_js, 1);

function conversion_exit($msg, $file = '') {
    if ($file != '') {
        @unlink($file);
    }
    sleep(3);
    exit($msg);
}

$maxsize = 30000;
$uri = $_SERVER['SCRIPT_NAME'];
if (!isset($_POST['token'])) {
    $token = Securize::generate_token();
    $_SESSION['security']['token'] = $token;
} else {
    $token = $_SESSION['security']['token'];
}
?>
<br/>
<form id="convertForm" action="<?php echo $uri; ?>" method="post" enctype="multipart/form-data" >
    <fieldset class="ui-widget ui-widget-content ui-corner-all">
        <legend class="ui-widget ui-widget-header ui-corner-all">Conversion avec <?php echo $appname; ?></legend>
        <p>
            <input type="hidden" name="token" value="<?php echo $token; ?>" />
            <label for="srcfile">Fichier source RPG (obligatoire)</label>        
            <!-- MAX_FILE_SIZE doit précéder le champ input de type file -->
            <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $maxsize; ?>" class="required" />
            <input type="file" name="srcfile" id="srcfile" size="42" class="required ui-widget-content" />
        </p>
        <p>
            <input type="submit" name="submit" value="Charger et convertir" />
            <img src="../images/ajax-loader.gif" alt="" class="loading" />
        </p>
    </fieldset>

</form>
<?php
if (isset($_POST) && count($_POST) > 0) {
    $errors = array();

    $src_final = '';
    
    $form_correct = true;
    $http_referer = parse_url($_SERVER['HTTP_REFERER']);
    if ($http_referer['scheme'] != 'http' && $http_referer['scheme'] != 'https') {
        $form_correct = false;
    }
    if ($form_correct) {
        if ($http_referer['host'] != $_SERVER['HTTP_HOST']) {
            $form_correct = false;
        }
    }
    if (!$form_correct) {
        error_log('FATAL ERROR : Formulaire compromis, protocole erroné ');
        $errors[] = 'Erreur: anomalie rencontrée dans le traitement du formulaire, traitement interrompu.';
    } else {
        if (!isset($_POST['token']) || $_POST['token'] != $token) {
            error_log('FATAL ERROR : Formulaire compromis, problème de jeton ');
            $errors[] = 'Attention : vous ne pouvez demander qu\'une seule conversion à la fois, reformulez votre demande en ouvrant un seul formulaire, SVP';
        }
    }

    echo '<br/>';

    if (!isset($_FILES['srcfile']['tmp_name']) || trim($_FILES['srcfile']['tmp_name']) == '') {
        $errors[] = 'Nom de fichier source obligatoire';
    }

    if (isset($_POST['cemail']) && trim($_POST['cemail']) != '') {
        $mail = Sanitize::blinderPost('cemail');
        if (strlen($mail) > 256) {
            $errors[] = 'Adresse Email erronée';
        } else {
            if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Adresse Email erronée';
            }
        }
    }
    if (count($errors) <= 0) {
        if ($_FILES['srcfile']['type'] != 'text/plain') {
            $errors[] = 'Erreur: le fichier source fourni n\'est pas de type "texte".';
        } else {
            if (intval($_FILES['srcfile']['size']) > $maxsize) {
                $errors[] = 'Taille du code source original supérieure à la limite autorisée.';
            }
            if (intval($_FILES['srcfile']['error']) != 0) {
                $errors[] = 'Erreur lors du chargement du fichier source, traitement interrompu.';
            }
        }
    }
    if (count($errors) <= 0) {
        $ticket = str_replace('.', '_', uniqid("src-", true));

        $dropFolder = dirname(dirname(__FILE__)) . '/tempload';
        $zipfile = "$dropFolder/$ticket.zip";

        $original = "$dropFolder/$ticket.before.txt";
        $encoded = "$dropFolder/$ticket.after.txt";

        $file_moved = @move_uploaded_file($_FILES['srcfile']['tmp_name'], $original);
        if (!$file_moved) {
            $errors[] = 'Erreur lors du chargement du fichier source, traitement interrompu.';
        }

        if (!is_readable($original)) {
            $errors[] = 'Le code source original est invalide.';
        } else {
            if (intval(filesize($original)) > $maxsize) {
                $errors[] = 'Taille du code source original supérieure à la limite autorisée.';
            }
            if (filetype($original) != 'file') {
                $errors[] = 'Type du code source original invalide.';
            }

            $conv = new CvtsrcRPG2Free($original, 'CALCRPG', 'PGM');
            $conv->conversion();
            $src_final = $conv->retrieve();

            if (trim($src_final) != '') {
                file_put_contents($encoded, $src_final);
            } else {
                $errors[] = 'Conversion non effectuée, code original non valide.';
            }

        }

    }
    if (count($errors) >= 0) {
        echo <<<BLOC_CODE
        <fieldset id="resultat" class="ui-widget ui-widget-content ui-corner-all">
            <legend class="ui-widget ui-widget-header ui-corner-all">Conversion RPG terminée</legend>
            <pre>$src_final</pre>
        </fieldset>
BLOC_CODE;
    }
}
echo '<br/>';

echo HtmlToolbox::piedPage();


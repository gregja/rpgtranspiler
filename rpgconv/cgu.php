<?php
require_once ('../inc/config.php');

$param_js = array();

echo HtmlToolbox::entetePage('Conversion de code source RPG', $param_js, 1);

?>
<br/>
<fieldset class="ui-widget ui-widget-content ui-corner-all">
    <legend class="ui-widget ui-widget-header ui-corner-all">Conditions générales 
        d'utilisation du module de conversion de code RPG</legend>
    <div class="notes">
        <h4>Article 1</h4>
        <p class="last">En utilisant le module de conversion de code RPG proposé 
            sur le présent site, vous reconnaissez avoir pris pleinement connaissance, 
            et accepter sans réserve, les conditions générales d'utilisation 
            décrites ci-dessous.</p>
    </div>
    <div class="notes">
        <h4>Article 2</h4>
        <p class="last">En utilisant le module de conversion de code RPG, vous 
            reconnaissez être détenteur des droits d'utilisation et de modification 
            du code source que vous soumettez à cet outil. Si vous n'êtes pas vous 
            même détenteur des droits, vous certifiez intervenir pour le compte 
            d'une personne physique ou morale détentrice des droits d'utilisation 
            et de modification du code RPG à convertir. </p>
    </div>
    <div class="notes">
        <h4>Article 3</h4>
        <p class="last">Le module de conversion de code RPG est conçu pour 
            "transformer" un code source RPG au format délimité, dans un code 
            source équivalent au format libre ("free format" pour les anglophones). 
            La conversion est prévue pour restituer un code source fournissant 
            strictement le même périmètre iso-fonctionnel. C'est la raison pour 
            laquelle, certaines fonctionnalités du RPG Fixe, qui n'ont pas 
            d'équivalent dans le RPG Free, sont conservées telles quelles dans 
            le code source généré. Ceci est rendu possible du fait que le compilateur
            RPG autorise l'utilisation des deux syntaxes, fixe et libre, au sein 
            d'un même programme. A ce propos, si le code source d'origine contenait 
            déjà des portions de code en RPG "free", ces portions de code sont 
            conservées telles quelles dans le code source de destination. Parmi 
            les instructions RPG qui ne sont pas converties en RPG Free, on trouve 
            notamment les instructions de débranchement de type "GOTO".</p>
    </div>
    <div class="notes">
        <h4>Article 4</h4>
        <p class="last">Le module de conversion de code RPG possède quelques 
            limitations. Par exemple, il est capable de tranformer des instructions 
            RPG de type MOVE ou MOVEL, en un code RPG Free équivalent, dans la 
            mesure où les données d'origine et de destination sont clairement 
            identifiées comme étant toutes deux de type alphabétiques. Or, seules
            les variables déclarées dans le code source RPG ont un type clairement 
            identifiable (les autres données n'étant pas fournies au convertisseur 
            de code, il n'est pas en mesure d'en déterminer le type). Donc dans 
            le cas d'un MOVE, ou un MOVEL, destiné à alimenter une zone écran, 
            une zone état, ou une colonne de table SQL, le type de la donnée 
            destinataire n'est pas "connu" et l'instruction n'est pas traduite 
            en RPG "free". Il faut souligner que les instructions MOVE et MOVEL 
            étaient traditionnellement utilisées, dans les vieux programmes RPG, 
            pour effectuer des conversions de type (alpha vers numérique par 
            exemple). Cette fonctionnalité n'est pas reconduite en RPG Free, qui 
            propose des fonctions de conversion de type dédiées à cet usage. 
            L'outil de conversion de code RPG ne prend pas en charge ces opérations
            de conversion. Pour connaîre les équivalences entre les vieilles 
            techniques RPG et celles en vigueur en RPG Free, je vous invite à 
            lire l'excellent livre "Free-Format RPG IV", écrit par Jim Martin, 
            et publié aux éditions MC Press.</p>
    </div>    
    <div class="notes">
        <h4>Article 5</h4>
        <p class="last">Le module de conversion de code RPG est développé avec 
            un maximum de soin, avec l'objectif de respecter au maximum l'intégrité
            du code source d'origine. Néanmoins, si des erreurs apparaissaient 
            dans le code source RPG généré, il est de la responsabilité du 
            propriétaire du source d'origine d'identifier et de corriger ces 
            erreurs. Aucune réclamation ne pourra être formulée à l'encontre de 
            l'auteur du composant de conversion de code RPG.</p>
    </div>
    <div class="notes">
        <h4>Article 6</h4>
        <p class="last">Le module de conversion de code RPG génére - pour chaque 
            demande de conversion - un fichier zip contenant le code source avant 
            conversion (src_before.txt) et le fichier source après conversion 
            (src_after.txt). Ce fichier zip est mis à disposition de l'utilisateur
            au moyen d'un lien apparaissant sous le formulaire dès que la conversion 
            est achevée. L'URL fournie n'est valide que 24 heures, passé ce délai
            le fichier zip est supprimé automatiquement, et n'est en aucun cas 
            archivé. Si vous souhaitez reconvertir un fichier source dont le lien 
            n'est plus valide, vous devez procéder à une nouvelle demande de conversion.</p>
    </div>
    <div class="notes">
        <h4>Article 7</h4>
        <p class="last">Le concepteur du module de conversion de code RPG s'astreint
            à des règles de confidentialité très strictes. Il s'interdit de conserver
            les sources traités au delà du délai indiqué dans l'article précédent. 
            Il s'interdit également de consulter le contenu des fichiers zip traités
            durant cette période. Il s'interdit de surcroît toute utilisation des 
            codes source déposés sur le serveur, que ce soit à des fins de veille 
            technologique, d'espionnage industriel, ou simplement à des fins de 
            démarchage commercial. </p>
    </div>
    <div class="notes">
        <h4>Article 8</h4>
        <p class="last">Le module de conversion de code RPG ne traite dans leur 
            intégralité que les fichiers sources RPG dont la taille n'excède pas 
            25.000 lignes de code. Les fichies source excédant cette limite sont 
            automatiquement tronqués de manière à ne pas dépasser cette limite.</p>
    </div>
    <div class="notes">
        <h4>Article 9</h4>
        <p class="last">L'adresse email qui vous est demandée sur le formulaire 
            est facultative. Saisissez-la uniquement si vous souhaitez être tenus 
            informés des nouveautés et modifications relatives à ce projet.</p>
    </div>
</fieldset>
<?php 

echo HtmlToolbox::piedPage();


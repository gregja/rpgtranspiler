<?php

abstract class Zip {

    public static function archive($archiveFile, $files) {
        
        $ZIP_ERROR = [
          ZipArchive::ER_EXISTS => 'File already exists.',
          ZipArchive::ER_INCONS => 'Zip archive inconsistent.',
          ZipArchive::ER_INVAL => 'Invalid argument.',
          ZipArchive::ER_MEMORY => 'Malloc failure.',
          ZipArchive::ER_NOENT => 'No such file.',
          ZipArchive::ER_NOZIP => 'Not a zip archive.',
          ZipArchive::ER_OPEN => "Can't open file.",
          ZipArchive::ER_READ => 'Read error.',
          ZipArchive::ER_SEEK => 'Seek error.',
        ];

        $answer = 0;
        $errmsg = '';
        $ziph = new ZipArchive();


        if (file_exists($archiveFile)) {
            $result_code = $ziph->open($archiveFile, ZIPARCHIVE::CHECKCONS);
//            $result_code = $ziph->open($archiveFile,  ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE);
            if( $result_code !== true ){
                $errmsg = isset($ZIP_ERROR[$result_code])? $ZIP_ERROR[$result_code] : 'Unknown error (1a).';
                $answer = 1;
            }
        } else {
            $result_code = $ziph->open($archiveFile, ZIPARCHIVE::CM_PKWARE_IMPLODE);
            if ($result_code !== true) {
                $errmsg = isset($ZIP_ERROR[$result_code])? $ZIP_ERROR[$result_code] : 'Unknown error (1b).';
                $answer = 1;
            }
        }
        foreach ($files as $filekey=>$filename) {
            $filename = str_replace('/home/gregja/Documents/htdocs/gregphpdemo/tempload', '/var/www/html/gregphpdemo/tempload/', $filename);
            //$filename = realpath($filename);
            if (file_exists($filename)) {
                $result_code = $ziph->addFile($filename, $filekey );
                if ($result_code !== true) {
                    $errmsg = "error archiving $filename in $archiveFile: ";
                    $errmsg .=  isset($ZIP_ERROR[$result_code])? $ZIP_ERROR[$result_code] : "Unknown error (2 / $filename).";
                    $answer = 2;
                }
            } else {
                $errmsg = "error archiving $filename in $archiveFile: file not found";
                $answer = 2;
            }
        }
        $ziph->close();

        return array($answer, $errmsg);
    }

}

/*
$zip = new ZipArchive();
$filename = "./test112.zip";

if ($zip->open($filename, ZIPARCHIVE::CREATE)!==TRUE) {
    exit("Impossible d'ouvrir <$filename>\n");
}

$zip->addFromString("testfilephp.txt" . time(), "#1 Ceci est une cha�ne texte, ajout�e comme testfilephp.txt.\n");
$zip->addFromString("testfilephp2.txt" . time(), "#2 Ceci est une cha�ne texte, ajout�e comme testfilephp2.txt.\n");
$zip->addFile($thisdir . "/too.php","/testfromfile.php");
echo "Nombre de fichiers : " . $zip->numFiles . "\n";
echo "statut :" . $zip->status . "\n";
$zip->close();
*/

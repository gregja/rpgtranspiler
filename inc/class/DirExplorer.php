<?php
/*
 * Classe destinées à extraire le contenu d'un répertoire, et
 * permettant d'afficher les caractéristiques (nb octets, type
 * de fichier, etc...)
 */
abstract class DirExplorer {
	public static function format_bytes($bytes) {
		// Define an array of the different display forms:
		// These are the SI approved binary variations:
		$display = array ('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB' );
		
		// Now, constantly divide the value by 1024 until it is less than 1024
		$level = 0;
		while ( $bytes > 1024 ) {
			$bytes /= 1024;
			$level ++;
		}
		
		// Now we have our final value, format it to just 1 decimal place
		// and append on to the the appropriate level moniker.
		return round ( $bytes, 1 ) . ' ' . $display [$level];
	}
	
	public static function recupExtension($fic) {
		// on s�pare toutes les parties du nom du fichier en utilisant
		// le point comme s�parateur et on stocke ces parties dans un tableau
		$ext = explode ( ".", $fic );
		// l'extension est contenue dans le dernier poste du tableau
		$nb = sizeof ( $ext ) - 1;
		return strtolower ( $ext [$nb] );
	}
	
	public static function DirIterator($path, $search_extension = '') {
		// This program works well as an index.php file in a directory you wish to
		//  display all files for.  It automatically excludes itself from the list,
		//  and is easy to modify to make it ignore others as well:
		

		// Define an array to hold the files
		$files = array ();
		
		// Open the current directory
		try {
			$dir = new DirectoryIterator ( $path );
			foreach ( $dir as $entry ) {
				$filename = $entry->getFilename ();
				if ((! $entry->isFile() || $entry->isDir()) && ($filename != basename ( $_SERVER ['PHP_SELF'] ))) {
					if ($entry->isFile()) {
						$extension = $entry->getExtension() ;  // self::recupExtension ( $filename );
						if ($search_extension == '' || $search_extension == $extension) {
							$files [] = array ("name" => $filename, "size" => $entry->getSize (), "type" => $entry->getType (), "mtime" => $entry->getMtime (), "path" => $entry->getPathname (), "read" => $entry->isReadable (), "write" => $entry->isWritable (), "exec" => $entry->getType () == 'file' ? $entry->isExecutable () : '', "ext" => $extension );
						}
					}
				}
			}
			
			// Release the resource.
			unset ( $dir );
			return $files;
		} catch ( Exception $e ) {
			// error_log( "Contenu du repertoire vide :". $path );
			return null;
		}
	}
	
	public static function DirUnlinks($path) {
		// Open the current directory
		try {
			$dir = new DirectoryIterator ( $path );
			foreach ( $dir as $entry ) {
				$filename = $entry->getFilename ();
				if ((! $entry->isDot ()) && ($filename != '~') && ($filename != basename ( $_SERVER ['PHP_SELF'] ))) {
					$asupprimer = $path . $filename;
					if (! is_dir ( $asupprimer )) {
						unlink ( $asupprimer );
					}
				
				}
			}
			
			// Release the resource.
			unset ( $dir );
			return true;
		} catch ( Exception $e ) {
			echo "Contenu du r&eacute;pertoire vide<br />";
			return null;
		}
	}
	
	public static function DirDisplay($path) {
		$files = self::DirIterator ( $path );
		$repert = $path == '.' ? "courant" : $path;
		// Now let's output a basic table with this information
		echo '<table><caption>Contenu du r&eacute;pertoire', $repert, '</caption>';
		
		// Sort the files so that they are alphabetical
		ksort ( $files );
		
		// Prepare for using date functions:
		date_default_timezone_set ( 'Europe/Paris' );
		
		// Now loop through them, echoing out a new table row for each one:
		foreach ( $files as $item => $stats ) {
			// Start the row, and output a link via the filename:
			echo "<tr><td><a href=\"{$stats['name']}\">{$stats['name']}</a></td>\n";
			echo '<td>', $stats ['type'], "</td>\n";
			echo '<td>', $stats ['ext'], "</td>\n";
			// Now a table cell with the filesize in bytes:
			$size = self::format_bytes ( $stats ['size'] );
			echo "<td align='right'>{$size}</td>\n";
			// Finally a column for the date:
			echo '<td>', date ( 'd-m-Y h:m:s', $stats ['mtime'] ), "</td>\n";
			// Finally a column for the path:
			echo '<td>', $stats ['path'], "</td>\n";
			$readwrite = "";
			$readwrite .= $stats ['read'] ? "R" : "";
			$readwrite .= $stats ['write'] ? "W" : "";
			echo '<td>', $readwrite, "</td>\n";
			echo '<td>', $stats ['exec'] ? "EXE" : "", "</td></tr>\n";
		}
		
		echo '</table>';
	}
	
	public static function DirExport($path) {
		$files = self::DirIterator ( $path );
		$files2 = array ();
		
		// Sort the files so that they are alphabetical
		ksort ( $files );
		
		// Prepare for using date functions:
		date_default_timezone_set ( 'Europe/Paris' );
		
		// Now loop through them, echoing out a new table row for each one:
		foreach ( $files as $item => $stats ) {
			// Start the row, and output a link via the filename:
			$files2 ['name'] = $stats ['name'];
			$files2 ['type'] = $stats ['type'];
			$files2 ['ext'] = $stats ['ext'];
			// Now a table cell with the filesize in bytes:
			$files2 ['size'] = self::format_bytes ( $stats ['size'] );
			// Finally a column for the date:
			$files2 ['date'] = date ( 'd-m-Y h:m:s', $stats ['mtime'] );
			// Finally a column for the path:
			$files2 ['path'] = $stats ['path'];
			$readwrite = "";
			$readwrite .= $stats ['read'] ? "R" : "";
			$readwrite .= $stats ['write'] ? "W" : "";
			$files2 [''] = $readwrite;
			echo '<td>', $readwrite;
			$files2 ['exec'] = $stats ['exec'] ? "EXE" : "";
		}
		
		return $files2;
	}
	
	// renvoie "true" si le suffixe du fichier est inclus dans la liste
	// des suffixes autorisés (liste définie dans $suffixe)
	// à noter : $suffixe peut recevoir soit un tableau contenant une liste de
	//  suffixes, soit une chaîne contenant un seul suffixe
	public static function ctrl_suffixe_fichier($fichier, $suffixe) {
		if (is_array ( $suffixe )) {
			$tab_suffixe = $suffixe;
		} else {
			$tab_suffixe = array ($suffixe );
		}
		$info = pathinfo ( $fichier );
		$suffixe = strtolower ( $info ['extension'] );
		if (in_array ( $suffixe, $tab_suffixe )) {
			return true;
		} else {
			return false;
		}
	}

}

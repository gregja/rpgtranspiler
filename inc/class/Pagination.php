<?php
/*
 * Classe permettant de générer une barre de pagination
 * Créée à partir de fonctions extraites de cet excellent livre : 
 * PHP Cookbook (2nd Edition), par Adam Trachtenberg et David Sklar, O'Reilly (2006) 
 * De légères modifications ont été apportées au code initial, telles que : 
 * - le regroupement de ces 2 fonctions dans une classe abstraite, sous forme de
 *   méthodes statiques, afin de renforcer la robustesse et de faciliter la
 *   réutilisation au sein de projets orientés objet 
 * - la possibilité de passer la page d'appel aux 2 méthodes, ceci afin de faciliter 
 *   la réutilisation de ces 2 m�thodes sur diff�rentes pages 
 * - il a été nécessaire d'ajouter un tableau $params permettant de transmettre d'une 
 *   page à l'autre des paramètres autres que l'offset, tels que les critéres de 
 *   sélection saisis sur le formulaire de recherche.
 * - le nombre de pages directement "appelables" a été limité à 5, des points de suspension
 *   sont ajoutés ensuite, et le lien vers la dernière page est ajouté en fin de barre de 
 *   pagination (la version initiale proposait un lien vers chaque page, ce qui
 *   donnait des résultats particulièrement laids sur des jeux de données de grande taille. 
 */

abstract class Pagination {
	
	public function __construct() {
		throw new Exception ( "Static class - instances not allowed." );
	}
	
	static public function pc_print_link($inactive, $text, $offset, $current_page, $params_page) {
		// on prépare l'URL avec tous les param�tres sauf "offset"
		if (! isset ( $offset ) or $offset == '' or $offset == '0') {
			$offset = '1';
		}
		$url = '';
		$params_page ['offset'] = $offset;
		$url = '?' . http_build_query ( $params_page );
		if ($inactive) {
			print "<span class='inactive'>$text</span>\n";
		} else {
			print "<span class='active'>" . "<a href='" . htmlentities ( $current_page ) . "$url'>$text</a></span>\n";
		}
	}
	
	static public function pc_indexed_links($total, $offset, $per_page, $curpage, $parmpage) {
		$separator = ' | ';
		
		self::pc_print_link ( $offset == 1, '<< Pr&eacute;c.', $offset - $per_page, $curpage, $parmpage );
		
		$compteur = 0;
		$top_suspension = false;
		
		// affichage de tous les groupes � l'exception du dernier
		for($start = 1, $end = $per_page; $end < $total; $start += $per_page, $end += $per_page) {
			$compteur += 1;
			if ($compteur < 5) {
				print $separator;
				self::pc_print_link ( $offset == $start, "$start-$end", $start, $curpage, $parmpage );
			} else {
				/*
				 * if ($compteur == 15) { $compteur = 0 ; print '<br />'.PHP_EOL ; } else { print $separator; } ;
				 */
				if (! $top_suspension) {
					$top_suspension = true;
					print ' | ... ';
				}
			}
		}
		
        $end = ($total > $start) ? '-'.$total : '';
		
		print $separator;
		self::pc_print_link ( $offset == $start, "$start$end", $start, $curpage, $parmpage );
		
		print $separator;
		self::pc_print_link ( $offset == $start, 'Suiv. >>', $offset + $per_page, $curpage, $parmpage );
	}
}

<?php
abstract class MenuApp {
	
    public static function initMenu() {			
            $menus = array ();
            $menus [] = array ('id' => 0, 'title' => 'RPGTranspiler', 'option' => 'rpgconv/index.php', 'test' => true  );
            return $menus;
    }

}
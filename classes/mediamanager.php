<?php
class KabinetMediaManager {

	public static function construct() {
		define('TITLE',hsc($lang['mediaselect']));
	}

	public static function getMediaTree() {
		$markup = false;

		ob_start();
		tpl_mediaTree();
		$markup = ob_get_contents();
		ob_end_clean();

		return $markup;
	}

	public static function printMediaTree() {
		echo self::getMediaTree();
	}

	public static function getMediaContent() {
		$markup = false;

		ob_start();
		tpl_mediaContent();
		$markup = ob_get_contents();
		ob_end_clean();

		return $markup;
	}

	public static function printMediaContent() {
		echo self::getMediaContent();
	}

	public static function getPageContent() {
		$markup = false;

		$title = '<h1>'.hsc($lang['mediaselect']).'</h1>';
		$tree = '<div id="media__tree">'.self::getMediaTree().'</div>'.PHP_EOL;
		$content = '<div id="media__content">'.self::getMediaContent().'</div>'.PHP_EOL;
		$list = '<div id="media__opts"></div>'.PHP_EOL;
		$script = '<script>(function(H){H.className=H.className.replace(/\bno-js\b/,"js")})(document.documentElement)</script>';
		$markup = $title.$tree.$list.$content.$script;

		return $markup;
	}

	public static function printPageContent() {
		echo self::getPageContent();
	}

}
KabinetMediaManager::construct();
?>

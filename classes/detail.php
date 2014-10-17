<?php
class KabinetImage {

	static $img;
	static $error;
	static $width;
	static $height;

	public static function construct() {
		$result = false;

		global $IMG,$ERROR;
		self::$img = $IMG;
		self::$error = $ERROR;
		self::$width = 900;
		self::$height = 700;

		define('TITLE',hsc(tpl_img_getTag('IPTC.Headline',self::$img))); 

		if(!self::$img) {
			return $result;
		} else {
			$result = true;
		}

		if(self::$error) {
			self::$title = self::$error;
			self::$image = '';
			self::$meta = '';
		} else {
			self::$title = nl2br(hsc(tpl_img_getTag('simple.title')));
			ob_start();
			tpl_img(self::$width,self::$height);
			self::$image = ob_get_contents();
			ob_clean();
			tpl_img_meta();
			self::$meta = ob_get_contents();
			ob_end_clean();
		}

		return $result;
	}

	public static function getPageContent() {
		$markup = false;

		$markup = '<h1 class="image-title">'.self::$title.'</h1>'.PHP_EOL;
		$markup .= '<div class="image-media">'.self::$image.'</div>'.PHP_EOL;
		$markup .= '<div class="image-meta">'.self::$meta.'</div>'.PHP_EOL;

		return $markup;
	}

	public static function printPageContent() {
		echo self::getPageContent();
	}
}
KabinetImage::construct();
?>

<?php
class Kabinet {

	static $info;
	static $conf;
	static $lang;
	static $act;
	static $htmlAttributes;
	static $windowTitle_delimiter;
	static $headerTitle_imageOrder;
	static $breadcrumbs_delimiter;
	static $youAreHere_delimiter;

	//-------------------------------------------------------------------------------
	//	Constructor
	//-------------------------------------------------------------------------------

	public static function construct() {
		global $INFO,$conf,$lang,$ACT;

		self::$info = &$INFO;
		self::$conf = &$conf;
		self::$lang = &$lang;
		self::$act = &$ACT;

		self::$info['hasSidebar'] = page_findnearest(self::$conf['sidebar']);
		self::$info['showSidebar'] = self::$info['hasSidebar']&&(self::$act=='show');

		self::$htmlAttributes = array(
			'lang' => self::$conf['lang'],
			'dir' => self::$lang['direction'],
			'class' => 'no-js',
		);
		self::$windowTitle_delimiter = ' | ';
		self::$headerTitle_imageOrder = array(
			':wiki:logo.png',
			':logo.png',
			'images/logo.png',
		);
		self::$breadcrumbs_delimiter = ' &middot; ';
		self::$youAreHere_delimiter = ' &rsaquo; ';
	}

	//-----------------------------------------------------------------------------
	//	Utilities
	//-----------------------------------------------------------------------------

	function buildAttributes($attributes=array()) {
		$markup = false;

		if(!empty($attributes)) {
			$markup = '';
			foreach($attributes as $key => $value) {
				$markup .= ' '.$key.'="'.$value.'"';
			}
		}

		return $markup;
	}

	function buildLinks($items=array(),$attributes=array()) {
		$markup = false;

		if(!empty($items)) {
			$markup = '';
			foreach($items as $item) {
				if(!is_object($item)) {
					$item = (object) $item;
				}
				$links[] = sprintf('<li class="%s"><a href="%s">%s%s</a></li>'.PHP_EOL,$item->id,$item->link,$item->icon,$item->label);
			}
			$markup .= '<div'.self::buildAttributes($attributes).'>'.PHP_EOL;
			$markup .= '<ul>'.PHP_EOL.implode(PHP_EOL,$links).PHP_EOL.'</ul>'.PHP_EOL;
			$markup .= '</div>'.PHP_EOL;
		}

		return $markup;
	}

	//-------------------------------------------------------------------------------
	//	Checker
	//-------------------------------------------------------------------------------

	public static function isUser() {
		global $INPUT;
		return $INPUT->server->bool('REMOTE_USER');
	}

	//-------------------------------------------------------------------------------
	//	Template tags
	//-------------------------------------------------------------------------------

	public static function getHtmlAttributes($attributes=array()) {
		$markup = false;

		$attributes = !empty($attributes)?$attributes:self::$htmlAttributes;
		$markup = self::buildAttributes($attributes);

		return $markup;
	}

	public static function printHtmlAttributes($attributes=array()) {
		echo self::getHtmlAttributes($attributes);
	}

	public static function getWindowTitle($title='') {
		$markup = false;
		global $INFO,$conf;
		$sitename = $conf['title'];
		if(!$title) {
			$title = defined('TITLE')?TITLE:$INFO['meta']['title'];
		}
		if($INFO['id']==$conf['start']) {
			$title = '';
		}
		$markup = ($title?$title.self::$windowTitle_delimiter:'').$sitename;
		return $markup;
	}

	public static function printWindowTitle($title='') {
		echo self::getWindowTitle($title);
	}

	public static function getBodyClasses($class=array()) {
		$classes = false;
		global $INFO;
		$class[] = 'site';
		$class[] = preg_replace('/[[:cntrl:][:blank:][:punct:]]/','_',$INFO['id']);
		$class[] = $INFO['showSidebar']?'showSidebar':'';
		$class[] = $INFO['hasSidebar']?'hasSidebar':'';

		ob_start();
		echo tpl_classes();
		$classes = ob_get_contents();
		ob_end_clean();

		$classes = implode(' ',$class).' '.$classes;
		return $classes;
	}

	public static function printBodyClasses($class=array()) {
		echo self::getBodyClasses($class);
	}

	public static function getHeaderTitle($options=array()){
		$markup = false;
		global $conf;
		$defaults = array(
			'image' => self::$headerTitle_imageOrder,
			'size' => array(),
			'title' => $conf['title'],
		);
		$filtered1 = array_merge($defaults,$options);
		$filtered2 = array_intersect_key($defaults,$filtered1);
		extract($filtered2);
		$logo = tpl_getMediaFile($image,false,$size);
		$markup = sprintf('<span class="logo"><img src="%s" %s alt="%s"></span><span class="label"><span>%s</span></span>',$logo,$size[3],$title,$title);

		ob_start();
		tpl_link(wl(),$markup,'accesskey="h" title="[H]"');
		$markup = ob_get_contents();
		ob_end_clean();

		return $markup;
	}

	public static function printHeaderTitle($options=array()) {
		echo self::getHeaderTitle($options);
	}

	public static function getBreadcrumbs($delimiter='') {
		$markup = false;
		$delimiter = $delimiter?$delimiter:self::$breadcrumbs_delimiter;
		global $INFO, $conf;
		$conf['breadcrumbs'] = true;
		ob_start();
		tpl_breadcrumbs($delimiter);
		$breadcrumbs = ob_get_contents();
		ob_end_clean();
		$markup = '<div id="breadcrumbs" class="trace"><div class="wrap">'.PHP_EOL
			.$breadcrumbs.PHP_EOL
			.'</div></div><!--/#breadcrumbs-->'.PHP_EOL;
		return $markup;
	}

	public static function printBreadcrumbs($delimiter='') {
		echo self::getBreadcrumbs($delimiter);
	}

	public static function getYouAreHere($delimiter='') {
		$markup = false;
		$delimiter = $delimiter?$delimiter:self::$youAreHere_delimiter;
		global $INFO, $conf;
		$conf['youarehere'] = true;
		$offset = 2;
		ob_start();
		tpl_youarehere($delimiter);
		$youarehere = ob_get_contents();
		ob_end_clean();
		if(substr_count($youarehere,'</a>')>$offset) {
			$markup = '<div id="youarehere" class="trace"><div class="wrap">'.PHP_EOL
				.$youarehere.PHP_EOL
				.'</div></div><!--/#youarehere-->'.PHP_EOL;
		}
		return $markup;
	}

	public static function printYouAreHere($delimiter='') {
		echo self::getYouAreHere($delimiter);
	}

	public static function getNavigation() {
		$markup = false;
		global $INFO, $conf;
		if(page_exists('nav')){
			$raw = rawWiki('nav');
			if($raw){
				list($garbage,$raw) = explode('<nav>',$raw);
				list($raw,$garbage) = explode('</nav>',$raw);
				$items = array_filter(explode(PHP_EOL,$raw),'trim');
				foreach( $items as $item ) {
					list($item,$attributes) = explode('=',$item);
					$item = trim($item);
					if(page_exists($item)&&auth_quickaclcheck($item)) {
						$link = wl($item);
						list($label,$icon) = explode('|',$attributes);
						$label = trim($label);
						$label = $label&&$label!=$item?$label:p_get_first_heading(trim($item));
						$icon = trim($icon);
						$icon = $icon?"<i class='$icon'></i> ":'';
						$INFO[nav][] = array('id'=>$item,'link'=>$link,'label'=>$label,'icon'=>$icon);
					}
				}
				$markup = self::buildLinks($INFO[nav],array('id'=>'nav','class'=>'nav','role'=>'navigation'));
			}
		}
		return $markup;
	}

	public static function printNavigation() {
		echo self::getNavigation();
	}

	public static function getPageContent() {
		$markup = false;

		if(KabinetImage::$img) {
			$markup = Kabinet::getPageContent();
		} else {
			ob_start();
			tpl_content();
			$markup = ob_get_contents();
			ob_end_clean();

			// nothing by now
		}
		
		return $markup;
	}

	public static function printPageContent() {
		echo self::getPageContent();
	}

	public static function getPageInfo() {
		$markup = false;
		global $INFO,$conf;
		$page = (object) array(
			'update_page' => $INFO['id'],
			'update_time' => $INFO['lastmod'],
			'update_user' => $INFO['editor'],
		);
		$meta = (object) array(
			'update_page' => $INFO['meta']['last_change']['id'],
			'update_time' => $INFO['meta']['last_change']['date'],
			'update_user' => $INFO['meta']['last_change']['user'],
		);
		$markup = sprintf(
			'<p class="pageinfo">Updated at %s by %s</p>',
			date('Y-m-d',$page->update_time),
			userlink($page->update_user)
		);
		return $markup;
	}

	public static function printPageInfo() {
		echo self::getPageInfo();
	}

	public static function getFooter() {
		$markup = false;
		global $INFO,$conf;
		if(page_exists('footer')){
			$raw = rawWiki('footer');
			if($raw){
				list($garbage,$raw) = explode('<footer>',$raw);
				list($raw,$garbage) = explode('</footer>',$raw);
				$items = array_filter(explode(PHP_EOL,$raw),'trim');
				foreach( $items as $item ) {
					list($item,$attributes) = explode('=',$item);
					list($link,$label,$icon) = explode('|',$attributes);
					$itme = trim($item);
					$link = trim($link);
					$label = trim($label);
					$icon = trim($icon);
					$icon = $icon?"<i class='$icon'></i> ":'';
					$INFO[footer][] = array('id'=>$item,'link'=>$link,'label'=>$label,'icon'=>$icon);
				}
				$markup = self::buildLinks($INFO[footer],array('id'=>'footer','class'=>'nav','role'=>'aside'));
			}
		}
		return $markup;
	}

	public static function printFooter() {
		echo self::getFooter();
	}
}
Kabinet::construct();
?>

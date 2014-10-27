<?php
class Kabinet {

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

		$INFO['hasSidebar'] = page_findnearest($conf['sidebar']);
		$INFO['showSidebar'] = $INFO['hasSidebar']&&($ACT=='show');

		self::$htmlAttributes = array(
			'lang' => $conf['lang'],
			'dir' => $lang['direction'],
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

	function sanitize($string='') {
		$pattern = array(
			' ' => '-',
			':' => '_',
			'&' => '',
			';' => '',
			'.' => '',
			'#' => '',
		);
		$string = htmlentities($string);
		$string = str_replace(array_keys($pattern),array_values($pattern),$string);

		return $string;
	}

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

	function buildLinks($items=array(),$properties=array()) {
		$markup = false;

		if(!empty($items)) {
			$markup = '';
			foreach($items as $item) {
				if(!is_object($item)) {
					$item = (object) $item;
				}
				$item->class = implode(' ',array(
					'item',
					'item-id-'.self::sanitize($item->item),
					'item-type-'.$item->type,
				));
				$item->icon = $item->icon?"<i class='$item->icon'></i> ":'';
				$links[] = sprintf('<li class="%s"><a href="%s">%s%s</a></li>'.PHP_EOL,$item->class,$item->link,$item->icon,$item->label);
			}
			$markup .= '<div'.self::buildAttributes($properties).'>'.PHP_EOL;
			$markup .= '<ul>'.PHP_EOL.implode(PHP_EOL,$links).PHP_EOL.'</ul>'.PHP_EOL;
			$markup .= '</div>'.PHP_EOL;
		}

		return $markup;
	}

	function getLinks($page='',$tag='',$properties=array()) {
		$markup = false;
		global $INFO,$conf;
		static $instance;
		$instance++;

		if(!page_exists($page)) {
			return $markup;
		}

		$tag = $tag?$tag:$page;
		$properties = !empty($properties)?$properties:array('id'=>"navigation-custom-{$tag}-{$instance}",'class'=>"navigation-custom navigation-{$tag}",'role'=>'navigation');
		$INFO['navigation-custom'][$page] = array(
			'tag' => $tag,
			'properties' => $properties,
			'items' => array(),
		);

		$content = rawWiki($page);
		list($garbage,$content) = explode('<code '.$tag.'>',$content);
		list($content,$garbage) = explode('</code>',$content);
		$items = array_filter(explode(PHP_EOL,$content),'trim');

		if(!empty($items)) {
			foreach($items as $index => $item) {
				list($item,$attributes) = explode('=>',$item);
				$item = trim($item);
				$attributes = array_filter(explode('|',$attributes),'trim');
				$object = array(
					'item' => $item,
					'attributes' => $attributes,
					'type' => page_exists($item)?'page':'link',
				);
				foreach($attributes as $attribute) {
					list($key,$value) = explode('=',$attribute);
					$key = trim($key);
					$value = trim($value);
					$object[$key] = $value;
				}
				if($object['type']=='page'&&!auth_quickaclcheck($item)) {
					continue;
				}
				switch($object['type']) {
					case 'page':
						$object['link'] = isset($object['link'])?$object['link']:wl($item);
						$object['label'] = isset($object['label'])?$object['label']:($conf['useheading']==1?p_get_first_heading($item):$item);
						break;
					default: // link
						$object['link'] = isset($object['link'])?$object['link']:'#'.$item;
						$object['label'] = isset($object['label'])?$object['label']:$item;
						break;
				}
				$INFO['navigation-custom'][$page]['items'][$index] = $object; 
			}
			$markup = self::buildLinks($INFO['navigation-custom'][$page]['items'],$properties);
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

		$markup = self::getLinks('nav');

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

			// nothing to do, by now.
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

		$markup = self::getLinks('footer');

		return $markup;
	}

	public static function printFooter() {
		echo self::getFooter();
	}
}
Kabinet::construct();
?>

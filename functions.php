<?php
function j_is_user_logged_in() {
	global $INPUT;
	return $INPUT->server->bool('REMOTE_USER');
}

function j_build_links($items,$attributes) {
	if(!empty($items)&&is_array($items)) {
		foreach($items as $item) {
			if(!is_object($item)) {
				$item = (object) $item;
			}
			$links[] = sprintf('<li class="%s"><a href="%s">%s%s</a></li>'.PHP_EOL,$item->id,$item->link,$item->icon,$item->label);
		}
		$defaults = array('class'=>'nav',);
		$attributes = array_merge($defaults,$attributes);
		foreach($attributes as $key => $value) {
			$value = ($key=='class'&&strpos($value,$defaults['class'])===false?$value.' ':'').$defaults['class'];
			$_attributes[] = "$key = '$value'";
		}
		$markup = '<div '.implode(' ',$_attributes).'>'.PHP_EOL
			.'<ul>'.PHP_EOL
			.implode(PHP_EOL,$links).PHP_EOL
			.'</ul>'.PHP_EOL
			.'</div>'.PHP_EOL;
	}
	if($markup) {
		return $markup;
	} else {
		return false;
	}
}

function j_breadcrumbs($delimiter=' &middot; ') {
	global $INFO, $conf;
	$conf['breadcrumbs'] = true;
	ob_start();
	tpl_breadcrumbs($delimiter);
	$breadcrumbs = ob_get_contents();
	ob_end_clean();
	echo '<div id="breadcrumbs" class="trace"><div class="wrap">'.PHP_EOL
		.$breadcrumbs.PHP_EOL
		.'</div></div><!--/#breadcrumbs-->'.PHP_EOL;
}

function j_youarehere() {
	global $INFO, $conf;
	$conf['youarehere'] = true;
	ob_start();
	tpl_youarehere($delimiter=' &rsaquo; ');
	$youarehere = ob_get_contents();
	ob_end_clean();
	if(substr_count($youarehere,'</a>')>1) {
		echo '<div id="youarehere" class="trace"><div class="wrap">'.PHP_EOL
			.$youarehere.PHP_EOL
			.'</div></div><!--/#youarehere-->'.PHP_EOL;
	}
}

function j_navigation() {
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
			echo j_build_links($INFO[nav],array('id'=>'nav','role'=>'navigation'));
		}
	}
}

function j_pageinfo() {
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
	printf(
		'<p class="pageinfo">Updated at %s by %s</p>',
		date('Y-m-d',$page->update_time),
		userlink($page->update_user)
	);
}

function j_footer() {
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
			echo j_build_links($INFO[footer],array('id'=>'footer','role'=>'navigation'));
		}
	}
}


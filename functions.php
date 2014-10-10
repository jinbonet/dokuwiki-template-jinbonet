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

function j_navigation() {
	global $INFO, $conf;
	if( $INFO[perm] && j_is_user_logged_in() ) {
		echo '<div class="breadcrumbs" role="navigation">' . PHP_EOL;
		echo '<div class="trace">' . PHP_EOL;
		$conf['breadcrumbs'] = true;
		tpl_breadcrumbs();
		echo '</div>' . PHP_EOL;
		/*
		echo '<div class="youarehere">' . PHP_EOL;
		$conf['youarehere'] = true;
		tpl_youarehere();
		echo '</div>' . PHP_EOL;
		*/
		echo '</div>' . PHP_EOL;
	}
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
				}
				$INFO[nav][] = array('id'=>$item,'link'=>$link,'label'=>$label,'icon'=>$icon);
			}
			echo j_build_links($INFO[nav],array('id'=>'nav','role'=>'navigation'));
		}
	}
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

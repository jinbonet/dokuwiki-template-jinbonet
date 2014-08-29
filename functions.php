<?php
$INFO[nav] = array(
	'index' => 'home',
	'work:index' => 'picture',
	'study:index' => 'book',
	'playground:index' => 'leaf',
	'system:index' => 'cog',
	'wiki:index' => 'question-sign',
);
function xx_is_user_logged_in() {
	global $INPUT;
	return $INPUT->server->bool('REMOTE_USER');
}

function xx_navigation() {
	global $INFO, $conf;
	if( $INFO[perm] && xx_is_user_logged_in() ) {
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
	if( $INFO[nav] ){
		$nav = $INFO[nav];
		foreach( $nav as $page => $item ) {
			if( page_exists( $page ) && auth_quickaclcheck( $page ) ) {
				$class = str_replace( ':', '-', $page );
				$items[] = sprintf(
					'<li class="%s"><a href="%s">%s%s</a></li>' . PHP_EOL,
					$class,
					wl( $page ),
					( $icon ? "<i class='glyphicon glyphicon-$icon'></i> " : '' ),
					p_get_first_heading( $page )					
				);
			}
		}
	}
	if( $items ) {
		echo '<div class="nav" role="navigation">' . PHP_EOL;
		echo '<ul>' . PHP_EOL;
		echo implode( '', $items ) . PHP_EOL;
		echo '</ul>' . PHP_EOL;
		echo '</div>' . PHP_EOL;
	}
}

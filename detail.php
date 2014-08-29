<?php
if (!defined('DOKU_INC')) die();
define( 'XX_TITLE', hsc(tpl_img_getTag('IPTC.Headline',$IMG)) . ' &mdash; ' . strip_tags($conf['title']) ); 
require_once dirname(__FILE__).'/tpl_header.php';
if( $ERROR ) {
	echo '<h1>'.$ERROR.'</h1>';
} else {
	echo '<h1>' . nl2br(hsc(tpl_img_getTag('simple.title'))) . '</h1>' . PHP_EOL;
	tpl_img(900,700); /* parameters: maximum width, maximum height (and more) */
	echo '<div class="img_detail">' . PHP_EOL;
        tpl_img_meta();
        echo '</div>' . PHP_EOL;
}
require_once dirname(__FILE__).'/tpl_footer.php';
?>

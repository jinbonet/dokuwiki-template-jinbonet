<?php
if (!defined('DOKU_INC')) die(); /* must be run from within DokuWiki */
define( 'J_TITLE', ( $INFO[id] != $conf[start] ? $INFO[meta][title] . ' &mdash; ' : '' ) . strip_tags($conf['title']) );
require_once dirname(__FILE__).'/tpl_header.php';
tpl_content();
require_once dirname(__FILE__).'/tpl_footer.php';
?>

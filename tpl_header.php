<?php
	if( !defined('DOKU_INC') ) die();
	require_once dirname(__FILE__).'/functions.php';
	header('X-UA-Compatible: IE=edge,chrome=1');
	$hasSidebar = page_findnearest($conf['sidebar']);
	$showSidebar = $hasSidebar && ($ACT=='show');
?><!DOCTYPE html>
<html lang="<?php echo $conf['lang'] ?>" dir="<?php echo $lang['direction'] ?>" class="no-js">
<head>
	<meta charset="utf-8" />
	<title><?php echo J_TITLE; ?></title>
	<meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0" />
	<script>(function(H){H.className=H.className.replace(/\bno-js\b/,'js')})(document.documentElement)</script>
<?php
	echo tpl_favicon(array('favicon', 'mobile'));
	tpl_metaheaders();
?></head>
<body>
<div id="dokuwiki__site">
<div id="dokuwiki__top" class="site <?php echo tpl_classes() . ( $showSidebar ? 'showSidebar' : '' ) . ( $hasSidebar ? 'hasSidebar' : '' ); ?>">

<div id="dokuwiki__header">
	<div class="pad group">
		<div class="headings group">
			<ul class="a11y skip">
				<li><a href="#dokuwiki__content"><?php echo $lang['skip_to_content']; ?></a></li>
			</ul>
			<div class="site-header" role="banner">
				<h1><?php tpl_link( wl(), '<span>'.$conf['title'].'</span>', 'accesskey="h" title="[H]"' ); ?></h1>
<?php if ($conf['tagline']): ?>
				<p class="claim"><?php echo $conf['tagline']; ?></p>
<?php endif ?>
			</div>
		</div><!--/.headings.group-->
		<div class="tools group" role="aside">
<?php if ($conf['useacl']): ?>
			<div id="dokuwiki__usertools">
				<h3 class="a11y"><?php echo $lang['user_tools']; ?></h3>
				<ul>
<?php
	if (!empty($_SERVER['REMOTE_USER'])) {
		echo '<li class="user">';
		tpl_userinfo(); /* 'Logged in as ...' */
		echo '</li>';
	}
	tpl_action('admin', 1, 'li');
	tpl_action('profile', 1, 'li');
	tpl_action('register', 1, 'li');
	tpl_action('login', 1, 'li');
?>
				</ul>
			</div><!--/#dokuwiki__usertools-->
<?php endif ?>
			<div id="dokuwiki__sitetools">
				<h3 class="a11y"><?php echo $lang['site_tools']; ?></h3>
				<?php tpl_searchform(); ?>
				<div class="mobileTools">
<?php
	 tpl_actiondropdown($lang['tools']);
 ?>
				</div>
				<ul>
<?php
	tpl_action('recent', 1, 'li');
	tpl_action('media', 1, 'li');
	tpl_action('index', 1, 'li');
?>
				</ul>
			</div><!--/#dokuwiki__sitetools-->
		</div><!--/.tools.group-->
<?php
	j_navigation();
	html_msgarea();
?>
		<hr class="a11y" />
	</div><!--/.pad.group-->
</div><!--/#dokuwiki__header-->

<div class="wrapper group">

<?php if($showSidebar): ?>
	<div id="dokuwiki__aside" role="aside">
		<div class="pad aside include group">
			<h3 class="toggle"><?php echo $lang['sidebar'] ?></h3>
			<div class="content">
<?php
	tpl_flush();
	tpl_include_page( $conf['sidebar'], 1, 1 );
 ?>
			</div><!--/.content-->
		</div><!--/.pad.aside.include.group-->
	</div><!--/#dokuwiki__aside-->
<?php endif; ?>

	<div id="dokuwiki__content">
		<div class="pad group">
			<!--div class="pageId"><span><?php /* echo hsc($ID); */ ?></span></div-->
			<div class="page group" role="main">
<?php
	tpl_flush();
?>

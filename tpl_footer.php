<?php
	if (!defined('DOKU_INC')) die();
?>
			</div><!--/.page.group-->
			<div class="docInfo">
<?php
	if(Kabinet::isUser() ) {
		Kabinet::printPageInfo();
	}
?>
			</div><!--/.docInfo-->
<?php
	tpl_flush();
?>
		</div><!--/.pad.group-->
	</div><!--/#dokuwiki__content-->

	<hr class="a11y" />

	<div id="dokuwiki__pagetools" role="aside">
		<h3 class="a11y"><?php echo $lang['page_tools']; ?></h3>
		<div class="tools">
			<ul>
<?php
	$data = array(
		'view'  => 'main',
		'items' => array(
			'edit'      => tpl_action('edit',      1, 'li', 1, '<span>', '</span>'),
			'revert'    => tpl_action('revert',    1, 'li', 1, '<span>', '</span>'),
			'revisions' => tpl_action('revisions', 1, 'li', 1, '<span>', '</span>'),
			'backlink'  => tpl_action('backlink',  1, 'li', 1, '<span>', '</span>'),
			'subscribe' => tpl_action('subscribe', 1, 'li', 1, '<span>', '</span>'),
			'top'       => tpl_action('top',       1, 'li', 1, '<span>', '</span>')
		)
	);
	// the page tools can be amended through a custom plugin hook
	$evt = new Doku_Event('TEMPLATE_PAGETOOLS_DISPLAY', $data);
	if( $evt->advise_before() ){
		foreach( $evt->data['items'] as $k => $html ) {
			echo $html;
		}
	}
	$evt->advise_after();
	unset($data);
	unset($evt);
?>
			</ul>
		</div><!--/.tools-->
	</div><!--/#dokuwiki__pagetools-->
</div><!--/.wrapper.group-->

<div id="dokuwiki__footer" role="contentinfo">
	<div class="pad">
<?php Kabinet::printFooter(); ?>
	</div><!--/.pad-->
</div><!--/#dokuwiki__footer-->
</div><!--/#dokuwiki__top-->
</div><!--/#dokuwiki__site-->
<div class="no"><?php tpl_indexerWebBug() /* provide DokuWiki housekeeping, required in all templates */ ?></div>
<div id="screen__mode" class="no"></div><?php /* helper to detect CSS media query in script.js */ ?>
</body>
</html>

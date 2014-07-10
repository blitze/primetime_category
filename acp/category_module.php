<?php
/**
 *
 * @package primetime
 * @copyright (c) 2013 Daniel A. (blitze)
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace primetime\category\acp;

class category_module
{
	var $u_action;

	function main($id, $mode)
	{
		global $phpbb_container, $phpbb_root_path, $phpEx, $template, $user;

		$icon = $phpbb_container->get('primetime.icon_picker');
		$primetime = $phpbb_container->get('primetime');

		$asset_path = $primetime->asset_path;
		$primetime->add_assets(array(
			'js'        => array(
				'//ajax.googleapis.com/ajax/libs/jqueryui/' . JQUI_VERSION . '/jquery-ui.min.js',
				'http://d1n0x3qji82z53.cloudfront.net/src-min-noconflict/ace.js',
				$asset_path . 'ext/primetime/primetime/assets/tree/nestedSortable.js',
				$asset_path . 'ext/primetime/primetime/assets/tree/builder.js',
				$asset_path . 'ext/primetime/category/assets/js/admin.js',
			),
			'css'   => array(
				'//ajax.googleapis.com/ajax/libs/jqueryui/' . JQUI_VERSION . '/themes/base/jquery-ui.css',
				$asset_path . 'ext/primetime/primetime/assets/tree/builder.css',
			)
		));

		$template->assign_vars(array(
			'S_CATEGORIES'	=> true,
			'ICON_PICKER'	=> $icon->picker(),
			'T_PATH'		=> $phpbb_root_path,
			'UA_AJAX_URL'   => "{$phpbb_root_path}app.$phpEx/category/admin/"
		));

		$this->tpl_name = 'acp_category';
		$this->page_title = $user->lang['CATEGORIES'];
	}
}

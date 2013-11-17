<?php
/**
 *
 * @package primetime
 * @copyright (c) 2013 Daniel A. (blitze)
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace primetime\category\acp;

/**
 * @ignore
 */
if (!defined('IN_PHPBB'))
{
	exit;
}

/**
* @package acp
*/
class category_module
{
	var $u_action;

	function main($id, $mode)
	{
        global $phpbb_root_path, $phpEx, $template, $user;

		$template->assign_vars(array(
			'S_CATEGORIES'	=> true,
			'T_PATH'		=> $phpbb_root_path,
			'UA_AJAX_URL'   => "{$phpbb_root_path}app.$phpEx/category/admin/")
		);

		$this->tpl_name = 'acp_category';
		$this->page_title = $user->lang['CATEGORIES'];
	}
}

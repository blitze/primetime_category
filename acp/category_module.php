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

use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Exception\ParseException;

/**
* @package acp
*/
class category_module
{
	var $u_action;
	var $tree_builder;
	var $new_config = array();

	function main($id, $mode)
	{
        global $phpbb_root_path, $phpEx;
		global $db, $phpbb_container, $request, $template, $user;

		$user->add_lang_ext('primetime/category', 'admin');

		$recalc = $request->variable('recalc', false);
		$submit = $request->is_set_post('submit');

		$manager = $phpbb_container->get('primetime.category.manager');
		$primetime = $phpbb_container->get('primetime');

		if ($submit)
		{
			$parent_id = $request->variable('parent_id', 0);
			$bulk_list = $request->variable('add_list', '', true);

			$tree = $manager->string_to_nestedset($bulk_list, array('cat_name' => ''));

			$manager->add_branch($tree, $parent_id);
		}

		if ($recalc === true)
		{
			$manager->recalc_nestedset();
		}

		$sql = $manager->qet_tree_sql();
		$result = $db->sql_query($sql);

		$data = array();
		while ($row = $db->sql_fetchrow($result))
		{
			$data[] = $row;
		}
		$db->sql_freeresult($result);

		$manager->display_list($data, $template);
		$manager->display_options($data, $template);

		$template->assign_vars(array(
			'S_CATEGORIES'	=> true,
			'T_PATH'		=> $phpbb_root_path,
			'U_RECALC_TREE' => $this->u_action . '&amp;recalc=true',
			'UA_AJAX_URL'   => "{$phpbb_root_path}app.$phpEx/category/admin/")
		);

		$this->tpl_name = 'acp_category';		
	}
}

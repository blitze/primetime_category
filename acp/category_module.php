<?php
/**
*
* @package acp
* @version $Id$
* @copyright (c) 2005 phpBB Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

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
class phpbb_ext_primetime_category_acp_category_module
{
	var $u_action;
	var $tree_builder;
	var $new_config = array();

	function main($id, $mode)
	{
		global $db, $phpbb_container, $request, $template, $user;

		$user->add_lang_ext('primetime/category', 'admin');

		$recalc = $request->variable('recalc', false);
		$submit = $request->is_set_post('submit');

		$manager = $phpbb_container->get('primetime.category.manager');

		if ($submit)
		{
			$parent_id = $request->variable('parent_id', 0);
			$bulk_list = $request->variable('add_list', '', true);

			$tree = $manager->string_to_tree($bulk_list, array('cat_name' => ''));

			$manager->add_branch($tree, $parent_id);
		}

		if ($recalc === true)
		{
			$manager->recalc_nested_sets();
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
			'U_RECALC_TREE' => $this->u_action . '&amp;recalc=true',
			'UA_AJAX_URL'   => '../app.php/category/admin/')
		);

		$this->tpl_name = 'acp_category';		
	}
}

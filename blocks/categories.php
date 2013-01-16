<?php
/**
*
* @package phpBB3 Primetime
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

/**
* Categories Block
* @package phpBB Primetime Categories
*/
class phpbb_ext_primetime_category_blocks_categories implements phpbb_ext_primetime_blocks_core_interface
{
	public $title;

	/**
	 * Constructor method
	 *
	 * @param phpbb_request $request Request object
	 * @param phpbb_ext_primetime_category_core_display $tree Tree display object
	 */
	public function __construct(phpbb_request $request, phpbb_db_driver $db, phpbb_template $template, phpbb_ext_primetime_category_core_display $tree)
	{
		$this->request = $request;
		$this->template = $template;
		$this->tree = $tree;
		$this->db = $db;
		$this->title = 'Categories';
	}

	public function config($settings)
	{
		return array_merge(array('ext' => ''), $settings);
	}

	public function edit($settings)
	{
		return "";
	}

	public function display($settings)
	{
		$config = $this->config($settings);

		$sql = $this->tree->qet_tree_sql();
		$result = $this->db->sql_query($sql);

		$data = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$data[] = $row;
		}
		$this->db->sql_freeresult($result);

		$this->template->set_filenames(array(
			'categories' => 'block_categories.html',
		));

		$this->tree->display_list($data, $this->template, 'tree');		
		$content = $this->template->assign_display('categories');
		$this->template->destroy_block_vars('tree');

		return $content;
	}
}

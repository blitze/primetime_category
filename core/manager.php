<?php
/**
*
* @package phpBB3
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
* Manage nested sets
* @package phpBB Primetime
*/
class phpbb_ext_primetime_category_core_manager extends phpbb_ext_primetime_core_includes_tree_builder
{
	public function __construct(phpbb_db_driver $db, phpbb_ext_primetime_core_includes_helper $helper, $table, $pk, $where = '')
	{
		$this->db = $db;
		$this->helper = $helper;
		$this->table = $table;
		$this->pk = $pk;
		$this->where = $where;
	}
}

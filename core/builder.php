<?php
/**
 *
 * @package primetime
 * @copyright (c) 2013 Daniel A. (blitze)
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace primetime\category\core;

/**
 * Manage nested sets
 * @package phpBB Primetime
 */
class builder extends \primetime\primetime\core\tree\builder
{
	/**
	 * Cache
	 * @var \phpbb\cache\service
	 */
	protected $cache;

	/**
	 * Construct
	 *
	 * @param \phpbb\cache\service					$cache			Cache object
	 * @param \phpbb\db\driver\factory				$db             Database connection
	 * @param \primetime\primetime\core\primetime	$primetime		Primetime object
	 * @param string								$table_name		Table name
	 * @param string								$pk				Primary key
	 * @param string								$sql_where		Column restriction
	 */
	public function __construct(\phpbb\cache\driver\driver_interface $cache, \phpbb\db\driver\factory $db, \primetime\primetime\core\primetime $primetime, $table, $pk)
	{
		parent::__construct($db, $primetime, $table, $pk);
		$this->cache = $cache;
	}

	public function on_tree_change($data)
	{
		$row = array_pop($data);
		$this->cache->destroy('pt_block_data_' . $row['cat_id']);
	}
}

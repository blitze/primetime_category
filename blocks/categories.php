<?php
/**
 *
 * @package primetime
 * @copyright (c) 2013 Daniel A. (blitze)
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace primetime\category\blocks;

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
class categories implements \primetime\primetime\core\iblock
{
	/**
	 * Database
	 * @var \phpbb\db\driver\driver
	 */
	protected $db;

	/**
	* Template object
	* @var \phpbb\template\template
	*/
	protected $template;

	/**
	* Tree object
	* @var \primetime\category\core\display
	*/
	protected $user;

	/**
	* Primetime object
	* @var \primetime\primetime\core\primetime
	*/
	protected $primetime;

	/**
	* Constructor
	*
	* @param \phpbb\db\driver\driver				$db             Database connection
	* @param \phpbb\template\template				$template		Template object
	* @param \primetime\category\core\display		$tree			Primetime helper object
	* @param \primetime\primetime\core\primetime	$primetime		Primetime helper object
	*/
	public function __construct(phpbb_db_driver $db, \phpbb\template\template $template, \primetime\category\core\display $tree, \primetime\primetime\core\primetime $primetime)
	{
		$this->primetime = $primetime;
		$this->template = $template;
		$this->tree = $tree;
		$this->db = $db;
	}

	public function config()
	{
		return array(
            'legend1'       => 'Settings',
            'enable_icons'  => array('lang' => 'ENABLE_ICONS', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => false, 'default' => false),
        );
	}

	public function display($settings)
	{
		$sql = $this->tree->qet_tree_sql();
		$result = $this->db->sql_query($sql);

		$data = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$data[] = $row;
		}
		$this->db->sql_freeresult($result);

		$this->tree->display_list($data, $this->template, 'tree');	

		return array(
            'title'     => 'Categories',
            'content'   => 	$this->primetime->render_block('primetime/category', 'blocks_categories.html', 'categories'),
        );
	}
}

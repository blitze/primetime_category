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
class categories  extends \primetime\primetime\core\blocks\driver\block
{
	/**
	 * Database
	 * @var \phpbb\db\driver\driver
	 */
	protected $db;

	/**
	* User object
	* @var \phpbb\user
	*/
	protected $user;

	/**
	* Tree object
	* @var \primetime\category\core\display
	*/
	protected $tree;

	/**
	* Constructor
	*
	* @param \phpbb\db\driver\driver				$db     Database connection
	* @param \phpbb\template\template				$user	User object
	* @param \primetime\category\core\display		$tree	Category tree display object
	*/
	public function __construct(\phpbb\db\driver\driver $db, \phpbb\user $user, \primetime\category\core\display $tree)
	{
		$this->db = $db;
		$this->user = $user;
		$this->tree = $tree;
	}

	public function get_config()
	{
		return array(
            'legend1'       => 'Settings', //$this->user->data['SETTINGS'],
            'enable_icons'  => array('lang' => 'ENABLE_ICONS', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => false, 'default' => 0),
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

		$this->tree->display_list($data, $this->btemplate, 'tree');	

		return array(
            'title'     => 'Categories',
            'content'   => 	$this->render_block('primetime/category', 'block_categories.html', 'categories'),
        );
	}
}

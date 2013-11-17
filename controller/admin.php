<?php
/**
 *
 * @package primetime
 * @copyright (c) 2013 Daniel A. (blitze)
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace primetime\category\controller;

/**
 * @ignore
 */
if (!defined('IN_PHPBB'))
{
	exit;
}

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Exception\ParseException;

/**
*
*/
class admin
{
	/**
	 * Database object
	 * @var \phpbb\db\driver
	 */
	protected $db;

	/**
	 * Request object
	 * @var \phpbb\request\request_interface
	 */
	protected $request;

	/**
	* User object
	* @var \phpbb\user
	*/
	protected $user;

	/**
	* Tree builder object
	* @var \primetime\category\core\builder
	*/
	protected $tree;

	/**
	* Constructor
	*
	* @param \phpbb\db\driver\driver			$db				Database object
	* @param \phpbb\request\request_interface	$request 		Request object
	* @param \phpbb\user                		$user       	User object
	* @param \primetime\category\core\builder	$tree			Tree builder Object
	*/
	public function __construct(\phpbb\db\driver\driver $db, \phpbb\request\request_interface $request, \phpbb\user $user, \primetime\category\core\builder $tree)
	{
		$this->db = $db;
		$this->request = $request;
		$this->user = $user;
		$this->tree = $tree;
	}

	/**
	* Default controller method to be called if no other method is given.
	* In our case, it is accessed when the URL is /example
	*
	* @return Symfony\Component\HttpFoundation\Response A Symfony Response object
	*/
	public function handle($action, $cat_id = 0)
	{
		$this->user->add_lang_ext('primetime/category', 'acp/info_acp_category');

		if ($this->request->is_ajax() === false)
		{
			$this->return_data['errors'] = $this->user->lang['NOT_AUTHORIZED'];
			return new Response(json_encode($this->return_data));
		}

		$errors = array();
		$return = array();

		switch ($action)
		{
			case 'add':
			case 'edit':

				$data = array(
					'cat_id'	=> (int) $cat_id,
					'cat_name'  => $this->request->variable('title', $this->user->lang['CHANGE_ME'], true),
				);

				if ($action == 'edit')
				{
					if ($data['cat_id'])
					{
						$data += $this->tree->get_row($data['cat_id']);
					}
					else
					{
						$errors[] = $this->user->lang['MISSING_CAT_ID'];
					}
				}

				if (!sizeof($errors))
				{
					$data['cat_name'] = ucwords($data['cat_name']);

					$this->tree->save_node($data['cat_id'], $data);

					$return = $this->manager->get_row($data['item_id']);
					$errors += $this->tree->get_errors();
				}

			break;

			case 'add_bulk':

				$parent_id = $this->request->variable('parent_id', 0);
				$bulk_list = $this->request->variable('add_list', '', true);
	
				$tree = $this->manager->string_to_nestedset($bulk_list, array('cat_name' => ''));
				if (sizeof($tree)) {
					$return['items'] = $this->manager->add_branch($tree, $parent_id);
				}
				$errors += $this->manager->get_errors();

			break;

			case 'update':

				$data = array(
					'cat_id'	=> $this->request->variable('cat_id', 0),
					'cat_icon'  => $this->request->variable('icon', ''),
				);

				$errors += $this->tree->save_node($data['cat_id'], $data);

			break;

			case 'save_tree':

				$raw_tree = $this->request->variable('tree', array(0 => array('' => 0)));

				$data = array();
				for ($i = 1, $size = sizeof($raw_tree); $i < $size; $i++)
				{
					$row = $raw_tree[$i];
					$data[$row['item_id']] = array(
						'cat_id'	=> (int) $row['item_id'],
						'parent_id' => (int) $row['parent_id'],
					);
				}

				$this->tree->update_tree($data);

			break;

			case 'get_item':

				$return = $this->tree->get_row($cat_id);

			break;


			case 'rebuild_tree':

				$this->tree->recalc_nestedset();
				
				// no break here

			case 'get_all_items':

				$sql = $this->tree->qet_tree_sql();
				$result = $this->db->sql_query($sql);

				$items = array();
				while ($row = $this->db->sql_fetchrow($result))
				{
					$items[] = $row;
				}
				$this->db->sql_freeresult($result);
				
				$return['items'] = $items;

			break;
		}

		$return['errors'] = join('<br />', $errors);

		$response = new Response(json_encode($return));
		$response->headers->set('Content-Type', 'application/json');

		return $response;
	}
}

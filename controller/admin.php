<?php
/**
 *
 * @package primetime
 * @copyright (c) 2013 Daniel A. (blitze)
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace primetime\category\controller;

use Symfony\Component\HttpFoundation\Response;

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
	 * @param \phpbb\db\driver\factory			$db				Database object
	 * @param \phpbb\request\request_interface	$request 		Request object
	 * @param \phpbb\user                		$user       	User object
	 * @param \primetime\category\core\builder	$tree			Tree builder Object
	 */
	public function __construct(\phpbb\db\driver\factory $db, \phpbb\request\request_interface $request, \phpbb\user $user, \primetime\category\core\builder $tree)
	{
		$this->db = $db;
		$this->request = $request;
		$this->user = $user;
		$this->tree = $tree;
	}

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

				$return = array(
					'cat_name'  => $this->request->variable('cat_name', $this->user->lang['CHANGE_ME'], true),
				);

				if ($action == 'edit' && !$cat_id)
				{
					$errors[] = $this->user->lang['MISSING_CAT_ID'];
				}
				else
				{
					$return['cat_name'] = ucwords($return['cat_name']);

					$this->tree->save_node($cat_id, $return);
					$errors += $this->tree->get_errors();
				}

			break;

			case 'add_bulk':

				$parent_id = $this->request->variable('parent_id', 0);
				$bulk_list = $this->request->variable('add_list', '', true);

				$tree = $this->tree->string_to_nestedset($bulk_list, array('cat_name' => ''));
				if (sizeof($tree)) {
					$return['items'] = $this->tree->add_branch($tree, $parent_id);
				}
				$errors += $this->tree->get_errors();

			break;

			case 'update':

				$return = array(
					'cat_icon'  => $this->request->variable('icon', ''),
				);

				$this->tree->save_node($cat_id, $return);
				$errors += $this->tree->get_errors();

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

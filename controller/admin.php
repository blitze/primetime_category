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

// This is required for all controllers
use Symfony\Component\HttpFoundation\Response;

/**
*
*/
class admin
{
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
	* NOTE: The parameters of this method must match in order and type with
	* the dependencies defined in the services.yml file for this service.
	*
	* @param \phpbb\request\request_interface	$request 		Request object
	* @param \phpbb\user                		$user       	User object
	* @param \primetime\category\core\builder	$tree			Tree builder Object
	*/
	public function __construct(\phpbb\request\request_interface $request, \phpbb\user $user, \primetime\category\core\builder $tree)
	{
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
		$this->user->add_lang_ext('primetime/category', 'admin');

		$errors = array();
		$return = array();

		switch ($action)
		{
			case 'add':
			case 'edit':

				$data = array(
					'cat_id'	=> $this->request->variable('id', 0),
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

				$id	 = 0;
				$title 	= '';

				if (!sizeof($errors))
				{
					$data['cat_name'] = ucwords($data['cat_name']);

					$this->tree->save_node($data['cat_id'], $data);

					$id = $data['cat_id'];
					$title = $data['cat_name'];
					$errors += $this->tree->get_errors();
				}

				$return = array(
					'id'		=> $id,
					'title'		=> $title,
				);

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

			case 'get':

				$return = $this->tree->get_row($cat_id);

			break;
		}

		$return['errors'] = join('<br />', $errors);

		$response = new Response(json_encode($return));
		$response->headers->set('Content-Type', 'application/json');

		return $response;
	}
}

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

class items
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

	public function index($category)
	{
		
	}
}

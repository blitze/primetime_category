<?php
/**
 *
 * @package primetime
 * @copyright (c) 2013 Daniel A. (blitze)
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace primetime\category\core;

class form_field extends \primetime\primetime\core\form\field\base
{
	/** @var \phpbb\db\driver\factory */
	protected $db;

	/** @var \phpbb\request\request_interface */
	protected $request;

	/** @var \primetime\primetime\core\template */
	protected $ptemplate;

	/** @var \primetime\category\core\display */
	protected $tree;

	/** @var string */
	protected $data_table;

	/** @var array */
	protected $cats;

	/**
	 * Constructor
	 *
	 * @param \phpbb\db\driver\factory				$db				Database connection
	 * @param \phpbb\request\request_interface		$request		Request object
	 * @param \primetime\primetime\core\template	$ptemplate		Primetime template object
	 * @param \primetime\category\core\display		$tree			Categories tree object
	 * @param string								$data_table		Categories Data Table
	 */
	public function __construct(\phpbb\db\driver\factory $db, \phpbb\controller\helper $helper, \phpbb\request\request_interface $request, \primetime\primetime\core\template $ptemplate, \primetime\category\core\display $tree, $data_table)
	{
		$this->db = $db;
		$this->helper = $helper;
		$this->request = $request;
		$this->ptemplate = $ptemplate;
		$this->tree = $tree;
		$this->data_table = $data_table;
	}

	/**
	 * @inheritdoc
	 */
	public function display_field($categories, $data = array(), $view = 'detail', $item_id = 0)
	{
		$categories = explode(' | ', $categories);

		$list = array();
		foreach ($categories as $cat_name)
		{
			$u_cat = $this->helper->route('primetime.category.items', array('category' => urlencode($cat_name)));
			$list[] = '<a href="' . $u_cat . '">' . $cat_name . '</a>';
		}

		return join(', ', $list);
	}

	/**
	 * @inheritdoc
	 */
	public function get_field_value($name, $default)
	{
		$categories = $default;
		if ($this->request->server('REQUEST_METHOD') == 'POST')
		{
			$categories =  $this->request->variable($name, array(0 => 0));
		}

		return $categories;
	}

	/**
	 * @inheritdoc
	 */
	public function render_view($name, &$data, $item_id = 0)
	{
		$data += $this->get_default_props();

		$selected	= $this->get_field_value($name, $this->get_item_cats($item_id));
		$this->cats	= $this->tree->get_tree_array();
		$foptions	= $this->tree->display_options($this->cats, 'cat_name', $this->ptemplate, $selected, 'option', '----');

		$cat_names = array();
		foreach ($selected as $cat_id)
		{
			if (isset($this->cats[$cat_id]))
			{
				$cat_names[] = $this->cats[$cat_id]['cat_name'];
			}
		}

		$data['field_name'] 	= $name;
		$data['field_value'] 	= join(' | ', $cat_names);
		$data['field_required']	= ($data['field_required']) ? ' required' : '';
		$data['field_size']		= (sizeof($foptions) < $data['field_size']) ? sizeof($foptions) : $data['field_size'];

		$this->ptemplate->assign_vars(array_change_key_case($data, CASE_UPPER));

		return $this->ptemplate->render_view('primetime/category', "category_field.html", 'category_field');
	}

	/**
	 * @inheritdoc
	 */
	public function get_item_cats($item_id)
	{
		$result = $this->db->sql_query('SELECT cat_id FROM ' . $this->data_table . ' WHERE item_id = ' . (int) $item_id);

		$cat_ids = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$cat_ids[] = $row['cat_id'];
		}
		$this->db->sql_freeresult($result);

		return $cat_ids;
	}

	/**
	 * @inheritdoc
	 */
	public function save_field($field, $categories, $data = array(), $item_id = 0)
	{
		if (empty($categories))
		{
			return;
		}

		$categories = $this->get_field_value($field, $categories);

		$this->db->sql_query('DELETE FROM ' . $this->data_table . " WHERE item_id = " . (int) $item_id);

		foreach ($categories as $cat_id)
		{
			$sql_ary[] = array(
				'cat_id'	=> $cat_id,
				'item_id'	=> $item_id,
			);
		}

		$this->db->sql_multi_insert($this->data_table, $sql_ary);
	}

	/**
	 * @inheritdoc
	 */
	public function get_default_props()
	{
		return array(
			'field_size'		=> 10,
			'field_minlen'		=> 0,
			'field_maxlen'		=> 200,
			'field_value'		=> '',
			'requires_item_id'	=> false,
		);
	}

	/**
	 * @inheritdoc
	 */
	public function get_name()
	{
		return 'category';
	}
}

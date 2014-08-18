<?php
/**
 *
 * @package primetime
 * @copyright (c) 2013 Daniel A. (blitze)
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace primetime\category\migrations\v10x;

class v100 extends \phpbb\db\migration\migration
{
	/**
	 * @inheritdoc
	 */
	static public function depends_on()
	{
		return array('\primetime\primetime\migrations\v10x\v100');
	}

	/**
	 * @inheritdoc
	 */
	public function update_schema()
	{
		return array(
			'add_tables'	=> array(
				$this->table_prefix . 'pt_categories' => array(
					'COLUMNS'        => array(
						'cat_id'			=> array('UINT', null, 'auto_increment'),
						'cat_name'			=> array('VCHAR:55', ''),
						'cat_icon'			=> array('VCHAR', ''),
						'parent_id'			=> array('UINT', 0),
						'left_id'			=> array('UINT', 0),
						'right_id'			=> array('UINT', 0),
						'depth'				=> array('UINT', 0),
					),
					'PRIMARY_KEY'	=> 'cat_id',
					'KEYS'			=> array(
						'cat_id'			=> array('INDEX', 'cat_id'),
					),
				),

				$this->table_prefix . 'pt_categories_data' => array(
					'COLUMNS'        => array(
						'cat_id'			=> array('UINT', 0),
						'item_id'			=> array('UINT', 0),
					),
					'KEYS'			=> array(
						'cat_id'			=> array('INDEX', 'cat_id'),
					),
				),
			),
		);
	}

	public function revert_schema()
	{
		return array(
			'drop_tables'	=> array(
				$this->table_prefix . 'pt_categories',
				$this->table_prefix . 'pt_categories_data',
			),
		);
	}

	public function update_data()
	{
		return array(
			array('module.add', array('acp', 'ACP_PRIMETIME_EXTENSIONS', array(
					'module_basename'	=> '\primetime\category\acp\category_module',
				),
			)),
		);
	}
}

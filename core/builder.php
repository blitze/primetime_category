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
class builder extends \primetime\primetime\core\tree\builder
{
	/**
	* Construct
	*
	* @param \phpbb\db\driver\driver				$db             Database connection
	* @param \primetime\primetime\core\primetime	$primetime		Primetime object
	* @param string									$table_name		Table name
	* @param string									$pk				Primary key
	* @param string									$sql_where		Column restriction
	*/
	public function __construct(\phpbb\db\driver\driver $db, \primetime\primetime\core\primetime $primetime, $table, $pk)
	{
		parent::__construct($db, $primetime, $table, $pk, '');
	}
}

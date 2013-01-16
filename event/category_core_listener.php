<?php

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class phpbb_ext_primetime_category_event_category_core_listener implements EventSubscriberInterface
{
	public function __construct()
	{
		global $phpbb_container;

		// Let's get our table constants out of the way
		$table_prefix = $phpbb_container->getParameter('core.table_prefix');
		define('CATEGORIES_TABLE', $table_prefix . 'categories');
	}

	static public function getSubscribedEvents()
	{
		return array();
	}
}

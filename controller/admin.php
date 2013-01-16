<?php

// This is required for all controllers
use Symfony\Component\HttpFoundation\Response;

class phpbb_ext_primetime_category_controller_admin
{
	/**
	* Constructor
	* NOTE: The parameters of this method must match in order and type with
	* the dependencies defined in the services.yml file for this service.
	*
	* @param phpbb_request $request Request object
	* @param phpbb_user $user User object
	* @param phpbb_template $template Template object
	* @param dbal $db DBAL object
	* @param phpbb_controller_helper $helper Controller helper object
	*/
	public function __construct(phpbb_request $request, phpbb_user $user, phpbb_ext_primetime_core_includes_tree_builder $manager)
	{
		$this->request = $request;
		$this->user = $user;
		$this->manager = $manager;
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
					'cat_name'  => $this->request->variable('title', '', true),
				);

				if (!$data['cat_name'])
				{
					$errors[] = $this->user->lang['MISSING_CAT_NAME'];
				}

				if ($action == 'edit')
				{
					if ($data['cat_id'])
					{
						$data += $this->manager->get_row($data['cat_id']);
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

					$this->manager->save($data['cat_id'], $data);

					$id = $data['cat_id'];
					$title = $data['cat_name'];
					$errors += $this->manager->get_errors();
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

				$errors += $this->manager->save($data['cat_id'], $data);

			break;

			case 'save_tree':

				$raw_tree = $this->request->variable('tree', array(0 => array('' => 0)));

				$tree = array();
				for ($i = 1, $size = sizeof($raw_tree); $i < $size; $i++)
				{
					$row = $raw_tree[$i];
					$tree[$row['item_id']] = array(
						'cat_id'	=> (int) $row['item_id'],
						'parent_id' => (int) $row['parent_id'],
					);
				}

				$this->manager->update_tree($tree);

			break;

			case 'get':

				$return = $this->manager->get_row($cat_id);

			break;
		}

		$return['errors'] = join('<br />', $errors);

		$response = new Response(json_encode($return));
		$response->headers->set('Content-Type', 'application/json');

		return $response;
	}
}

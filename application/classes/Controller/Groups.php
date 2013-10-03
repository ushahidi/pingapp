<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Groups Controller
 * 
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */
class Controller_Groups extends Controller_PingApp {
	
	/**
	 * List all Groups
	 * 
	 * @return void
	 */
	public function action_index()
	{
		$this->template->content = View::factory('pages/groups/index')
			->bind('groups', $groups);

		$groups = $this->user->groups
			->select(array(DB::expr('COUNT(gp.person_id)'), 'people'))
			->join(array('groups_people', 'gp'), 'LEFT')
				->on('gp.group_id', '=', 'group.id')
			->join(array('people', 'p'), 'LEFT')
				->on('p.id', '=', 'gp.person_id')
			->group_by('group.id')
			->order_by('name', 'ASC')
			->find_all();
	}

	/**
	 * Add/Edit Group
	 * 
	 * @return void
	 */
	public function action_edit()
	{
		$this->template->content = View::factory('pages/groups/edit')
			->bind('group', $group)
			->bind('post', $post)
			->bind('errors', $errors)
			->bind('done', $done);

		$group_id = $this->request->param('id', 0);
		$group = ORM::factory('Group')
			->where('id', '=', $group_id)
			->where('user_id', '=', $this->user->id)
			->find();

		if ( ! empty($_POST) )
		{
			$post = $_POST;

			try
			{
				// Save Group
				$group->values($post, array(
					'name',
					));
				$group->check();
				$group->user_id = $this->user->id;
				$group->save();

				// Redirect to prevent repost
				HTTP::redirect('groups/edit/'.$group->id.'?done');
			}
			catch (ORM_Validation_Exception $e)
			{
				$errors = Arr::flatten($e->errors('models'));
			}
		}
		else
		{
			if ( $group->loaded() )
			{
				$post = $group->as_array();
			}

			$done = (isset($_GET['done'])) ? TRUE : FALSE;
		}
	}

	/**
	 * Delete A Group
	 * 
	 * @return void
	 */
	public function action_delete()
	{
		$group_id = $this->request->param('id', 0);

		$group = ORM::factory('Group')
			->where('id', '=', $group_id)
			->where('user_id', '=', $this->user->id)
			->find();

		if ( $group->loaded() )
		{
			$group->delete();
			HTTP::redirect('groups');
		}
		else
		{
			HTTP::redirect('groups');
		}
	}
}
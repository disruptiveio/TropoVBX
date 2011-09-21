<?php

// Routing functions
require('Router.php');

$tropo = new Tropo;

// Get a list of users to transfer to
if (!isset($_GET['user_ids'])) {
	$dial_whom_user_or_group = AppletInstance::getUserGroupPickerValue('dial-whom-user-or-group');
	if (!$dial_whom_user_or_group->users) {
		$tropo->say("Error: Invalid group.");
		$tropo->renderJSON();
		return;
	}
	$users = $dial_whom_user_or_group->users;
} else {
	$users = array();
	if (!empty($_GET['user_ids'])) {
		$userIDs = explode(',', $_GET['user_ids']);
		foreach ($userIDs as $userID) {
			$users[] = VBX_User::get(array('id'=>$userID));
		}
	}
}

// No users to transfer to?
if (empty($users)) {
	$tropo->say("Error: No valid users.");
	$tropo->renderJSON();
	return;
}

// TODO: Select a user to call
$acdRouteType = AppletInstance::getValue('acd-route-type');
$router = new ACDRouter($acdRouteType, $users);

// Call each user. FIFO queue.
while ($userToDial = $router->next())
{
	$userToDial = VBX_User::get($userToDial->id);

	// Call (only) the first active device
	while ($deviceToDial = array_shift($userToDial->devices))
	{
		if (!$deviceToDial->is_active)
			$deviceToDial = null;
		else
			break;
	}

	if ($deviceToDial)
	{
		// TODO: Push screenpop data to user
		// Dial the user
		if (strpos($deviceToDial->value, 'client:') !== false)
			$deviceToDial = VBX_Device::get(array(
				'user_id' => $deviceToDial->user_id,
				'name' => 'Phono',
				'tenant_id' => $deviceToDial->tenant_id
			));
		$tropo->transfer($deviceToDial->value);
		$tropo->on(array('event'=>'continue',
			'next'=>$router->getNextUrl()));
		$tropo->renderJSON();
		return;
	}
}

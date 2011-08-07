<?php
// wcf imports
require_once(WCF_DIR.'lib/system/event/EventListener.class.php');

/**
 * Applies the user online marking to the shoutbox.
 * 
 * @author	Sebastian Oettl
 * @copyright	2009-2011 WCF Solutions <http://www.wcfsolutions.com/index.html>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.wcfsolutions.wcf.shoutbox.entry.userOnlineMarking
 * @subpackage	system.event.listener
 * @category	Community Framework
 */
class ShoutboxEntryFactoryUserOnlineMarkingListener implements EventListener {
	/**
	 * @see EventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName) {
		if (MODULE_USERS_ONLINE == 1) {
			if ($eventName == 'shouldInit') {
				$eventObj->entryList->sqlSelects = (!empty($eventObj->sqlSelects) ? ', ' : '').'user.userOnlineGroupID';
				$eventObj->entryList->sqlJoins .= " LEFT JOIN wcf".WCF_N."_user user ON (user.userID = shoutbox_entry.userID)";
			}
			else if ($eventName == 'didInit') {
				// get cached groups
				WCF::getCache()->addResource('groups', WCF_DIR.'cache/cache.groups.php', WCF_DIR.'lib/system/cache/CacheBuilderGroups.class.php');
				$groups = WCF::getCache()->get('groups', 'groups');
				
				// apply user online markings
				foreach ($eventObj->getEntries() as $entry) {
					if ($entry->userID && isset($groups[$entry->userOnlineGroupID]) && $groups[$entry->userOnlineGroupID]['userOnlineMarking'] != '%s') {
						$entry->usernameStyle = $groups[$entry->userOnlineGroupID]['userOnlineMarking'];
					}
				}
			}
		}
	}
}
?>
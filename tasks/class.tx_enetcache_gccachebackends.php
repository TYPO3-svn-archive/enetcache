<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Christian Kuhn <lolli@schwarzbu.ch>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * Collect garbage of cache backends.
 * This iterates through configured cache framework backends and call the garbage collection
 *
 * @author		Christian Kuhn <lolli@schwarzbu.ch>
 * @package		TYPO3
 * @subpackage	enetcache
 */
class tx_enetcache_gccachebackends extends tx_scheduler_Task {
	/**
	 * @var array Selected backends to do garbage collection for
	 */
	public $selectedBackends = array();

	/**
	 * Function executed from the Scheduler.
	 *
	 * @return	void
	 */
	public function execute() {
		$cacheConfigurations = $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'];

			// Iterate through configured cache configurations and call garbageCollection if
			// backend is within selected backends in additonal fields of task
		foreach ($cacheConfigurations as $cacheName => $cacheConfiguration) {
			$cacheBackend = array($cacheConfiguration['backend']);
			$collectGarbageOfBackend = array_intersect($cacheBackend, $this->selectedBackends);
			if (count($collectGarbageOfBackend) === 1) {
				try {
					$cache = $GLOBALS['typo3CacheManager']->getCache($cacheName);
				} catch (t3lib_cache_exception_NoSuchCache $exception) {
					$GLOBALS['typo3CacheFactory']->create(
						$cacheName,
						$cacheConfiguration['frontend'],
						$cacheConfiguration['backend'],
						$cacheConfiguration['options']
					);
					$cache = $GLOBALS['typo3CacheManager']->getCache($cacheName);
				}
				$cache->collectGarbage();
			}
		}

		$success = TRUE;
		return($success);
	}
} // End of class

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/enetcache/tasks/class.tx_enetcache_gccachebackends.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/enetcache/tasks/class.tx_enetcache_gccachebackends.php']);
}

?>

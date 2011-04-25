<?php
/**
 * jUpgrade
 *
 * @version		$Id: kunena.php 
 * @package		MatWare
 * @subpackage	com_jupgrade
 * @copyright	Copyright 2006 - 2011 Matias Aguire. All rights reserved.
 * @license		GNU General Public License version 2 or later.
 * @author		Matias Aguirre <maguirre@matware.com.ar>
 * @link		http://www.matware.com.ar
 */

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

/**
 * jUpgrade class for Kunena migration
 *
 * This class migrates the Kunena extension
 *
 * @since		1.1.0
 */
class jUpgradeExtensionsKunena extends jUpgrade
{

	/**
	 * @var		string	The name of the source database table.
	 * @since	0.4.4
	 */
	protected $source = '#__kunena_categories';

	/**
	 * @var		string	Extension xml url
	 * @since	1.1.0
	 */
	protected $url = 'http://www.matware.com.ar/extensions/kunena/kunena.xml';

	/**
	 * Get the mapping of the old usergroups to the new usergroup id's.
	 *
	 * @return	array	An array with keys of the old id's and values being the new id's.
	 * @since	1.1.0
	 */
	public static function getUsergroupIdMap()
	{
		$map = array(
			// Old	=> // New
			28		=> 1,	// USERS
			29		=> 1,	// Public Frontend
			18		=> 2,	// Registered
			19		=> 3,	// Author
			20		=> 4,	// Editor
			21		=> 5,	// Publisher
			30		=> 1,	// Public Backend
			23		=> 6,	// Manager
			24		=> 7,	// Administrator
			25		=> 8,	// Super Administrator
		);

		return $map;
	}

	/**
	 * Get the raw data for this part of the upgrade.
	 *
	 * @return	array
	 * @since	1.1.0
	 * @throws	Exception
	 */
	protected function &getSourceData() {

		// Set up the mapping table for the old groups to the new groups.
		$map = self::getUsergroupIdMap();

		$rows = parent::getSourceData(
			'*',
			null,
			null
		);

		// Do some custom post processing on the list.
		foreach ($rows as &$row)
		{
			// 
			if (isset($row['accesstype']) || $row['accesstype'] == 'none' ) {
				if ($row['admin_access'] != 0) {
					$row['admin_access'] = $map[$row['admin_access']];
				}
				if ($row['pub_access'] != 0) {
					$row['pub_access'] = $map[$row['pub_access']];
				}
			}


		}

		return $rows;
	}

	/**
	 * The public entry point for the class.
	 *
	 * @return	boolean
	 * @since	1.1.0
	 */
	public function migrateExtensionDataHook()
	{
		// Truncate the table for better debug
		$clean	= $this->cleanDestinationData();

		try
		{
			$this->setDestinationData();
		}
		catch (Exception $e)
		{
			echo JError::raiseError(500, $e->getMessage());

			return false;
		}

		return true;
	}
}

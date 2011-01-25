<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Georg Ringer <typo3@ringerge.org>
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
 * Controller of the widget view helper
 *
 * @package TYPO3
 * @subpackage tx_news2
 * @version $Id$
 */
class Tx_News2_ViewHelpers_Widget_Controller_YearMenuController extends Tx_Fluid_Core_Widget_AbstractWidgetController {

	/**
	 * @var array
	 */
	protected $configuration = array('itemsPerPage' => 10, 'insertAbove' => FALSE, 'insertBelow' => TRUE);

	/**
	 * @var Tx_Extbase_Persistence_QueryResultInterface
	 */
	protected $objects;

	/**
	 * @var integer
	 */
	protected $currentYear = '2010';

	/**
	 * @return void
	 */
	public function initializeAction() {
		$this->objects = $this->widgetConfiguration['objects'];
		$this->configuration = t3lib_div::array_merge_recursive_overrule($this->configuration, $this->widgetConfiguration['configuration'], TRUE);

	}

	/**
	 * @param integer $currentPage
	 * @return void
	 */
	public function indexAction($currentPage = 1) {


		$modifiedObjects = $this->buildMenu();

		$this->view->assign('contentArguments', array(
			$this->widgetConfiguration['as'] => $modifiedObjects
		));
		$this->view->assign('configuration', $this->configuration);
//		$this->view->assign('pagination', $this->buildPagination());
	}
	
	protected function buildMenu() {
		$menu = array();
		
		$query = $this->objects->getQuery();
		
		$oldConstraints = $query->getConstraint();

		for($i=1;$i<=12;$i++) {
			$startMonth = mktime(0, 0, 1, $i, 0, $this->currentYear);
			$endMonth = mktime(0, 0, 0, $i+1, 0, $this->currentYear);
			$constraints = $query->logicalAnd(
				$query->greaterThanOrEqual('datetime', 	$startMonth),
				$query->lessThanOrEqual('datetime', 	$endMonth)
			);


			if (is_null($oldConstraints)) {
				$query->matching($constraints);
			} else {
				$query->matching($query->logicalAnd(array($oldConstraints, $constraints)));
			}
			
			
			$menu[$i] = $query->execute()->count();		
		}
		
		return $menu;
	}

}

?>
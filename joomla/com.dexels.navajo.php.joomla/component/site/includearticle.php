<?php
/**
 * @version		mod_placehere alpha
 * @package		Joomla
 * @copyright	Copyright (C) 2007 Eike Pierstorff eike@diebesteallerzeiten.de
 * @license		GNU/GPL, see LICENSE.php
 *
 * File last changed 10/30/07
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// Include the syndicate functions only once
//require_once (dirname(__FILE__).DS.'helper.php');
//require_once (dirname(__FILE__).DS.'helperhtml.php');

require_once (JPATH_SITE.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php');

class ArticleLister
{
	function getList($params,$articleid)
	{
		global $mainframe;
		JPluginHelper::importPlugin('content');

		// $dispatcher	   =& JEventDispatcher::getInstance();
		$dispatcher	   =& JDispatcher::getInstance();
		$db			=& JFactory::getDBO();
		$user		=& JFactory::getUser();
		$show_front	= 1;
		$aid		= $user->get('aid', 0);
		$count		= false;
		// Id of currently displayed article
//		$curid		= JRequest::getVar('id', 0, '', 'int');
		$hide_current	=false;
		$contentConfig = &JComponentHelper::getParams( 'com_content' );
		$access		= !$contentConfig->get('shownoauth');

		$nullDate	= $db->getNullDate();

		// $now		= date('Y-m-d H:i:s', time());
		// 18/11/07 Correct date routine as suggested by eathaiku
		// http://diebesteallerzeiten.de/blog/module-for-15-alpha/#comment-1275
		$date = new JDate();
		$now = $date->toMySQL();

		$where		= 'a.state = 1'
		. ' AND ( a.publish_up = '.$db->Quote($nullDate).' OR a.publish_up <= '.$db->Quote($now).' )'
		. ' AND ( a.publish_down = '.$db->Quote($nullDate).' OR a.publish_down >= '.$db->Quote($now).' )'
		;

		//if(	$hide_current ) {
		$where .= " AND a.id = '" . $articleid . "' ";
		//	}

		$ordering		= 'a.created DESC';

		$Condition = ' AND (a.id=' . $articleid . ')';

		// Content Items only
		$query = 'SELECT a.*, ' .
			' CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug,'.
			' CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(":", cc.id, cc.alias) ELSE cc.id END as catslug'.
			' , cc.id as catid, cc.description as category ' .
			' , s.id as sectionid, s.description as section' .			
			' FROM #__content AS a' .
		($show_front == '0' ? ' LEFT JOIN #__content_frontpage AS f ON f.content_id = a.id' : '') .
			' INNER JOIN #__categories AS cc ON cc.id = a.catid' .
			' INNER JOIN #__sections AS s ON s.id = a.sectionid' .
			' WHERE '. $where .' AND s.id > 0' .
		($access ? ' AND a.access <= ' .(int) $aid. ' AND cc.access <= ' .(int) $aid. ' AND s.access <= ' .(int) $aid : '').

		($show_front == '0' ? ' AND f.content_id IS NULL ' : '').
			' AND s.published = 1' .
			' AND cc.published = 1' .
			' ORDER BY '. $ordering;

		if($count) {
		 $db->setQuery($query, 0, $count);
		} else {
		 $db->setQuery($query);
		}
		//		echo $query;
		$rows = $db->loadObjectList();
		$i = 0;
	//	echo "Rowcount: ".count($rows);
		if(count($rows) == 0) {
			echo "Article not found.<br/>\n";
			return;
		}
		//		for($i=0;$i<count($rows);$i++) {
		$rows[$i]->text = $rows[$i]->introtext . $rows[$i]->fulltext;

		$rows[$i]->event = new stdClass();
		$results = $dispatcher->trigger('onPrepareContent', array (& $rows[$i], & $params));
		$results = $dispatcher->trigger('onAfterDisplayTitle', array ($rows[$i], &$params));
		$row->event->afterDisplayTitle = trim(implode("\n", $results));
				 echo trim(implode("\n", $results));
				 $results = $dispatcher->trigger('onBeforeDisplayContent', array (& $rows[$i], & $params));
				 $rows[$i]->event->beforeDisplayContent = trim(implode("\n", $results));
				 $results = $dispatcher->trigger('onAfterDisplayContent', array (& $rows[$i], & $params));
				 $rows[$i]->event->afterDisplayContent = trim(implode("\n", $results));
			
			

		//	}					 -
		return $rows;
	}
}
class TableArray
{
	function buildTablefromArray($rows,$params) {
		// ****************** Output **********************/
		 
	 if(count($rows)<1) { return; }

	 $moduleclass_sfx			= "";
	 $leading						=	1;
	 $num_of_cols					= 1;
	 $w										= false;
	 $width = "";
	 if($w) {
	  $width = ' width="' . $w . '"';
	 }

	 // print_r($rows);
	 echo '<div class="mod_placehere' . $moduleclass_sfx . '">';
	 echo '<table ' . $width . '>';
	 for($i=0;$i<$leading;$i++)  {
	  if($rows[$i]) {
	   echo '<tr>';
	   echo '<td valign="top" colspan="' . $num_of_cols . '" class="mod_placehere_leading">' . $rows[$i]->text . '</div>';
	   echo '</tr>';
	  }
	 }
	 // following paragraphs
	 // How many table rows ?
	 $num_of_trs = count($rows);
	 $width = 100/$num_of_cols;
	  
	 for($i=$leading;$i<=$num_of_trs;$i++) {
	  echo '<tr>';
	  for($z=0;$z<$num_of_cols;$z++) {
	  	if(isset($rows[$i])) {
	  		echo '<td valign="top" width="' . $width . '%" class="mod_placehere_following">' . $rows[$i]->text . '</td>';
	  	} else {
	  		echo '<td width="' . $width . '%" class="mod_placehere_following">&nbsp;</td>';
	  	}
	  	$i++;
	  }
	  echo '</tr>';
	  $i--;
	 }
	 echo '</table>';
	 echo '</div>';
	 // ****************** Output **********************/

	}


}
$params = $mainframe->getParams();

//print_r($_REQUEST);

$submitted = $_REQUEST['direction'];
//echo 'AA: '.$submitted;
//echo 'BB: '.$_REQUEST[$submitted.':id'];

$articleid =$_REQUEST[$submitted.':id'];
$list = ArticleLister::getList($params,$articleid);
//print_r($list);
//require(JModuleHelper::getLayoutPath('mod_placehere'));
TableArray::buildTablefromArray($list,$params);





<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2002-2004 Kasper Skårhøj (kasperYYYY@typo3.com)
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
 * Sub function for list views for the plugin 'Extension Manager Frontend'
 * for the 'extrep_mgm' extension.
 *
 * @author		Kasper Skårhøj  <kasperYYYY@typo3.com>
 * @co-author	Robert Lemke <robert@typo3.org>
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   59: class tx_extrepmgm_listviews extends tx_extrepmgm_pi1
 *   66:     function main()
 *  121:     function listMode_multi()
 *  285:     function listMode_categories()
 *  411:     function renderExtensionRow($rec,$crtime=0)
 *  480:     function linkDocPage($str,$eUid)
 *  497:     function renderExtensionhead()
 *  517:     function listMode_fullList()
 *  657:     function listMode_myList()
 *  844:     function updateFeUserSelection()
 *  869:     function makeTableRowForFullList($rec,$listOnlySel)
 *  943:     function makeTableHRowForFullList()
 *
 *              SECTION: User listing
 *  984:     function listUsers()
 *
 * TOTAL FUNCTIONS: 12
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */

require_once(t3lib_extMgm::extPath('extrep_mgm').'pi1/class.tx_extrepmgm_pi1.php');

class tx_extrepmgm_listviews extends tx_extrepmgm_pi1 {

	/**
	 * Listing the extensions in repository. Basically this is managing the mode menu in the top and calling other methods for the rendering.
	 *
	 * @return	[type]		...
	 */
	function main()	{
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();		// Loading the LOCAL_LANG values
		$this->pi_USER_INT_obj=1;	// Configuring so caching is not expected. This value means that no cHash params are ever set.
		$lConf = $this->conf['listView.'];	// Local settings for the listView function

		$items=array(
			'1'=> $this->pi_getLL('list_mode_1','1'),
			'2'=> $this->pi_getLL('list_mode_2','2'),
			'3'=> $this->pi_getLL('list_mode_3','3'),
			'4'=> $this->pi_getLL('list_mode_4','4'),
			'7'=> $this->pi_getLL('list_mode_7','7'),
			'5'=> $this->pi_getLL('list_mode_5','5'),
			'6'=> 'My extension keys'
		);

		if (!$GLOBALS['TSFE']->loginUser)	{
			unset($items['6']);
		}


		if (!isset($this->piVars['mode']))	$this->piVars['mode']=1;

		$fullTable='';	// Clear var;
		$fullTable.=$this->pi_list_modeSelector($items);


		$this->pi_linkTP('|',Array($this->prefixId=>array('mode'=>99)));
		$fullTable.='<p><form action="'.$this->cObj->lastTypoLinkUrl.'" style="margin: 0px 0px 0px 0px;" method="POST">
			<input type="text" name="'.$this->prefixId.'[DATA][sword]" value="'.htmlspecialchars($this->piVars["DATA"]["sword"]).'" title="'.htmlspecialchars('Enter extension key, uid or search words.').'"><input type="submit" name="-" value="Search">
			</form></p>';

		switch($this->piVars['mode'])	{
			case 2:
				$fullTable.=$this->listMode_categories();
			break;
			case 5:
				$fullTable.=$this->listMode_fullList();
			break;
			case 6:
				$fullTable.=$this->listMode_myList();
			break;
			default:
				$fullTable.=$this->listMode_multi();
			break;
		}
			// Returns the content from the plugin.
		return $fullTable;
	}

	/**
	 * Listing extensions by update/new property.
	 *
	 * @return	[type]		...
	 */
	function listMode_multi()	{
		switch($this->piVars['mode'])	{
			case 3:
				$content.='<h3>Popular by download</h3>';
				$content.='<p>Showing the 20 most popular extension measured by download numbers.</p>';
				if (!$this->piVars['alltime'])	{
					$content.='<p>Showing <strong>all time downloads</strong>. '.$this->pi_linkTP_keepPIvars('Show downloads of most recent version.',array('alltime'=>1)).'</p>';
				} else {
					$content.='<p>Showing downloads of <strong>most recent version</strong>. '.$this->pi_linkTP_keepPIvars('Show all time downloads.',array('alltime'=>'')).'</p>';
				}
				$query = 'SELECT tx_extrep_keytable.uid, max(tx_extrep_repository.download_counter) AS dlcounter FROM tx_extrep_keytable,tx_extrep_repository WHERE
							tx_extrep_keytable.uid=tx_extrep_repository.extension_uid
							AND tx_extrep_repository.emconf_private=0 '.
							$this->cObj->enableFields('tx_extrep_keytable').
							$this->cObj->enableFields('tx_extrep_repository').'
							GROUP BY tx_extrep_keytable.uid
							ORDER BY '.(!$this->piVars['alltime']?'tx_extrep_keytable.download_counter':'dlcounter').' DESC
							LIMIT 20
							';
			break;
			case 4:
				$content.='<h3>Listing reviewed extensions</h3>';
				$content.='<p>Extensions may be reviewed by authorized mentors who will rate the extension in accord with this scheme:</p>';

				$listItems=array();
				reset($this->reviewStates);
				while(list($sKey,$sName)=each($this->reviewStates))	{
					if ($sKey > 0)	{
						$listItems[]='<tr>
							<td>'.$this->pi_linkTP_keepPIvars('<img src="'.t3lib_extMgm::siteRelPath('extrep_mgm').'res/'.$sName[2].'" width=50 height=50 border=0 hspace=10 align="absmiddle">',array('review'=>$sKey)).
							'</td><td><p><strong>'.$this->pi_linkTP_keepPIvars($sName[0],array('review'=>$sKey)).'</strong><br>'.$sName[1].'</p></td></tr>';
					}
				}
				$content.='<table '.$this->conf['listExt.']['tableParams_reviewSel'].'>'.implode('',$listItems).'</table>';

				if ($this->piVars['review'])	{
					$content.='<p>&nbsp;</p>
								<p>Displaying extensions with review "'.$this->reviewStates[$this->piVars['review']][0].'"</p>';
					$query = 'SELECT tx_extrep_keytable.uid FROM tx_extrep_keytable,tx_extrep_repository WHERE
								tx_extrep_keytable.uid=tx_extrep_repository.extension_uid
								AND tx_extrep_repository.emconf_private=0 '.
								$this->cObj->enableFields('tx_extrep_keytable').
								$this->cObj->enableFields('tx_extrep_repository').'
								AND tx_extrep_keytable.tx_extrepmgm_appr_flag=0
								AND ABS(tx_extrep_keytable.tx_extrepmgm_cache_review)='.intval($this->piVars['review']).'
								GROUP BY tx_extrep_keytable.uid
								LIMIT 2000';
		#			debug(array($query));
				}
			break;
			case 7:
				$content.='<h3>Listing extensions by their development state</h3>';
				$content.='<p>Extensions are marked by these development states. Please select one:</p>';

				$listItems=array();
				reset($this->states);
				while(list($sKey,$sName)=each($this->states))	{
					$listItems[]='<li>'.$this->pi_linkTP_keepPIvars('<img src="'.t3lib_extMgm::siteRelPath('extrep_mgm').'res/state_'.$sKey.'.gif" width="109" height="17" style="margin-right: 5px; vertical-align: top;" border="0" alt="">',array('state'=>$sKey)).htmlspecialchars($this->statesDescr[$sKey]).'<br /></li>';
				}
				$listItems[]='<li>'.$this->pi_linkTP_keepPIvars('<img src="'.t3lib_extMgm::siteRelPath('extrep_mgm').'res/state_na.gif" width="109" height="17" border=0>',array('state'=>'_nostate')).'</li>';
				$content.='<ul>'.implode('',$listItems).'</ul>';

				if ($this->piVars['state'])	{
					$content.='<p>Displaying extensions with state "'.($this->states[$this->piVars['state']]?$this->states[$this->piVars['state']]:"<em>Not available</em>").'"</p>';
					$query = 'SELECT tx_extrep_keytable.uid FROM tx_extrep_keytable,tx_extrep_repository WHERE
								tx_extrep_keytable.uid=tx_extrep_repository.extension_uid
								AND tx_extrep_repository.emconf_private=0
								'.
								$this->cObj->enableFields('tx_extrep_keytable').
								$this->cObj->enableFields('tx_extrep_repository').
								($this->states[$this->piVars['state']] ?
									' AND tx_extrep_keytable.tx_extrepmgm_cache_state="'.addslashes($this->piVars['state']).'"' :
									' AND tx_extrep_keytable.tx_extrepmgm_cache_state NOT IN ("'.implode('","',array_keys($this->states)).'")').'
								GROUP BY tx_extrep_keytable.uid
								LIMIT 2000
								';
					#debug(array($query));
				}
			break;
			case 99:
				$content.='<h3>Search for extensions</h3>';
				if ($this->piVars['DATA']['sword'])	{
					$content.='<p>Search for <em>"'.htmlspecialchars($this->piVars['DATA']['sword']).'"</em> in the extension repository.</p>';
					$query = 'SELECT tx_extrep_keytable.uid FROM tx_extrep_keytable,tx_extrep_repository WHERE
								tx_extrep_keytable.uid=tx_extrep_repository.extension_uid
								AND tx_extrep_repository.emconf_private=0
								'.
								$this->cObj->enableFields('tx_extrep_keytable').
								$this->cObj->enableFields('tx_extrep_repository').
								$this->cObj->searchWhere($this->piVars['DATA']['sword'],'title,description,extension_key_modules,extension_key,uid','tx_extrep_keytable').
								'
								GROUP BY tx_extrep_keytable.uid
								LIMIT 2000
								';
#					debug(array($query));
#					extension_key_modules
				} else {
					$query='';
					$content.='<p>Enter an extension key or search word in the field above.</p>';
				}
			break;
			default:
				$content.='<h3>New and updated extensions</h3>';
				$content.='<p>These extensions has been added or updated with a main- or subversion the last 20 days. <br />(Development-version updates are not listed here).</p>';

				$query = 'SELECT tx_extrep_keytable.uid,tx_extrep_keytable.title, MAX(tx_extrep_repository.crdate) AS the_cr_time FROM tx_extrep_keytable,tx_extrep_repository WHERE
								tx_extrep_keytable.uid=tx_extrep_repository.extension_uid
								AND tx_extrep_repository.emconf_private=0
								AND tx_extrep_keytable.members_only=0
								AND tx_extrep_repository.version_dev=0
								'.
								$this->cObj->enableFields('tx_extrep_keytable').
								$this->cObj->enableFields('tx_extrep_repository').
								'
								AND tx_extrep_repository.crdate > '.(time()-20*24*3600).'
								GROUP BY tx_extrep_keytable.uid
								ORDER BY the_cr_time DESC
								LIMIT 20
								';
			break;
		}

#$pt1=t3lib_div::milliseconds();
		if ($query)	{
#debug(array($query));
			$res=mysql(TYPO3_db,$query);
			echo mysql_error();
			$uidL=array();
			$items=array();
			while($r=mysql_fetch_assoc($res))	{
	#			debug(array($r['title'],date('d-m-y H:i',$r['the_cr_time'])));
				$uidL[]=$r['uid'];
				$items[$r['uid']]='';
			}
#debug($uidL);
#debug(t3lib_div::milliseconds()-$pt1);
#$pt1=t3lib_div::milliseconds();

			if (count($uidL))	{
				$array = $this->currentListing(0,1,0,'',count($uidL)?' AND uid IN ('.implode(',',$uidL).')':'',',title,description,tx_extrepmgm_appr_flag,tx_extrepmgm_cache_infoarray,tx_extrepmgm_cache_oodoc,tx_extrepmgm_documentation,tx_extrepmgm_nodoc_flag');
#debug(t3lib_div::milliseconds()-$pt1);

				$feUserData = unserialize($GLOBALS['TSFE']->fe_user->user['tx_extrepmgm_selext']);
				$this->ext_feUserSelection = is_array($feUserData['extSelection']) ? array_keys($feUserData['extSelection']) : array();

				reset($array);
				while(list(,$rec)=each($array))	{
					$items[$rec['extension_uid']]=$this->renderExtensionRow($rec,1);
				}

				$content.='<table '.$this->conf['listExt.']['tableParams'].$this->pi_classParam('lTbl').'>
					'.implode('',$items).$this->renderExtensionHead().'
					</table>';
			}
		}

		return '<DIV'.$this->pi_classParam('extList').'>'.$content.'</DIV>';
	}

	/**
	 * Listing extensions by category
	 *
	 * @return	[type]		...
	 */
	function listMode_categories()	{

			// Displaying the category table:
		if (!strcmp($this->piVars['display_cat'],''))	{

				// Selecting categories and displaying category table:
			$query = 'SELECT * FROM tx_extrepmgm_extgroup WHERE pid='.intval($this->dbPageId).
						$this->cObj->enableFields('tx_extrepmgm_extgroup').
						' ORDER BY title';

				// Select all available:
			$array = $this->currentListing(0,1,0,$q);
			reset($array);
			$uidL=array();
			while(list(,$rec)=each($array))	{
				$uidL[]=$rec['extension_uid'];
			}
			$addWhere = count($uidL)?' AND tx_extrep_keytable.uid IN ('.implode(',',$uidL).')' : '';

				// Make Q:
			$res = mysql(TYPO3_db,$query);
			$numRows=ceil((mysql_num_rows($res)+1)/2);
			$col=array(array(),array());
			$c=0;
			while($row=mysql_fetch_assoc($res))	{
				$query = 'SELECT count(*) FROM tx_extrep_keytable_tx_extrepmgm_group_mm,tx_extrep_keytable WHERE'.
							' tx_extrep_keytable_tx_extrepmgm_group_mm.uid_local=tx_extrep_keytable.uid'.
							" AND tx_extrep_keytable_tx_extrepmgm_group_mm.uid_foreign=".intval($row['uid']).
							$addWhere.
							$this->cObj->enableFields('tx_extrep_keytable');
				$res2 = mysql(TYPO3_db,$query);
				list($count) = mysql_fetch_row($res2);

				$col[floor($c/$numRows)][]='
					<tr><td'.$this->pi_classParam("catHead").'>'.$this->pi_linkTP_keepPIvars($row["title"],array("display_cat"=>$row["uid"])).' ('.$count.')</td></tr>
					<tr><td><p>'.htmlspecialchars($row["descr"]).'</p></td></tr>
					';
				$c++;
			}
			$query = 'SELECT count(*) FROM tx_extrep_keytable LEFT JOIN tx_extrep_keytable_tx_extrepmgm_group_mm
						ON tx_extrep_keytable.uid=tx_extrep_keytable_tx_extrepmgm_group_mm.uid_local WHERE'.
						' tx_extrep_keytable_tx_extrepmgm_group_mm.uid_local IS NULL'.
						$addWhere.
						$this->cObj->enableFields('tx_extrep_keytable');
			$res2 = mysql(TYPO3_db,$query);
	#		echo mysql_error();
			list($count) = mysql_fetch_row($res2);
			$col[floor($c/$numRows)][]='
				<tr><td'.$this->pi_classParam('catHead').'><em>'.$this->pi_linkTP_keepPIvars('No category',array('display_cat'=>0)).'</em> ('.$count.')</td></tr>
				<tr><td><p>Extensions which are not assigned to any category.</p></td></tr>
				';

			$catTable=array();
			reset($col);
			while(list($k,$v)=each($col))	{
				$catTable[]='<td valign=top width="'.floor(100/count($col)).'%"><table border=0 cellspacing=0 cellpadding=0 width="100%"'.$this->pi_classParam('catTbl').'>'.implode('',$v).'</table></td>';
			}
			$content.='<h3>Select a category:</h3>';
			$content.='<table border=0 cellspacing=0 cellpadding=0>'.implode('<td><img src="clear.gif" width=10 height=1></td>',$catTable).'</table>';

		} else {	// Displaying a single CATEGORY:

				// Selecting categories for a selector-box:
			$query = 'SELECT * FROM tx_extrepmgm_extgroup WHERE pid='.intval($this->dbPageId).
						$this->cObj->enableFields('tx_extrepmgm_extgroup').
						' ORDER BY title';
			$opt=array();
			$res = mysql(TYPO3_db,$query);
			while($row=mysql_fetch_assoc($res))	{
				$opt[]='<option value="'.htmlspecialchars($GLOBALS['TSFE']->baseUrl.$this->pi_linkTP_keepPIvars_url(array("display_cat"=>$row["uid"]))).'"'.($row["uid"]==$this->piVars["display_cat"]?" SELECTED":"").'>'.htmlspecialchars($row["title"]).'</option>';
			}
			$opt[]='<option value="'.htmlspecialchars($GLOBALS['TSFE']->baseUrl.$this->pi_linkTP_keepPIvars_url(array("display_cat"=>0))).'"'.(!$this->piVars["display_cat"]?" SELECTED":"").'>'.htmlspecialchars("No category").'</option>';


				// Getting and displaying current category:
			$catRec = $this->pi_getRecord('tx_extrepmgm_extgroup',$this->piVars['display_cat']);
			$content.='<h3>'.(is_array($catRec)?$catRec['title']:"Not categorized:").'</h3>';
				// "Menu":
			$content.='<p><form style="margin: 0px 0px 0px 0px;"><select onChange="document.location=this.options[this.selectedIndex].value;">'.implode('',$opt).'</select></form></p>';
			$content.='<p>'.$this->pi_linkTP_keepPIvars('Back to category menu',array('display_cat'=>'')).'</p>';


			if ($this->piVars['display_cat']>0)	{
				$q='SELECT tx_extrep_keytable.uid,tx_extrep_keytable.members_only,tx_extrep_keytable.owner_fe_user,tx_extrep_keytable.download_counter,tx_extrep_keytable.title,tx_extrep_keytable.description,tx_extrep_keytable.tx_extrepmgm_appr_flag, tx_extrep_keytable.tx_extrepmgm_cache_infoarray,tx_extrep_keytable.tx_extrepmgm_cache_oodoc,tx_extrep_keytable.tx_extrepmgm_documentation,tx_extrep_keytable.tx_extrepmgm_nodoc_flag
						FROM tx_extrep_keytable_tx_extrepmgm_group_mm,tx_extrep_keytable
						WHERE tx_extrep_keytable_tx_extrepmgm_group_mm.uid_local=tx_extrep_keytable.uid
								AND tx_extrep_keytable.pid='.intval($this->dbPageId).'
								AND tx_extrep_keytable_tx_extrepmgm_group_mm.uid_foreign='.intval($this->piVars['display_cat']).
						$GLOBALS['TSFE']->sys_page->enableFields('tx_extrep_keytable').'
						ORDER BY tx_extrep_keytable.title';
			} else {
				$q='SELECT tx_extrep_keytable.uid,tx_extrep_keytable.members_only,tx_extrep_keytable.owner_fe_user,tx_extrep_keytable.download_counter,tx_extrep_keytable.title,tx_extrep_keytable.description,tx_extrep_keytable.tx_extrepmgm_appr_flag, tx_extrep_keytable.tx_extrepmgm_cache_infoarray,tx_extrep_keytable.tx_extrepmgm_cache_oodoc,tx_extrep_keytable.tx_extrepmgm_documentation,tx_extrep_keytable.tx_extrepmgm_nodoc_flag
						FROM tx_extrep_keytable LEFT JOIN tx_extrep_keytable_tx_extrepmgm_group_mm
						ON tx_extrep_keytable.uid=tx_extrep_keytable_tx_extrepmgm_group_mm.uid_local
						WHERE tx_extrep_keytable_tx_extrepmgm_group_mm.uid_local IS NULL'.
							' AND tx_extrep_keytable.pid='.intval($this->dbPageId).
						$GLOBALS['TSFE']->sys_page->enableFields('tx_extrep_keytable').'
						ORDER BY tx_extrep_keytable.title';
			}
			$array = $this->currentListing(0,1,0,$q);

			$feUserData = unserialize($GLOBALS['TSFE']->fe_user->user['tx_extrepmgm_selext']);
			$this->ext_feUserSelection = is_array($feUserData['extSelection']) ? array_keys($feUserData['extSelection']) : array();

			$items=array();
			reset($array);
			while(list(,$rec)=each($array))	{
				$items[]=$this->renderExtensionRow($rec);
			}
#			debug(count($items));

			$content.='<table '.$this->conf['listExt.']['tableParams'].$this->pi_classParam('lTbl').'>
				'.implode('',$items).$this->renderExtensionhead().'
				</table>';
		}

		return '<DIV'.$this->pi_classParam('extList').'>'.$content.'</DIV>';
	}

	/**
	 * Render the extension info row for listing of categories, news etc.
	 *
	 * @param	[type]		$rec: ...
	 * @param	[type]		$crtime: ...
	 * @return	[type]		...
	 */
	function renderExtensionRow($rec,$crtime=0)	{
		$infotable='<table '.$this->conf['listExt.']['tableParams_extInfoTbl'].$this->pi_classParam('extInfoTbl').'>
			<tr><td nowrap'.$this->pi_classParam('HCell').'>Author:</td><td nowrap>'.htmlspecialchars($rec['emconf_author']).'</td></tr>
			<tr><td nowrap'.$this->pi_classParam('HCell').'>Tech. Cat:</td><td nowrap>'.$this->categories[$rec['emconf_category']].'</td></tr>
			<tr><td nowrap'.$this->pi_classParam('HCell').'>Version:</td><td nowrap>'.$rec['version'].'</td></tr>
			<tr><td nowrap'.$this->pi_classParam('HCell').'>Downloads:</td><td nowrap>'.$rec['_EXTKEY_ROW']['download_counter'].' / '.$rec['download_counter'].'</td></tr>
			'.($crtime?'
			<tr><td nowrap'.$this->pi_classParam('HCell').'>Updated:</td><td nowrap>'.date($this->conf['listExt.']['updateDateFormat'],$rec['crdate']).'</td></tr>
			<tr><td nowrap'.$this->pi_classParam('HCell').'>Changelog:</td><td>'.htmlspecialchars($rec['upload_comment']).'</td></tr>':'').'
		</table>';

		$sel=0;
		if ($GLOBALS['TSFE']->loginUser && !t3lib_div::inList('owner,member',$rec['_ACCESS']))	{
			$sel = in_array($rec['extension_uid'],$this->ext_feUserSelection);
		}
		$bgcol= t3lib_div::inList('owner,member',$rec['_ACCESS']) ? $this->conf['listExt.']['bgcol.']['own_member'] : (!$sel ?  $this->conf['listExt.']['bgcol.']['selected'] : $this->conf['listExt.']['bgcol.']['default']);

		$review = $this->getIcon_review($rec,$rec['_EXTKEY_ROW']);

		$item='<tr bgcolor="'.$bgcol.'"'.$this->pi_classParam('HRow').'>
				<td>'.$rec['_ICON'].'</td>
				<td colspan=2 valign=top>'.$this->pi_linkTP_keepPIvars($rec['_EXTKEY_ROW']['title'],array('showUid'=>$rec['extension_uid'])).' - <em>'.$rec['extension_key'].'</em></td>
				<td align="right">'.$this->getIcon_state($rec).'</td>
				<td rowspan=3'.$this->pi_classParam('review').'>'.$review.'</td>
			</tr>';
		$item.='<tr>
				<td>&nbsp;</td>
				<td valign=top>'.$infotable.'</td>
				<td colspan=2 valign=top><p'.$this->pi_classParam("descr").'>'.trim(htmlspecialchars($rec["_EXTKEY_ROW"]["description"])).'</p></td>
			</tr>';

#debug($rec['_EXTKEY_ROW']);

			// Documentation:
		$documentationIndex = $rec['_EXTKEY_ROW']['tx_extrepmgm_nodoc_flag']&1 ? '' : '<span style="color:red"><strong>No documentation!</strong></span>';	// default...
		if ($rec['_EXTKEY_ROW']['tx_extrepmgm_cache_oodoc'])	{
			$linkItems=array();
			$docParts = unserialize($rec['_EXTKEY_ROW']['tx_extrepmgm_documentation']);
			if (is_array($docParts['doc_kind']) && count($docParts['doc_kind']))	{
				reset($this->kinds);
				while(list($kk,$label)=each($this->kinds))	{
					if ($docParts['doc_kind'][$kk])	{
						$linkItems[]=$this->linkToDocumentation($label,$rec['_EXTKEY_ROW']['uid'],$docParts['doc_kind'][$kk]);
					}
				}
			}
			if (!count($linkItems))	$linkItems[]=$this->linkToDocumentation('MANUAL',$rec['_EXTKEY_ROW']['uid']);
			if (count($linkItems))	$documentationIndex=implode(' - ',$linkItems);
		}


		$item.='<tr>
				<td>&nbsp;</td>
				<td colspan=3><p'.$this->pi_classParam('doclinks').'>'.$documentationIndex.'</p></td>
			</tr>';
		$item.='<tr>
				<td colspan=5><img src="clear.gif" width=1 height='.intval($this->conf['listExt.']['entryVertDistance']).'></td>
			</tr>';

		return $item;
	}

	/**
	 * NOT USED ????? (RL, 29.10.04)
	 *
	 * @param	[type]		$str: ...
	 * @param	[type]		$eUid: ...
	 * @return	[type]		...
	 */
	function linkDocPage($str,$eUid)	{
		$urlParameters = array($this->prefixId.'[extUid]'=>$eUid);

		$conf=array();
		$conf['useCacheHash']=1;
		$conf['no_cache']=0;
		$conf['parameter']=$this->docPage;
		$conf['additionalParams']=t3lib_div::implodeArrayForUrl('',$urlParameters,'',1);

		return $this->cObj->typoLink($str, $conf);
	}

	/**
	 * Extension head, spacing out the table.
	 *
	 * @return	[type]		...
	 */
	function renderExtensionhead()	{
		$code = '<tr>
			<td><img src="clear.gif" width=16 height=1></td>
			<td><img src="clear.gif" width=200 height=1></td>
			<td><img src="clear.gif" width=150 height=1></td>
			<td><img src="clear.gif" width=50 height=1></td>
			<td><img src="clear.gif" width=50 height=1></td>
		</tr>';
		return $code;
	}





	/**
	 * Listing extensions like in the EM.
	 *
	 * @return	[type]		...
	 */
	function listMode_fullList()	{
		$content='';
		if ($GLOBALS['TSFE']->fe_user->user['uid'])	{
			$content.='<p>Login status: You are authenticated as the user <strong>'.$GLOBALS['TSFE']->fe_user->user['username'].'</strong> ('.$GLOBALS['TSFE']->fe_user->user['name'].')</p>';
		} else {
			$content.='<p>Login status: You are not logged in.</p>';
		}

			// Making menu:
		$opt=array();
		$opt[]='<option value="'.htmlspecialchars($GLOBALS['TSFE']->baseUrl.$this->pi_linkTP_keepPIvars_url(array("orderby"=>""))).'">Technical Category</option>';
		$opt[]='<option value="'.htmlspecialchars($GLOBALS['TSFE']->baseUrl.$this->pi_linkTP_keepPIvars_url(array("orderby"=>"author"))).'"'.($this->piVars["orderby"]=="author"?" SELECTED":"").'>Author</option>';
		$opt[]='<option value="'.htmlspecialchars($GLOBALS['TSFE']->baseUrl.$this->pi_linkTP_keepPIvars_url(array("orderby"=>"state"))).'"'.($this->piVars["orderby"]=="state"?" SELECTED":"").'>State</option>';
		$menu = '<select onChange="document.location=this.options[this.selectedIndex].value;">'.implode("",$opt).'</select>';

			// Making show menu:
		$opt=array();
		$opt[]='<option value="'.htmlspecialchars($GLOBALS['TSFE']->baseUrl.$this->pi_linkTP_keepPIvars_url(array("show"=>""))).'">Details</option>';
		$opt[]='<option value="'.htmlspecialchars($GLOBALS['TSFE']->baseUrl.$this->pi_linkTP_keepPIvars_url(array("show"=>"langdoc"))).'"'.($this->piVars["show"]=="langdoc"?" SELECTED":"").'>Lang./Doc.</option>';
		$menu2 = '<select onChange="document.location=this.options[this.selectedIndex].value;">'.implode("",$opt).'</select>';

		$content.='<p>'.
			($GLOBALS["TSFE"]->fe_user->user["uid"]?'Get own/member/selected extensions only: <input type="checkbox" name="_" value="'.htmlspecialchars($GLOBALS['TSFE']->baseUrl.$this->pi_linkTP_keepPIvars_url(array("own_mem_sel"=>$this->piVars["own_mem_sel"]?"":1))).'" onClick="document.location=this.value;"'.($this->piVars["own_mem_sel"]?" CHECKED":"").'>&nbsp;&nbsp;':'').
			'Order by: '.$menu.
			' Show: '.$menu2.
			'</p>';

			// Show only selected flag:
		$listOnlySel = $this->piVars['own_mem_sel']&&$GLOBALS['TSFE']->fe_user->user['uid'];

			// Getting languages status
		if ($this->piVars['show']=='langdoc')	{
			$this->ext_langInfo = $this->getLanguagesAndTranslators();
		}

			// fe_user selection of extensions:
		$this->updateFeUserSelection();
		$feUserData = unserialize($GLOBALS['TSFE']->fe_user->user['tx_extrepmgm_selext']);
		$this->ext_feUserSelection = is_array($feUserData['extSelection']) ? array_keys($feUserData['extSelection']) : array();
#debug($GLOBALS['TSFE']->fe_user->user['tx_extrepmgm_selext']);

			// Getting extensions in an array
		$array = $this->currentListing(0,1,$listOnlySel,'',"",',tx_extrepmgm_cache_infoarray,owner_fe_user,tx_extrepmgm_cache_oodoc,tx_extrepmgm_nodoc_flag');


			// Sorting them
		$newOrderedArray=array();
		if (!$this->piVars['orderby'])	{
			reset($this->categories);
			while(list($k)=each($this->categories))	{
				$newOrderedArray[$k]=array();
			}
		}
		if ($this->piVars['orderby']=='state')	{
			reset($this->states);
			while(list($k)=each($this->states))	{
				$newOrderedArray[$k]=array();
			}
		}

		reset($array);
		while(list(,$rec)=each($array))	{
			switch($this->piVars['orderby'])	{
				case 'author':
					$newOrderedArray[$rec['emconf_author'].', '.$rec['emconf_author_company']][]=$rec;
				break;
				case 'state':
					$newOrderedArray[$rec['emconf_state']][]=$rec;
				break;
				default:
					$newOrderedArray[$rec['emconf_category']][]=$rec;
				break;
			}
		}

			// Rendering display of them
		$codeLines=0;
		$codeBytes=0;
		$count=0;
		$dataSize=0;


		$cols=$GLOBALS['TSFE']->loginUser?9:8;
		$tRows=array();
		$tRows[]=$this->makeTableHRowForFullList();
		reset($newOrderedArray);
		while(list($k,$array)=each($newOrderedArray))	{
			$tRows[]='<tr><td colspan='.$cols.'>&nbsp;</td></tr>';

			switch($this->piVars['orderby'])	{
				case 'author':
					$tRows[]='<tr><td colspan='.$cols.'><img src="'.t3lib_extMgm::siteRelPath('extrep_mgm').'res/sysf.gif" width="18" height="16" border="0" align="top"><strong>'.$k.'</strong> ('.count($array).')</td></tr>';
				break;
				case 'state':
					$tRows[]='<tr><td colspan='.$cols.'><img src="'.t3lib_extMgm::siteRelPath('extrep_mgm').'res/sysf.gif" width="18" height="16" border="0" align="top"><strong>'.$this->states[$k].'</strong> ('.count($array).')</td></tr>';
				break;
				default:
					$tRows[]='<tr><td colspan='.$cols.'><img src="'.t3lib_extMgm::siteRelPath('extrep_mgm').'res/sysf.gif" width="18" height="16" border="0" align="top"><strong>'.$this->categories[$k].'</strong> ('.count($array).')</td></tr>';
				break;
			}

			reset($array);
			while(list(,$rec)=each($array))	{
				$codeLines+=$rec['codelines'];
				$codeBytes+=$rec['codebytes'];
				$dataSize+=$rec['datasize'];
				$count++;
				$tRows[]=$this->makeTableRowForFullList($rec,$listOnlySel);

					// This is also a one-of event:
#				$this->updateExtKeyCache($rec['extension_uid']);

/*
		// This syncs the download counters of the keytable and current repository record with the stat-table.
		// Most likely this happend ONE time in the history and that was when the added upload-counters were created and had to be populated with the most recent numbers.
				$statRec = $this->getStatThisExtRep('import',$rec);
				$kTableRec=$this->getKeyTableRecord($rec['extension_uid']);
				$this->updateDownloadCounters('tx_extrep_repository',$rec['uid'],$statRec['extension_thisversion']);
				$this->updateDownloadCounters('tx_extrep_keytable',$kTableRec['uid'],$statRec['extension_allversions']);
*/
			}
		}

		$content.='<table '.$this->conf['listExt.']['tableParams_fullList'].$this->pi_classParam('lTbl').'>'.implode('',$tRows).'</table>';

		$content.='<BR><p>'.$count.' Extensions listed - PHP lines: '.$codeLines.' - PHP bytes: '.t3lib_div::formatSize($codeBytes).' - Total Data Size: '.t3lib_div::formatSize($dataSize).'</p>';

			// Adding button to submit the selection of extensions.
		$content = '<form action="'.t3lib_div::getIndpEnv('REQUEST_URI').'" method="POST" style="margin: 0px 0px 0px 0px;">'.$content.
			(!$listOnlySel && $GLOBALS['TSFE']->loginUser ? '<BR><p><strong>Selecting extensions:</strong> With the checkboxes to the left of each extension you can select it for listing in the Extension Manager - thus you don\'t have to receive information about all extensions each time you connect.</p><input type="submit" name="'.$this->prefixId.'[DATA][cmd_setSelection]" value="Set selection">' : '').
			'</form>';

		return '<DIV'.$this->pi_classParam('fullList').'>'.$content.'</DIV>';
	}

	/**
	 * Personal list of extension keys.
	 *
	 * @return	[type]		...
	 */
	function listMode_myList()	{
		if (isset($this->piVars['DATA']['uploadPassword']))	{
			if ($GLOBALS['TSFE']->loginUser)	{
				if (!strcmp(trim($this->piVars['DATA']['uploadPassword']),trim($this->piVars['DATA']['uploadPassword2'])))	{
					if ($this->piVars['DATA']['confirm'])	{
						$query = "UPDATE tx_extrep_keytable SET upload_password='".addslashes(trim($this->piVars['DATA']['uploadPassword']))."' WHERE
							owner_fe_user=".intval($GLOBALS['TSFE']->fe_user->user['uid']).'
							'.$GLOBALS['TSFE']->sys_page->enableFields('tx_extrep_keytable');
#debug($query);
						$res = mysql(TYPO3_db,$query);
						$out='<p>New upload password ('.$this->piVars['DATA']['uploadPassword'].') set for all extensions</p>';
						$out.='<p>'.mysql_affected_rows().' extensions updated.</p>';
					} else $out='<p>Error: You didn\'t check off the confirm check box.</p>';
				} else $out='<p>Error: The two passwords did not match each other!</p>';
			} else $out='<p>Error: You are not logged in as any user.</p>';
			$out.='<p>'.$this->pi_linkTP_keepPIvars(htmlspecialchars("Back to list")).'</p>';
		} else {
			// Extension key comparison:
			if (trim($this->piVars['DATA']['cmp_extkeys']))	{
				$entries=array_unique(t3lib_div::trimExplode(chr(10),$this->piVars['DATA']['cmp_extkeys'],1));
				$entries=array_flip($entries);
#				debug($entries);
			} else $entries=array();



			if (!$this->piVars['orderBy'])	$this->piVars['orderBy']='title';
			$repMode=!intval($this->piVars['myListMode']);

			if ($repMode)	{
				$q='SELECT tx_extrep_keytable.*,tx_extrep_repository.uid AS rep_uid, count(*) AS rep_count, MAX(tx_extrep_repository.last_upload_date) AS rep_latest FROM tx_extrep_keytable LEFT JOIN tx_extrep_repository
						ON tx_extrep_keytable.uid=tx_extrep_repository.extension_uid
						WHERE tx_extrep_keytable.owner_fe_user='.intval($GLOBALS['TSFE']->fe_user->user['uid']).
					' AND tx_extrep_keytable.pid='.$this->dbPageId.
		#			" AND (tx_extrep_repository.pid=".$this->dbPageId.' OR tx_extrep_repository.pid=NULL)'.
					$GLOBALS['TSFE']->sys_page->enableFields('tx_extrep_keytable').
		#			$GLOBALS['TSFE']->sys_page->enableFields('tx_extrep_repository').
					' GROUP BY tx_extrep_keytable.uid
						ORDER BY '.(t3lib_div::inList('title,extension_key,crdate,rep_latest,rep_count,download_counter,upload_password,tx_extrepmgm_cache_state',$this->piVars['orderBy'])?$this->piVars['orderBy']:'title').' '.($this->piVars['desc']?'DESC':'').'
					';
			} else {
				$q='SELECT tx_extrep_keytable.*,tx_extrep_groupmem_mm.uid_local AS rep_uid, count(*) AS rep_count FROM tx_extrep_keytable LEFT JOIN tx_extrep_groupmem_mm
						ON tx_extrep_keytable.uid=tx_extrep_groupmem_mm.uid_local
						WHERE tx_extrep_keytable.owner_fe_user='.intval($GLOBALS['TSFE']->fe_user->user['uid']).
					' AND tx_extrep_keytable.pid='.$this->dbPageId.
					$GLOBALS['TSFE']->sys_page->enableFields('tx_extrep_keytable').
					' GROUP BY tx_extrep_keytable.uid
						ORDER BY '.(t3lib_div::inList('title,extension_key,members_only,rep_count',$this->piVars['orderBy'])?$this->piVars['orderBy']:'title').' '.($this->piVars['desc']?'DESC':'').'
					';
			}




			$items=array();

			if ($repMode)	{
				$items[]='<tr>
					<td nowrap'.$this->pi_classParam('HCell').'>'.$this->pi_linkTP_keepPIvars('Title:',array('orderBy'=>'title','desc'=>$this->piVars['orderBy']=="title"?!$this->piVars['desc']:'')).'</td>
					<td nowrap'.$this->pi_classParam('HCell').'>'.$this->pi_linkTP_keepPIvars('Extension key:',array('orderBy'=>'extension_key','desc'=>$this->piVars['orderBy']=="extension_key"?!$this->piVars['desc']:'')).'</td>
					<td nowrap'.$this->pi_classParam('HCell').'>'.$this->pi_linkTP_keepPIvars('Created:',array('orderBy'=>'crdate','desc'=>$this->piVars['orderBy']=="crdate"?!$this->piVars['desc']:1)).'</td>
					<td nowrap'.$this->pi_classParam('HCell').'>'.$this->pi_linkTP_keepPIvars('Updated:',array('orderBy'=>'rep_latest','desc'=>$this->piVars['orderBy']=="rep_latest"?!$this->piVars['desc']:1)).'</td>
					<td nowrap'.$this->pi_classParam('HCell').'>'.$this->pi_linkTP_keepPIvars('# Upl.',array('orderBy'=>'rep_count','desc'=>$this->piVars['orderBy']=="rep_count"?!$this->piVars['desc']:1)).'</td>
					<td nowrap'.$this->pi_classParam('HCell').'>'.$this->pi_linkTP_keepPIvars('# Dnl.',array('orderBy'=>'download_counter','desc'=>$this->piVars['orderBy']=="download_counter"?!$this->piVars['desc']:1)).'</td>
					<td nowrap'.$this->pi_classParam('HCell').'>'.$this->pi_linkTP_keepPIvars('Passwd. hash:',array('orderBy'=>'upload_password','desc'=>$this->piVars['orderBy']=="upload_password"?!$this->piVars['desc']:'')).'</td>
					<td nowrap'.$this->pi_classParam('HCell').'>'.$this->pi_linkTP_keepPIvars('State:',array('orderBy'=>'tx_extrepmgm_cache_state','desc'=>$this->piVars['orderBy']=="tx_extrepmgm_cache_state"?!$this->piVars['desc']:'')).'</td>
				</tr>';
			} else {
				$items[]='<tr>
					<td nowrap'.$this->pi_classParam('HCell').'>'.$this->pi_linkTP_keepPIvars('Title:',array('orderBy'=>'title','desc'=>$this->piVars['orderBy']=="title"?!$this->piVars['desc']:'')).'</td>
					<td nowrap'.$this->pi_classParam('HCell').'>'.$this->pi_linkTP_keepPIvars('Extension key:',array('orderBy'=>'extension_key','desc'=>$this->piVars['orderBy']=="extension_key"?!$this->piVars['desc']:'')).'</td>
					<td nowrap'.$this->pi_classParam('HCell').'>'.$this->pi_linkTP_keepPIvars('Members-only:',array('orderBy'=>'members_only','desc'=>$this->piVars['orderBy']=="members_only"?!$this->piVars['desc']:1)).'</td>
					<td nowrap'.$this->pi_classParam('HCell').'>'.$this->pi_linkTP_keepPIvars('# Members:',array('orderBy'=>'rep_count','desc'=>$this->piVars['orderBy']=="rep_count"?!$this->piVars['desc']:1)).'</td>
				</tr>';
			}

			$c=0;
			$res = mysql(TYPO3_db,$q);
			while($row=mysql_fetch_assoc($res))	{
				if (isset($entries[$row['extension_key']]))	{
					unset($entries[$row['extension_key']]);
					$params=' style="background-color:#ff9933;"';
				} else $params='';

				if ($repMode)	{
					$items[]='<tr'.($c%2?'':$this->pi_classParam("oddRow")).'>
						<td nowrap>'.$this->pi_linkTP_keepPIvars(htmlspecialchars($row["title"]),array("showUid"=>$row["uid"],"cmd"=>"edit")).'</td>
						<td nowrap'.$params.'>'.($params?"** &nbsp;":"").$row["extension_key"].'</td>
						<td nowrap>'.date("d-m-Y",$row["crdate"]).'</td>
						<td nowrap align="center">'.($row["rep_latest"]?date("d-m-Y",$row["rep_latest"]):'-').'</td>
						<td nowrap align="center">'.($row["rep_uid"]?$row["rep_count"]:"-").'</td>
						<td nowrap align="center">'.($row["download_counter"]?$row["download_counter"]:"-").'</td>
						<td nowrap align="center">'.substr(md5($row["upload_password"]),0,4).'</td>
						<td nowrap align="center">'.$row["tx_extrepmgm_cache_state"].'</td>
					</tr>';
				} else {
					$items[]='<tr'.($c%2?'':$this->pi_classParam("oddRow")).'>
						<td>'.$this->pi_linkTP_keepPIvars(htmlspecialchars($row["title"]),array("showUid"=>$row["uid"],"cmd"=>"edit")).'</td>
						<td nowrap'.$params.'>'.($params?"** &nbsp;":"").$row["extension_key"].'</td>
						<td nowrap align="center">'.($row["members_only"]?"YES":"-").'</td>
						<td nowrap align="center">'.($row["rep_uid"]?$row["rep_count"]:"-").'</td>
					</tr>';
				}
				$c++;
	#			debug($row);
			}

			$out="";
			$out.='<p>'.$this->pi_linkTP_keepPIvars(htmlspecialchars($this->piVars["myListMode"]?"Show repository statistics":"Show member statistics"),array("myListMode"=>$this->piVars["myListMode"]?"":"1", "orderBy"=>"", "desc"=>"")).'</p>';
			if ($repMode)	{
				$out.='
				<table border=0 cellpadding=0 cellspacing=2'.$this->pi_classParam('legend').'>
					<tr><td nowrap><strong>Created:</strong></td><td>The day you registered this extension key. The newest keys are listed in the top.</td></tr>
					<tr><td nowrap><strong># Upl.:</strong></td><td>The number of uploaded versions in the repository. Please consider to remove old versions so you save server space.</td></tr>
					<tr><td nowrap><strong># Dnl.:</strong></td><td>The number of downloads of the most recent version in the repository.</td></tr>
					<tr><td nowrap><strong>Passwd. hash:</strong></td><td>Four letters from an md5-hash of the upload password. This helps you to get an idea which extensions has the same upload password.</td></tr>
					<tr><td nowrap><strong>State:</strong></td><td>The development state of the extension.</td></tr>
				</table>
				';
			} else {
				$out.='
				<table border=0 cellpadding=0 cellspacing=2'.$this->pi_classParam('legend').'>
					<tr><td nowrap><strong>Members-only:</strong></td><td>If this value is "YES" it means that only typo3.org users who are members of this extension can see and download it when they connect to the repository from the Extension Manager inside TYPO3 (with their username/password set).</td></tr>
					<tr><td nowrap><strong># Members:</strong></td><td>The number of members set for this extension. If none, then only you as the owner can see and download the extension. You must manually add members. Actually you\'ll have to invite people since they cannot know about your extension as long as it is "Members only".</td></tr>
				</table>
				';
			}
			$out.='<table '.$this->conf['listExt.']['tableParams_myList'].$this->pi_classParam('lTbl').'>'.implode('',$items).'</table>';


				// Make password form
			$out.='<h3>Change upload password globally</h3>';
			$out.='<form action="'.t3lib_div::getIndpEnv('REQUEST_URI').'" method="post" style="margin: 0px 0px 0px 0px;" name="editForm">
				<BR>
				<p>Set new upload password for ALL my extensions:<BR>
					<input type="password" name="'.$this->prefixId.'[DATA][uploadPassword]" value=""><BR>
					<input type="password" name="'.$this->prefixId.'[DATA][uploadPassword2]" value=""> (Enter again)<BR>
					<input type="checkbox" name="'.$this->prefixId.'[DATA][confirm]" value="1"> Confirm action<BR>
					<input type="submit" value="SET password"></p>
			</form>';

				// Make extension comparison table for non-found extension keys:
			$out.='<h3>Extension key comparison</h3>';
			if (count($entries))	{
				$out.='<p>All extension keys above marked with red color are extension keys you own and which were in the list you supplied. This is the extension keys from the list which were not found above:</p>';

				$trow=array();
				reset($entries);
				while(list($extKey)=each($entries))	{
					$q='SELECT tx_extrep_keytable.* FROM tx_extrep_keytable
						 WHERE tx_extrep_keytable.pid='.$this->dbPageId.
						 " AND tx_extrep_keytable.extension_key='".addslashes($extKey)."'".
						$GLOBALS['TSFE']->sys_page->enableFields('tx_extrep_keytable');

					$res = mysql(TYPO3_db,$q);
					if($row=mysql_fetch_assoc($res))	{
						$trow[]='<tr><td>'.$extKey.'</td><td>
								Owned by: '.$this->getUserName($row['owner_fe_user']).'<BR>
							</td></tr>';
					} else {
						$trow[]='<tr><td>'.$extKey.'</td><td><em>Not registered</em></td></tr>';
					}
				}
				$out.='<table border=1>'.implode('',$trow).'</table>';
			}
				// Make extension key form:
			$out.='<form action="'.t3lib_div::getIndpEnv('REQUEST_URI').'" method="post" style="margin: 0px 0px 0px 0px;" name="compareForm">
				<BR>
				<p>Enter lines with extension keys to compare with your registered ones:<BR>
					<textarea cols="20" rows="5" name="'.$this->prefixId.'[DATA][cmp_extkeys]"></textarea>
					<input type="submit" value="COMPARE"></p>
			</form>';





		}

		return '<DIV'.$this->pi_classParam('myList').'>'.$out.'</DIV>';
	}

	/**
	 * This updates the fe_user selection of extensions.
	 *
	 * @return	[type]		...
	 */
	function updateFeUserSelection()	{
			// STORING THE SELECTION MADE BY THE USER:
		if ($GLOBALS['TSFE']->loginUser && $this->piVars['DATA']['cmd_setSelection'])	{
			$fe_user_uid = intval($GLOBALS['TSFE']->fe_user->user['uid']);
			if ($fe_user_uid>0)	{
				$currentArray = unserialize($GLOBALS['TSFE']->fe_user->user['tx_extrepmgm_selext']);
				if (!is_array($currentArray))	$currentArray=array();
				$currentArray['extSelection']=$this->piVars['DATA']['extkey'];
				$GLOBALS['TSFE']->fe_user->user['tx_extrepmgm_selext'] = serialize($currentArray);
#debug($currentArray);
				$query = "UPDATE fe_users SET tx_extrepmgm_selext='".addslashes($GLOBALS['TSFE']->fe_user->user['tx_extrepmgm_selext'])."' WHERE uid=".$fe_user_uid;
				$res = mysql(TYPO3_db,$query);
				echo mysql_error();
#debug($query);
			}
		}
	}

	/**
	 * Render table row for classic extension list.
	 *
	 * @param	[type]		$rec: ...
	 * @param	[type]		$listOnlySel: ...
	 * @return	[type]		...
	 */
	function makeTableRowForFullList($rec,$listOnlySel)	{
		$sel=0;
		if (!$listOnlySel && $GLOBALS['TSFE']->loginUser && !t3lib_div::inList('owner,member',$rec['_ACCESS']))	{
			$sel = in_array($rec['extension_uid'],$this->ext_feUserSelection);
			$img = '<input type="checkbox" name="'.$this->prefixId.'[DATA][extkey]['.$rec['extension_uid'].']" value="1" style="width:12;height:12;"'.($sel?" CHECKED":"").'>';
		} else {
			$img = '<img src="clear.gif" width=16 height=1>';
		}

		$accessVals=array(
			'all'=>'&nbsp;',
			'member'=>'<span style="color:red;"><strong>Member</strong></span>',
#			'selected'=>'<strong>Selected</strong>',
			'owner'=>'<span style="color:red;"><strong>Owner</strong></span>',
			'owner_pub'=>'Owner',
			'member_pub'=>'Member',
		);
			// Is owner of a public plugin:
		if ($GLOBALS['TSFE']->loginUser)	{
			$user_status=$this->isUserMemberOrOwnerOfExtension($GLOBALS['TSFE']->fe_user->user, $rec['_EXTKEY_ROW']);
			if ($user_status=="owner" && $rec['_ACCESS']!='owner')	{
				$rec['_ACCESS']='owner_pub';
			} elseif ($user_status=="member" && $rec['_ACCESS']!='member')	{
				$rec['_ACCESS']='member_pub';
			}
		}


		$bgcol= t3lib_div::inList('owner,member',$rec['_ACCESS']) ? $this->conf['listExt.']['bgcol.']['own_member'] : (!$sel ?  $this->conf['listExt.']['bgcol.']['selected'] : $this->conf['listExt.']['bgcol.']['default']);

		$tRow='<tr bgcolor="'.$bgcol.'">
				<td bgcolor="white">'.$img.'</td>
				<td>'.$rec['_ICON'].'</td>
				<td nowrap>'.$this->pi_linkTP_keepPIvars($rec['emconf_title'],array('showUid'=>$rec['extension_uid'])).'</td>
				<td><em>'.$this->pi_linkTP_keepPIvars($rec['extension_key'],array('showUid'=>$rec['extension_uid'])).'</em></td>
				<td nowrap>'.$rec['version'].'</td>
				'.($GLOBALS['TSFE']->loginUser ? '<td>'.$accessVals[$rec['_ACCESS']].'</td>' : '');

		switch($this->piVars['show'])	{
			case 'langdoc':
				$langStat = $this->getLangStatVisual($rec['_EXTKEY_ROW']);
				if (is_array($langStat))	$langStat=implode('',$langStat);
				$tRow.='<td nowrap>'.
					($langStat!="-" ? $this->pi_linkTP_keepPIvars('<img src="t3lib/gfx/edit2.gif" width="11" height="12" hspace=2 border="0" align="absmiddle">',array('showUid'=>$rec['extension_uid'],'cmd'=>'translate')) : "").
					$langStat.
					'</td>';


					// Documentation:
				$documentationIndex = $rec['_EXTKEY_ROW']['tx_extrepmgm_nodoc_flag']&1 ? '' : '<span style="color:red"><strong>No documentation!</strong></span>';	// default...
				if ($rec['_EXTKEY_ROW']['tx_extrepmgm_cache_oodoc'])	{
					$documentationIndex=$this->linkToDocumentation('MANUAL',$rec['_EXTKEY_ROW']['uid']);
				}
				$tRow.='
					<td nowrap>'.$documentationIndex.'</td>
					<td>-</td>
				</tr>';
			break;
			default:
				$tRow.='
					<td nowrap>'.(t3lib_div::formatSize($rec['datasize']).'/'.t3lib_div::formatSize($rec['datasize_gz'])).'</td>
					<td nowrap>'.($rec['_STAT_IMPORT']['extension_allversions']?$rec['_STAT_IMPORT']['extension_allversions']:"&nbsp;&nbsp;").'/'.($rec['_STAT_IMPORT']['extension_thisversion']?$rec['_STAT_IMPORT']['extension_thisversion']:"&nbsp;").'</td>
					<td>'.$this->states[$rec['emconf_state']].'</td>
				</tr>';
			break;
		}
		return $tRow;
	}

	/**
	 * Render the header-rows for classic extension listing
	 *
	 * @return	[type]		...
	 */
	function makeTableHRowForFullList()	{

		$tRow='<tr>
				<td'.$this->pi_classParam('HCell').'>&nbsp;</td>
				<td'.$this->pi_classParam('HCell').'>&nbsp;</td>
				<td nowrap'.$this->pi_classParam('HCell').'>Title:</td>
				<td nowrap'.$this->pi_classParam('HCell').'>Extension key:</td>
				<td nowrap'.$this->pi_classParam('HCell').'>Version:</td>
					'.($GLOBALS['TSFE']->loginUser ? '<td nowrap'.$this->pi_classParam('HCell').'>Access:</td>' : '');
		switch($this->piVars['show'])	{
			case 'langdoc':
				$tRow.='
					<td nowrap'.$this->pi_classParam('HCell').'>Translation:</td>
					<td nowrap'.$this->pi_classParam('HCell').'>Documentation:</td>
					<td nowrap'.$this->pi_classParam('HCell').'>Other:</td>
				</tr>';
			break;
			default:
				$tRow.='
					<td nowrap'.$this->pi_classParam('HCell').'>Size:</td>
					<td nowrap'.$this->pi_classParam('HCell').'>DL:</td>
					<td nowrap'.$this->pi_classParam('HCell').'>State:</td>
				</tr>';
			break;
		}
		return $tRow;
	}

	/*********************************************************
	 *
	 * 	User listing
	 *
	 **********************************************************/

	/**
	 * Output the list of users who has contributed in some way
	 *
	 * This is CACHED content!
	 *
	 * @return	[type]		...
	 */
	function listUsers()	{
		$userArray = array();

			// Special flags and assigned jobs:
		$query = 'SELECT * FROM fe_users WHERE '.
					'pid='.intval($this->dbPageId).
					" AND (tx_extrepmgm_isreviewer>0 OR tx_extrepmgm_isdocreviewer>0 OR tx_extrepmgm_jobs!='' OR tx_extrepmgm_contribute)".
					$this->cObj->enableFields('fe_users').
					' ORDER BY crdate';
		$res = mysql(TYPO3_db,$query);
		while($row=mysql_fetch_assoc($res))	{
			$userArray[$row['uid']]['rec']=$row;
		}

			// Owning Extensions:
		$query = 'SELECT fe_users.*, count(*) AS extCount FROM fe_users,tx_extrep_keytable WHERE '.
					'fe_users.pid='.intval($this->dbPageId).
					' AND tx_extrep_keytable.pid='.intval($this->dbPageId).
					' AND fe_users.uid=tx_extrep_keytable.owner_fe_user AND tx_extrep_keytable.members_only=0'.
					$this->cObj->enableFields('fe_users').
					$this->cObj->enableFields('tx_extrep_keytable').
					' GROUP BY fe_users.uid';

		$res = mysql(TYPO3_db,$query);
		echo mysql_error();
		while($row=mysql_fetch_assoc($res))	{
			$userArray[$row['uid']]['rec']=$row;
			$userArray[$row['uid']]['extCount']=$row['extCount'];
		}

			// Being translators or assistants:
		$query = 'SELECT * FROM tx_extrepmgm_langadmin WHERE '.
				'pid='.intval($this->dbPageId).
				$this->cObj->enableFields('tx_extrepmgm_langadmin');
		$res = mysql(TYPO3_db,$query);
			// For each language:
		while($row=mysql_fetch_assoc($res))	{
				// Finding chief translator
			$chiefTranslator = $this->pi_getRecord('fe_users',$row['auth_translator']);
			if (is_array($chiefTranslator))	{
				$userArray[$chiefTranslator['uid']]['rec']=$chiefTranslator;
				$userArray[$chiefTranslator['uid']]['translator'][]=$row['title'];
			}
				// FInding assistants:
			$query = 'SELECT fe_users.* FROM tx_extrepmgm_langadmin_sub_translators_mm,fe_users WHERE
							fe_users.pid='.intval($this->dbPageId).
							' AND tx_extrepmgm_langadmin_sub_translators_mm.uid_foreign = fe_users.uid
							 AND tx_extrepmgm_langadmin_sub_translators_mm.uid_local = '.intval($row['uid']).
							$this->cObj->enableFields('fe_users').
							' ORDER BY tx_extrepmgm_langadmin_sub_translators_mm.sorting';
			$res2 = mysql(TYPO3_db,$query);
			echo mysql_error();
			while($row2=mysql_fetch_assoc($res2))	{
				$userArray[$row2['uid']]['rec']=$row2;
				$userArray[$row2['uid']]['assisting_translator'][]=$row['title'];
			}
		}

			// Typeheads:
		if (t3lib_extmgm::isLoaded('distributionlist'))	{
			$query = 'SELECT fe_users.*,tx_distributionlist_listmgm.country AS listmgmcountry FROM fe_users,tx_distributionlist_listmgm WHERE '.
				'fe_users.pid='.intval($this->dbPageId).
				' AND tx_distributionlist_listmgm.pid='.intval($this->dbPageId).
				' AND fe_users.uid=tx_distributionlist_listmgm.fe_user'.
				$this->cObj->enableFields('fe_users').
				$this->cObj->enableFields('tx_distributionlist_listmgm');

			$res = mysql(TYPO3_db,$query);
			echo mysql_error();
			while($row=mysql_fetch_assoc($res))	{
				$userArray[$row['uid']]['rec']=$row;
				$userArray[$row['uid']]['listmgmcountry'][]=$row['listmgmcountry'];
			}
		}

			// References
/*
	// NO references count
		if (t3lib_extmgm::isLoaded('t3references'))	{
			$query = 'SELECT fe_users.*, count(*) AS refcount FROM fe_users,tx_t3references WHERE '.
				'fe_users.pid='.intval($this->dbPageId).
				' AND tx_t3references.pid='.intval($this->conf['listUsers.']['referencesPid']).
				' AND fe_users.uid=tx_t3references.fe_owner_user'.
				$this->cObj->enableFields('fe_users').
				$this->cObj->enableFields('tx_t3references').
				' GROUP BY fe_users.uid';
#debug($query);
			$res = mysql(TYPO3_db,$query);
			echo mysql_error();
			while($row=mysql_fetch_assoc($res))	{
				if ($row['refcount']>1)	{
					$userArray[$row['uid']]['rec']=$row;
					$userArray[$row['uid']]['refcount']=$row['refcount'];
				}
			}
		}
	*/



			// Putting together the list:
		$lines=array();
		$lines[]='<tr>
			<td'.$this->pi_classParam('HCell').'>&nbsp;</td>
			<td'.$this->pi_classParam('HCell').'>Username/Name:</td>
			<td'.$this->pi_classParam('HCell').' width="50%">Jobs:</td>
			<td'.$this->pi_classParam('HCell').'>Personal mission:</td>
		</tr>';


			// Sorting:
		$userArray_count=array();
		reset($userArray);
		while(list($k,$dat)=each($userArray))	{
			$userArray_count[$k]=$dat['rec']['tx_extrepmgm_jobspoints'];
		}
		arsort($userArray_count);
#debug($userArray_count)		;

		$showUidPid = intval($this->conf['listUsers.']['tx_newloginbox_pi3-showUidPid']);

#debug($showUidPid);
		$c=0;
		$R_URI = t3lib_div::getIndpEnv('REQUEST_URI');
		$highScore=array();
		$emails='';

		reset($userArray_count);
		while(list($uUid)=each($userArray_count))	{
			$dat = $userArray[$uUid];

			$jobs=array();
			$jobpoints=intval($dat['rec']['tx_extrepmgm_addpoints']);

			if (trim($dat['rec']['tx_extrepmgm_jobs']))	{
				$jobs[]=trim($dat['rec']['tx_extrepmgm_jobs']);
				$jobpoints+=100;
			}
			if ($dat['extCount'] > 3)	{
				$jobs[]='Contributed '.$dat['extCount'].' publicly available extensions.';
				$jobpoints+=$dat['extCount']*10;
			}
			if ($dat['rec']['tx_extrepmgm_isreviewer'])	{
				$jobs[]='Authorized Extension Mentor.';
				$jobpoints+=100;
			}
			if ($dat['rec']['tx_extrepmgm_isdocreviewer'])	{
				$jobs[]='Proofreader of english documentation.';
				$jobpoints+=30;
			}
			if (is_array($dat['translator']))	{
				$jobs[]='Chief translator of the '.implode(' and ',$dat['translator']).' language.';
				$jobpoints+=40*count($dat['translator']);
			}
			if (is_array($dat['assisting_translator']))	{
				$jobs[]='Assisting translator of the '.implode(' and ',$dat['assisting_translator']).' language.';
				$jobpoints+=20*count($dat['assisting_translator']);
			}
			if (is_array($dat['listmgmcountry']))	{
				$jobs[]='Typehead of '.implode(' and ',$dat['listmgmcountry']);
				$jobpoints+=50*count($dat['listmgmcountry']);
			}
			if ($dat['refcount'])	{
				$jobs[]='Has registered '.$dat['refcount'].' TYPO3-made websites.';
				$jobpoints+=$dat['refcount']*2;
			}


			if (trim($dat['rec']['tx_extrepmgm_contribute']))	{
	#			$jobs[]='I want to contribute with: "<em>'.trim(strip_tags($dat['rec']['tx_extrepmgm_contribute'])).'</em>"';
			}




			$image='';
			if ($dat['rec']['tx_extrepmgm_images'])	{
				$imgArr = t3lib_div::trimExplode(',',$dat['rec']['tx_extrepmgm_images'],1);
				$GLOBALS['TSFE']->make_seed();
				$randval = intval(rand(0,count($imgArr)-1));
				$imgFile = 'uploads/tx_extrepmgm/'.$imgArr[$randval];
				$imgInfo = getimagesize(PATH_site.$imgFile);
				if (is_array($imgInfo))	{
					$image='<img src="'.$imgFile.'" '.$imgInfo[3].'>';
				}
			}

			$pp=($c%2 ? $this->pi_classParam('odd'):'');
			$lines[]='<tr>
				<td'.$pp.'>'.$image.'</td>
				<td'.$pp.'><em>'.($showUidPid ? $this->pi_linkToPage($dat['rec']['username'],$showUidPid,'',array('tx_newloginbox_pi3[showUid]' => $dat['rec']['uid'], 'tx_newloginbox_pi3[returnUrl]'=>$R_URI)) : $dat['rec']['username']).'</em><BR>'.$dat['rec']['name'].'</td>
				<td'.$pp.' width="50%">'.implode('<BR>',$jobs).'</td>
				<td'.$pp.'>'.(trim($dat['rec']['tx_extrepmgm_contribute'])?'<em>'.htmlspecialchars(strip_tags($dat['rec']['tx_extrepmgm_contribute'])).'</em>':'').'</td>
			</tr>
			';
			$highScore[]=$dat['rec']['username'].':'.$jobpoints;
			$emails.=','.$dat['rec']['email'];

			if ($dat['rec']['tx_extrepmgm_jobspoints']!=intval($jobpoints))	{
				$query='UPDATE fe_users SET tx_extrepmgm_jobspoints='.intval($jobpoints).' WHERE uid='.intval($dat['rec']['uid']);
				$res = mysql(TYPO3_db,$query);
			}

			$c++;
		}

#		$out='<h3>Who-is-who and contributers</h3>';
#		$out.='<p>In alphabetical order</p>';
		$out.='<table '.$this->conf['listUsers.']['tableParams'].'>
		'.implode(chr(10),$lines).'
		</table>';

		if ($GLOBALS['TSFE']->loginUser)	{
		$out.='<!--

'.implode(chr(10),$highScore).'


'.implode(',',t3lib_div::trimExplode(',',$emails,1)).'

		-->';
		}

		return '<DIV'.$this->pi_classParam('ulist').'>'.$out.'</DIV>';
	}


}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/extrep_mgm/pi1/class.tx_extrepmgm_listviews.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/extrep_mgm/pi1/class.tx_extrepmgm_listviews.php']);
}

?>

<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2002-2004 Kasper Skårhøj  (kasperYYYY@typo3.com)
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
 * Plugin 'Extension Manager Frontend' for the 'extrep_mgm' extension.
 *
 * @author		Kasper Skårhøj  <kasperYYYY@typo3.com>
 * @co-author	Robert Lemke <robert@typo3.org>
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   57: class tx_extrepmgm_singleviews extends tx_extrepmgm_pi1
 *   62:     function main()
 *   81:     function singleView()
 *  333:     function extensionDetails($extRepEntry)
 *  586:     function extInformationArray_dbReq($techInfo,$tableHeader=0)
 *  597:     function singleEdit()
 * 1222:     function feedback()
 * 1278:     function translateExtension()
 * 1668:     function translateSaveIncoming($fe_user_uid, $langkey, $fileName, $extKey, $LLarr)
 * 1720:     function deletedUsedTranslationRecords($extKey,$langKeyArray)
 * 1736:     function mentorReview()
 * 1844:     function oodocReview($isOwner,$extRepEntry)
 * 2075:     function downloadDocument($showDat,$extRepEntry)
 * 2131:     function showPreviewOfDocument($showDat,$extRepEntry)
 *
 * TOTAL FUNCTIONS: 13
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */

require_once(t3lib_extMgm::extPath('extrep_mgm').'pi1/class.tx_extrepmgm_pi1.php');

class tx_extrepmgm_singleviews extends tx_extrepmgm_pi1 {

	/**
	 * @return	[type]		...
	 */
	function main()	{
		if ($this->piVars['showUid'])	{	// If a single element should be displayed:
			$this->internal['currentTable'] = 'tx_extrep_keytable';
			$this->internal['currentRow'] = $this->pi_getRecord('tx_extrep_keytable',$this->piVars['showUid']);

			if (is_array($this->internal['currentRow']) && $this->checkUserAccessToExtension($this->internal['currentRow'], $GLOBALS['TSFE']->fe_user->user))	{
				$content = $this->singleView();
			} else {
				$content = '<p>Access denied!</p>';
			}
			return $content;
		}
	}

	/**
	 * Rendering the display of a single extension.
	 *
	 * @return	[type]		...
	 */
	function singleView()	{
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
/*
		if ($GLOBALS['TSFE']->fe_user->user['uid'])	{
			$logSt='<p>Login status: You are authenticated as the user <strong>'.$GLOBALS['TSFE']->fe_user->user['username'].'</strong> ('.$GLOBALS['TSFE']->fe_user->user['name'].')</p>';
		} else {
			$logSt='<p>Login status: You are not logged in.</p>';
		}
*/

		$logSt='';

			// MAKE MENU:
		$mItems =array();
		$mItems['']='Info';
		$mItems['details']='Details';
		$mItems['translate']='Translate';
		$mItems['forum']='Forum/Support';
		$mItems['feedback']='Feedback/Bugs';
		$mItems['mentorreview']='Mentor-Review';
		$mItems['oodocreview']='Doc-review';
		$mItems['edit']='Edit';

		$extRepEntry = $this->getLatestRepositoryEntry($this->internal['currentRow']['uid'],'*');

		if (!$GLOBALS['TSFE']->loginUser)	{
			$isOwner=0;
			unset($mItems['translate']);
			unset($mItems['mentorreview']);
			unset($mItems['oodocreview']);
			unset($mItems['edit']);
		} else {
			$isOwner = $GLOBALS['TSFE']->fe_user->user['uid'] && !strcmp($this->internal['currentRow']['owner_fe_user'],$GLOBALS['TSFE']->fe_user->user['uid'])?1:0;
			if (!$GLOBALS['TSFE']->fe_user->user['tx_extrepmgm_isdocreviewer'] && !$isOwner) unset($mItems['oodocreview']);
			if (!$GLOBALS['TSFE']->fe_user->user['tx_extrepmgm_isreviewer'] && $extRepEntry['tx_extrepmgm_appr_fe_user']!=intval($GLOBALS['TSFE']->fe_user->user['uid'])) unset($mItems['mentorreview']);
			if (!$isOwner)	unset($mItems['edit']);
		}
		if ($this->internal['currentRow']['tx_extrepmgm_appr_flag'])	{
			unset($mItems['mentorreview']);
		}
		if (($this->internal['currentRow']['tx_extrepmgm_flags']&4))	{
			unset($mItems['forum']);
		}
		if (!$this->internal['currentRow']['tx_extrepmgm_cache_oodoc'])	{
			unset($mItems['oodocreview']);
		}

		$topmenu='';
		reset($mItems);
		while(list($kk,$vv)=each($mItems))	{
			$topmenu.='<td'.($this->piVars['cmd']==$kk?$this->pi_classParam('SCell'):'').'>'.$this->pi_linkTP_keepPIvars(htmlentities($vv),array('cmd'=>$kk)).'</td>';
		}
		$topmenu.='<td>'.$this->pi_linkTP_keepPIvars(htmlentities('Back'),array('showUid'=>'','cmd'=>'')).'</td>';
		$topmenu='<table '.$this->conf['displayExt.']['tableParams_topmenu'].$this->pi_classParam('topmenu').'>'.$topmenu.'</table>';



			// This sets the title of the page for use in indexed search results:
		$content='
			<H3>'.$this->internal['currentRow']['title'].'</H3>
			'.$logSt.$topmenu;

		switch($this->piVars['cmd']){
			case 'mentorreview':
				$content.=$this->mentorReview();
			break;
			case 'feedback':
				$content.=$this->feedback();
			break;
			case 'oodocreview':
				$content.=$this->oodocReview($isOwner,$extRepEntry);
			break;
			case 'translate':
				$content.=$this->translateExtension();
			break;
			case 'details':
				$content.=$this->extensionDetails($extRepEntry);
			break;
			case 'forum':
				if (t3lib_extMgm::isLoaded('t3annotation'))	{
					$annotationObj = t3lib_div::makeInstance('tx_t3annotation_pi1');
					$annotationObj->cObj = &$this->cObj;
					$annotationObj->initRel('EXTREP',$this->internal['currentRow']['extension_key'].':forum');

					$sendConf = $this->conf['annotationConf.'];
					$annotationObj->pi_moreParams =	$this->conf['parent.']['addParams'].
							t3lib_div::implodeArrayForUrl('',Array($this->prefixId=>$this->piVars),'',1).
							$this->pi_moreParams;

					$content.=$annotationObj->main_ext('',$sendConf);
				}
			break;
			case 'edit':
				$content.=$this->singleEdit();
			break;
			default:
					// Getting selected information:
				$currentSelectedArray=array();
				if ($GLOBALS['TSFE']->loginUser)	{
					$fe_user_uid = intval($GLOBALS['TSFE']->fe_user->user['uid']);
					if ($fe_user_uid>0)	{
						$currentSelectedArray = unserialize($GLOBALS['TSFE']->fe_user->user['tx_extrepmgm_selext']);
						if (!is_array($currentSelectedArray))	$currentSelectedArray=array();

							// See selected state coming...
						if (isset($this->piVars['DATA']['selforlist']))	{
							if ($this->piVars['DATA']['selforlist'])	{
								$currentSelectedArray['extSelection'][$this->internal['currentRow']['uid']]=1;
							} else {
								unset($currentSelectedArray['extSelection'][$this->internal['currentRow']['uid']]);
							}

							$GLOBALS['TSFE']->fe_user->user['tx_extrepmgm_selext'] = serialize($currentSelectedArray);
							$query = "UPDATE fe_users SET tx_extrepmgm_selext='".addslashes($GLOBALS['TSFE']->fe_user->user['tx_extrepmgm_selext'])."' WHERE uid=".$fe_user_uid;
							$res = mysql(TYPO3_db,$query);
							echo mysql_error();
						}
					}
				}


					// Categories:
				$cats=array();
				$query = 'SELECT tx_extrepmgm_extgroup.* FROM tx_extrepmgm_extgroup,tx_extrep_keytable_tx_extrepmgm_group_mm WHERE
							tx_extrep_keytable_tx_extrepmgm_group_mm.uid_local = '.intval($this->internal['currentRow']['uid']).'
							AND tx_extrep_keytable_tx_extrepmgm_group_mm.uid_foreign=tx_extrepmgm_extgroup.uid'.
							$this->cObj->enableFields('tx_extrepmgm_extgroup').
							' ORDER BY tx_extrep_keytable_tx_extrepmgm_group_mm.sorting';
				$cats=array();
				$res = mysql(TYPO3_db,$query);
				echo mysql_error();
				while($row=mysql_fetch_assoc($res))	{
					$cats[]=$row['title'];
				}

				$reviewIcon = $this->getIcon_review($extRepEntry,$this->internal['currentRow'],1);
				$selectForMyList = $GLOBALS['TSFE']->loginUser ? '<tr><td nowrap'.$this->pi_classParam('HCell').' colspan=2><form action="'.t3lib_div::getIndpEnv('REQUEST_URI').'" method="POST" style="margin: 0px 0px 0px 0px;" name="selforlist">Select for my list: '.
					'<input type="hidden" name="'.$this->prefixId.'[DATA][selforlist]" value="0"><input type="checkbox" name="'.$this->prefixId.'[DATA][selforlist]" value="1" onClick="document.selforlist.submit();"'.($currentSelectedArray['extSelection'][$this->internal['currentRow']['uid']]?' CHECKED':'').'>'.
					'</form></td></tr>' : '';
#debug($this->piVars);

				$infoTable='
				<table '.$this->conf['displayExt.']['tableParams_extInfoTbl'].$this->pi_classParam('extInfoTbl').'>
					<tr><td nowrap'.$this->pi_classParam('HCell').'>Version:</td><td>'.$extRepEntry['version'].'</td></tr>
					<tr><td nowrap'.$this->pi_classParam('HCell').'>State:</td><td>'.$this->getIcon_state($extRepEntry).'</td></tr>
					'.($reviewIcon?'<tr><td nowrap'.$this->pi_classParam('HCell').'>Review:</td><td>'.$reviewIcon.'</td></tr>':'').'
					<tr><td nowrap'.$this->pi_classParam('HCell').'>Categories:</td><td>'.implode('<br>',$cats).'</td></tr>
					<tr><td nowrap'.$this->pi_classParam('HCell').'>CGL Compliance:</td><td>'.$extRepEntry['emconf_CGLcompliance'].'</td></tr>
					'.$selectForMyList.'
				</table>
				';

				$R_URI = t3lib_div::getIndpEnv('REQUEST_URI');
				$showUserUidPid = intval($this->conf['tx_newloginbox_pi3-showUidPid']);

					// Admin info:
				$adminRec = $this->pi_getRecord('fe_users',$this->internal['currentRow']['owner_fe_user']);
				$uName = $showUserUidPid ? $this->pi_linkToPage($adminRec['username'],$showUserUidPid,'',array('tx_newloginbox_pi3[showUid]' => $adminRec['uid'], 'tx_newloginbox_pi3[returnUrl]'=>$R_URI)) : $adminRec['username'];
				$adminInfo = is_array($adminRec) ? $uName.' ('.$this->cObj->getTypoLink($adminRec['name'],$adminRec['email']).')' : '';

					// Members info:
				$memberArray=array();
				$query = 'SELECT fe_users.* FROM tx_extrep_groupmem_mm,fe_users WHERE
							tx_extrep_groupmem_mm.uid_local = '.intval($this->internal['currentRow']['uid']).'
							AND tx_extrep_groupmem_mm.uid_foreign=fe_users.uid'.
							$this->cObj->enableFields('fe_users').
							' ORDER BY tx_extrep_groupmem_mm.sorting'
							;
				$memberArray=array();
				$res = mysql(TYPO3_db,$query);
				echo mysql_error();
				while($row=mysql_fetch_assoc($res))	{
					$uName = $showUserUidPid ? $this->pi_linkToPage($row['username'],$showUserUidPid,'',array('tx_newloginbox_pi3[showUid]' => $row['uid'], 'tx_newloginbox_pi3[returnUrl]'=>$R_URI)) : $row['username'];
					$memberArray[$row['uid']]=$uName.' ('.$this->cObj->getTypoLink($row['name'],$row['email']).')';
				}

					// Documentation:
				$documentationIndex = $this->internal['currentRow']['tx_extrepmgm_nodoc_flag']&1 ? '&nbsp;' : '<span style="color:red"><strong>No documentation!</strong></span>';	// default...
				$introDoc='';
				if ($this->internal['currentRow']['tx_extrepmgm_cache_oodoc'])	{
					$linkItems=array();
					$linkItems[]=$this->linkToDocumentation('Full manual',$this->internal['currentRow']['uid']);
					$docParts = unserialize($this->internal['currentRow']['tx_extrepmgm_documentation']);
					if (is_array($docParts['doc_kind']) && count($docParts['doc_kind']))	{
						reset($this->kinds);
						while(list($kk,$label)=each($this->kinds))	{
							if ($kk>1 && $docParts['doc_kind'][$kk])	{
								$linkItems[]=$this->linkToDocumentation('- '.$label,$this->internal['currentRow']['uid'],$docParts['doc_kind'][$kk]);
							}
						}
						if ($docParts['doc_kind'][1])	{
							$introDoc = $this->renderOOdocSlice($this->internal['currentRow']['uid'],$docParts['doc_kind'][1],1,1);
						}
					}
					if (count($linkItems))	$documentationIndex=implode('<br>',$linkItems);
				}

					// Preparing details status:
				$hpContentArray = t3lib_div::trimExplode(chr(10),trim(strip_tags($this->internal['currentRow']['tx_extrepmgm_homepage'],'<b><i><u><a><h3><ul><ol><li>')));
				reset($hpContentArray);
				while(list($hpK)=each($hpContentArray))	{
					if (substr(strtolower($hpContentArray[$hpK]),0,4)!='<h3>')	{
						$hpContentArray[$hpK].='<br>';
					}
				}
				$hpContent = implode(chr(10),$hpContentArray);

					// Putting it all together:
				$content.='
					<table '.$this->conf['displayExt.']['tableParams'].$this->pi_classParam('dTbl').'>
						<tr>
							<td nowrap valign="top"'.$this->pi_classParam('HCell').'>Ext. Key:</td>
							<td valign="top" width="90%"><em>'.$this->internal['currentRow']['extension_key'].'</em></td>
							<td rowspan=5 valign=top>'.$infoTable.'</td>
						</tr>
						<tr>
							<td nowrap'.$this->pi_classParam('HCell').'>Description:</td>
							<td valign="top">'.$this->internal['currentRow']['description'].'</td>
						</tr>
						<tr>
							<td nowrap'.$this->pi_classParam('HCell').'>Owner:</td>
							<td valign="top">'.$adminInfo.'</td>
						</tr>
						<tr>
							<td nowrap'.$this->pi_classParam('HCell').'>Members:</td>
							<td valign="top">'.(count($memberArray)?implode('<BR>',$memberArray):'&nbsp;').'</td>
						</tr>
						<tr>
							<td nowrap'.$this->pi_classParam('HCell').'>Documentation:</td>
							<td valign="top">'.$documentationIndex.'</td>
						</tr>
						<tr>
							<td nowrap'.$this->pi_classParam('HCell').'>Status notepad:</td>
							<td valign="top" colspan=2>'.$hpContent.'</td>
						</tr>
					</table>
				<br>'.$introDoc;
			break;
		}

		$content.='<P class="back">'.$this->pi_linkTP_keepPIvars(htmlentities('Back'),array('showUid'=>'','cmd'=>'')).'</P>';

		return '<DIV'.$this->pi_classParam('singleView').'>'.$content.'</DIV>'.$this->pi_getEditPanel();
	}

	/**
	 * Extension details
	 *
	 * @param	[type]		$extRepEntry: ...
	 * @return	[type]		...
	 */
	function extensionDetails($extRepEntry)	{
		$datablob = unserialize(gzuncompress($extRepEntry['datablob']));
		unset($extRepEntry['datablob']);
		unset($extRepEntry['icondata']);


				// Putting it all together:
		$content='';
		$content.='<tr>
					<td nowrap valign="top"'.$this->pi_classParam('HCell').'>Ext. Key (Uid):</td>
					<td>'.$extRepEntry['extension_key'].' ('.$extRepEntry['extension_uid'].')</td>
				</tr>';
		$content.='<tr>
					<td nowrap valign="top"'.$this->pi_classParam('HCell').'>Version:</td>
					<td>'.$extRepEntry['version'].'</td>
				</tr>';
		$content.='<tr>
					<td nowrap valign="top"'.$this->pi_classParam('HCell').'>Tech. Category:</td>
					<td>'.($this->categories[$extRepEntry['emconf_category']]?$this->categories[$extRepEntry['emconf_category']]:'<em>['.$extRepEntry['emconf_category'].']</em>').'</td>
				</tr>';
		$content.='<tr>
					<td nowrap valign="top"'.$this->pi_classParam('HCell').'>State:</td>
					<td>'.($this->states[$extRepEntry['emconf_state']]?$this->states[$extRepEntry['emconf_state']]:'<em>['.$extRepEntry['emconf_state'].']</em>').'</td>
				</tr>';
		$content.='<tr>
					<td nowrap valign="top"'.$this->pi_classParam('HCell').'>Shy?</td>
					<td>'.($extRepEntry['emconf_shy']?'YES':'&nbsp;').'</td>
				</tr>';
		$content.='<tr>
					<td nowrap valign="top"'.$this->pi_classParam('HCell').'>Internal?</td>
					<td>'.($extRepEntry['emconf_internal']?'YES':'&nbsp;').'</td>
				</tr>';
		$content.='<tr>
					<td nowrap valign="top"'.$this->pi_classParam('HCell').'>Dependencies:</td>
					<td>'.($extRepEntry['emconf_dependencies']?$extRepEntry['emconf_dependencies']:'&nbsp;').'</td>
				</tr>';
		$content.='<tr>
					<td nowrap valign="top"'.$this->pi_classParam('HCell').'>Conflicts:</td>
					<td>'.($extRepEntry['emconf_conflicts']?$extRepEntry['emconf_conflicts']:'&nbsp;').'</td>
				</tr>';
		$content.='<tr>
					<td nowrap valign="top"'.$this->pi_classParam('HCell').'>TYPO3 ver. required:</td>
					<td>'.($this->versionConv($extRepEntry['emconf_TYPO3_version_min'],TRUE).' - '.$this->versionConv($extRepEntry['emconf_TYPO3_version_max'],TRUE)).'</td>
				</tr>';
		$content.='<tr>
					<td nowrap valign="top"'.$this->pi_classParam('HCell').'>CGL Compliance:</td>
					<td>'.$extRepEntry['emconf_CGLcompliance'].($extRepEntry['emconf_CGLcompliance_note'] ? ' - <em>'.$extRepEntry['emconf_CGLcompliance_note'].'</em>' : '').'</td>
				</tr>';
		$content.='<tr>
					<td nowrap valign="top"'.$this->pi_classParam('HCell').'>PHP ver. required:</td>
					<td>'.$this->versionConv($extRepEntry['emconf_PHP_version_min'],TRUE).' - '.$this->versionConv($extRepEntry['emconf_PHP_version_max'],TRUE).'</td>
				</tr>';
		$content.='<tr>
					<td nowrap valign="top"'.$this->pi_classParam('HCell').'>Priority:</td>
					<td>'.($extRepEntry['emconf_priority']?$extRepEntry['emconf_priority']:'&nbsp;').'</td>
				</tr>';
		$content.='<tr>
					<td nowrap valign="top"'.$this->pi_classParam('HCell').'>Clear cache?</td>
					<td>'.($extRepEntry['emconf_clearCacheOnLoad']?'YES':'&nbsp;').'</td>
				</tr>';
		$content.='<tr>
					<td nowrap valign="top"'.$this->pi_classParam('HCell').'>Include modules:</td>
					<td>'.($extRepEntry['emconf_module']?$extRepEntry['emconf_module']:'&nbsp;').'</td>
				</tr>';
		$content.='<tr>
					<td nowrap valign="top"'.$this->pi_classParam('HCell').'>Lock type?</td>
					<td>'.($extRepEntry['emconf_lockType']?'YES':'&nbsp;').'</td>
				</tr>';
		$content.='<tr>
					<td nowrap valign="top"'.$this->pi_classParam('HCell').'>Modifies tables:</td>
					<td>'.($extRepEntry['emconf_modify_tables']?$extRepEntry['emconf_modify_tables']:'&nbsp;').'</td>
				</tr>';
		$content.='<tr>
					<td nowrap valign="top"'.$this->pi_classParam('HCell').'>Create dirs:</td>
					<td>'.($extRepEntry['emconf_createDirs']?$extRepEntry['emconf_createDirs']:'&nbsp;').'</td>
				</tr>';
		$content.='<tr>
					<td nowrap valign="top"'.$this->pi_classParam('HCell').'>Is manual included?</td>
					<td>'.($extRepEntry['is_manual_included']?$extRepEntry['is_manual_included']:'&nbsp;').'</td>
				</tr>';


		$content.='<tr>
					<td nowrap valign="top"'.$this->pi_classParam('HCell').'>Last upload by user:</td>
					<td>'.$this->getUserName($extRepEntry['last_upload_by_user']).', '.date('d-m-Y H:i',$extRepEntry['last_upload_date']).'</td>
				</tr>';
		$content.='<tr>
					<td nowrap valign="top"'.$this->pi_classParam('HCell').'># Uploads (this version):</td>
					<td>'.$extRepEntry['upload_counter'].'</td>
				</tr>';
		$content.='<tr>
					<td nowrap valign="top"'.$this->pi_classParam('HCell').'>Upload comment:</td>
					<td>'.nl2br(trim($extRepEntry['upload_comment'])).'</td>
				</tr>';
		$content.='<tr>
					<td nowrap valign="top"'.$this->pi_classParam('HCell').'>Uploaded from versions:</td>
					<td>TYPO3: '.$extRepEntry['upload_typo3_version'].' / PHP: '.$extRepEntry['upload_php_version'].'</td>
				</tr>';

		$content.='<tr>
					<td nowrap valign="top"'.$this->pi_classParam('HCell').'>Codelines:</td>
					<td>'.$extRepEntry['codelines'].'</td>
				</tr>';
		$content.='<tr>
					<td nowrap valign="top"'.$this->pi_classParam('HCell').'>Codebytes:</td>
					<td>'.t3lib_div::formatSize($extRepEntry['codebytes']).'</td>
				</tr>';
		$content.='<tr>
					<td nowrap valign="top"'.$this->pi_classParam('HCell').'>Total size:</td>
					<td>'.t3lib_div::formatSize($extRepEntry['datasize']).'</td>
				</tr>';
		$content.='<tr>
					<td nowrap valign="top"'.$this->pi_classParam('HCell').'>Compressed size:</td>
					<td>'.t3lib_div::formatSize($extRepEntry['datasize_gz']).' ('.floor($extRepEntry['datasize_gz']/$extRepEntry['datasize']*100).'%)</td>
				</tr>';


		$techInfo = unserialize($extRepEntry['techinfo']);
		$content.='<tr>
					<td nowrap valign="top"'.$this->pi_classParam('HCell').'>Database requirements:</td>
					<td>'.$this->extInformationArray_dbReq($techInfo,1).'</td>
				</tr>';
		$content.='<tr>
					<td nowrap valign="top"'.$this->pi_classParam('HCell').'>Flags:</td>
					<td>'.(is_array($techInfo['flags'])?implode('<BR>',$techInfo['flags']):"&nbsp;").'</td>
				</tr>';
		$content.='<tr>
					<td nowrap valign="top"'.$this->pi_classParam('HCell').'>Config template?</td>
					<td>'.($techInfo['conf']?"Yes":"&nbsp;").'</td>
				</tr>';
		$content.='<tr>
					<td nowrap valign="top"'.$this->pi_classParam('HCell').'>TypoScript files:</td>
					<td>'.(is_array($techInfo['TSfiles'])?implode('<BR>',$techInfo['TSfiles']):"&nbsp;").'</td>
				</tr>';
		$content.='<tr>
					<td nowrap valign="top"'.$this->pi_classParam('HCell').'>Language files:</td>
					<td>'.(is_array($techInfo['locallang'])?implode('<BR>',$techInfo['locallang']):"&nbsp;").'</td>
				</tr>';
		$content.='<tr>
					<td nowrap valign="top"'.$this->pi_classParam('HCell').'>Upload folder:</td>
					<td>'.($techInfo['uploadfolder']?$techInfo['uploadfolder']:"&nbsp;").'</td>
				</tr>';
		$content.='<tr>
					<td nowrap valign="top"'.$this->pi_classParam('HCell').'>Create directories:</td>
					<td>'.(is_array($techInfo['createDirs'])?implode('<BR>',$techInfo['createDirs']):"&nbsp;").'</td>
				</tr>';
		$content.='<tr>
					<td nowrap valign="top"'.$this->pi_classParam('HCell').'>Module names:</td>
					<td>'.(is_array($techInfo['moduleNames'])?implode('<BR>',$techInfo['moduleNames']):"&nbsp;").'</td>
				</tr>';
		$content.='<tr>
					<td nowrap valign="top"'.$this->pi_classParam('HCell').'>Class names:</td>
					<td>'.(is_array($techInfo['classes'])?implode('<BR>',$techInfo['classes']):"&nbsp;").'</td>
				</tr>';
		$content.='<tr>
					<td nowrap valign="top"'.$this->pi_classParam('HCell').'>Errors:</td>
					<td>'.(is_array($techInfo['errors'])?'<span style="color:red;"><strong>'.implode('<HR>',$techInfo['errors']).'</strong></span>':"&nbsp;").'</td>
				</tr>';


			// FILES:
		$files = unserialize($extRepEntry['files']);
		$filesCode='&nbsp;';
		if (is_array($files))	{
			reset($files);
			$trow=array();
			while(list($fileName,$fileDat)=each($files))	{
				$trow[]='<tr>
					<td nowrap>'.$fileName.'</td>
					<td nowrap>'.t3lib_div::formatSize($fileDat['size']).'</td>
					<td nowrap>'.($fileDat['codelines']?$fileDat['codelines']:'&nbsp;').'</td>
					<td nowrap>'.($fileDat['codelines']?$this->pi_linkTP_keepPIvars('View',array('DATA' => array('showFile'=>$fileName))):'&nbsp;').'</td>
					<td nowrap>'.date('d-m-Y H:i',$fileDat['mtime']).'</td>
					<td nowrap>'.$this->pi_linkTP_keepPIvars('Download',array('DATA' => array('dlFile'=>$fileName))).'</td>
				</tr>';
			}
			$filesCode='<table '.$this->conf['displayExt.']['tableParams_files'].$this->pi_classParam('fTbl').'>
				<tr'.$this->pi_classParam('HRow').'>
					<td>Filename:</td>
					<td>Filesize:</td>
					<td colspan=2>Codelines:</td>
					<td>Modified:</td>
					<td>&nbsp;</td>
				</tr>
				'.implode(chr(10),$trow).'
			</table>';

			$filesCode.='<BR><BR>
				<p><a href="index.php?id=1417&tx_extrep[cmd]=importExtension&tx_extrep[uid]='.$extRepEntry['uid'].'&tx_extrep[dlFileName]='.rawurlencode('T3X_'.$extRepEntry['extension_key'].'-'.str_replace('.','_',$extRepEntry['version']).'-z.t3x').'&tx_extrep[gzcompress]=1">Download compressed extension .T3X file</a></p>
				<p><a href="index.php?id=1417&tx_extrep[cmd]=importExtension&tx_extrep[uid]='.$extRepEntry['uid'].'&tx_extrep[dlFileName]='.rawurlencode('T3X_'.$extRepEntry['extension_key'].'-'.str_replace('.','_',$extRepEntry['version']).'.t3x').'">(Download <em>un</em>compressed extension .T3X file)</a></p>
				';
		}
		$content.='
			<tr>
				<td nowrap valign="top"'.$this->pi_classParam('HCell').'>Files:</td>
				<td>'.$filesCode.'</td>
			</tr>
		';

		$content = '<table '.$this->conf['displayExt.']['tableParams'].$this->pi_classParam('dTbl').'>'.$content.'</table>';


		// DOWNLOAD OR SHOW of file:
		if ($this->piVars['DATA']['showFile'])	{
			$content.= '<h3>Showing content of "'.$this->piVars['DATA']['showFile'].'":</h3>';
#			$content.= '<hr><pre>'.htmlspecialchars($datablob[$this->piVars['DATA']['showFile']]['content']).'</pre><hr>';

				// Syntax highlighted PHP:
			ob_start();
			highlight_string($datablob[$this->piVars['DATA']['showFile']]['content']);
			$php = ob_get_contents();
			ob_end_clean();
#debug($php);
			$php = str_replace('<code>','<pre>',$php);
			$php = str_replace('</code>','</pre>',$php);
			$content.= '<hr>'.$php.'<hr>';
		}
		if ($this->piVars['DATA']['dlFile'])	{
			$outContent = $datablob[$this->piVars['DATA']['dlFile']]['content'];
			if (strcmp($outContent,''))	{
				$pI=pathinfo($this->piVars['DATA']['dlFile']);
				switch(strtolower($pI['extension']))	{
					case 'gif':
						$mimeType = 'image/gif';
					break;
					case 'jpeg':
					case 'jpg':
						$mimeType = 'image/jpeg';
					break;
					case 'png':
						$mimeType = 'image/png';
					break;
					default:
						$mimeType = 'application/octet-stream';
					break;
				}
				Header('Content-Type: '.$mimeType);
				Header('Content-Disposition: attachment; filename='.basename($this->piVars['DATA']['dlFile']));
				echo $outContent;
				exit;
			}
		}

		return $content;
	}

	/**
	 * [Describe function...]
	 *
	 * @param	[type]		$techInfo: ...
	 * @param	[type]		$tableHeader: ...
	 * @return	[type]		...
	 */
	function extInformationArray_dbReq($techInfo,$tableHeader=0)	{
		return nl2br(trim((is_array($techInfo['tables'])?($tableHeader?"\n\n<strong>Tables:</strong>\n":"").implode("\n",$techInfo['tables']):"").
				(is_array($techInfo['static'])?"\n\n<strong>Static tables:</strong>\n".implode("\n",$techInfo['static']):"").
				(is_array($techInfo['fields'])?"\n\n<strong>Additional fields:</strong>\n".implode('<HR>',$techInfo['fields']):"")));
	}

	/**
	 * Rendering the editing of a single extension.
	 *
	 * @return	[type]		...
	 */
	function singleEdit()	{
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();

		$backLink=$this->pi_linkTP_keepPIvars('|',array('cmd'=>''));
		$backLink_url = $this->cObj->lastTypoLinkUrl;
		$editTable=array();	// This array will contain HTML table rows for the editing screen.

		if (!$GLOBALS['TSFE']->fe_user->user['uid'] || strcmp($this->internal['currentRow']['owner_fe_user'],$GLOBALS['TSFE']->fe_user->user['uid']))	{
			return '<p>You are trying to edit this extension, but do NOT own it, so beat it ...</p>';
		} else {
#debug($this->piVars['DATA']);
			$extRepEntry = $this->getLatestRepositoryEntry($this->internal['currentRow']['uid'],'*');
			if (is_array($this->piVars['DATA']))	{

					// Setting meta data for TOC:
				if (is_array($this->piVars['DATA']['tocElements_kind']))	{
					$docArray=array();
					reset($this->piVars['DATA']['tocElements_kind']);
					while(list($toc_uid,$kind)=each($this->piVars['DATA']['tocElements_kind']))	{
						if (strcmp($kind,''))	$docArray['doc_kind'][$kind]=$toc_uid;
					}
					$this->piVars['DATA']['editExt']['tx_extrepmgm_documentation'] = serialize($docArray);
				}

					// Update extension itself
				if (is_array($this->piVars['DATA']['editExt']))	{
						// Fix array of checkboxes:
					$tempVal=0;
					if (is_array($this->piVars['DATA']['editExt']['tx_extrepmgm_flags']))	{
						reset($this->piVars['DATA']['editExt']['tx_extrepmgm_flags']);
						while(list($fK,$fV)=each($this->piVars['DATA']['editExt']['tx_extrepmgm_flags']))	{
							$tempVal+=pow(2,$fK);
						}
					}
					$this->piVars['DATA']['editExt']['tx_extrepmgm_flags'] = $tempVal;

					$query = $this->cObj->DBgetUpdate(
						'tx_extrep_keytable',
						$this->internal['currentRow']['uid'],
						$this->piVars['DATA']['editExt'],
						"title,description,upload_password,members_only,tx_extrepmgm_documentation,tx_extrepmgm_homepage,tx_extrepmgm_flags".($GLOBALS['TSFE']->fe_user->user['tx_extrepmgm_isreviewer']?',tx_extrepmgm_appr_flag':'')
					);
#debug($query,1);
					$res = mysql(TYPO3_db,$query);
					$this->internal['currentRow'] = $this->pi_getRecord('tx_extrep_keytable',$this->internal['currentRow']['uid']);
				}

					// Update latest repository record:
				if (is_array($extRepEntry))	{
					$updateRep=array();
					if ($this->piVars['DATA']['setTitleInRep'])	{
						$updateRep['emconf_title']=$this->internal['currentRow']['title'];
					}
#debug($this->piVars['DATA']);
					if ($this->piVars['DATA']['setState'])	{
						$updateRep['emconf_state']=$this->piVars['DATA']['setState'];
					}
					if ($this->piVars['DATA']['setDescriptionInRep'])	{
						$updateRep['emconf_description']=$this->internal['currentRow']['description'];
					}
					if ($this->piVars['DATA']['setUserdatInRep'])	{
						$updateRep['emconf_author']=$GLOBALS['TSFE']->fe_user->user['name'];
						$updateRep['emconf_author_email']=$GLOBALS['TSFE']->fe_user->user['email'];
						$updateRep['emconf_author_company']=$GLOBALS['TSFE']->fe_user->user['company'];
					}
					if (count($updateRep))	{
						$query = $this->cObj->DBgetUpdate(
							'tx_extrep_repository',
							$extRepEntry['uid'],
							$updateRep,
							'emconf_description,emconf_title,emconf_author,emconf_author_email,emconf_author_company,emconf_state'
						);
						$res = mysql(TYPO3_db,$query);
						$extRepEntry = $this->getLatestRepositoryEntry($this->internal['currentRow']['uid'],'*');
					}
				}


					// Setting categories:
				$query='DELETE FROM tx_extrep_keytable_tx_extrepmgm_group_mm WHERE uid_local='.intval($this->internal['currentRow']['uid']);
				$res = mysql(TYPO3_db,$query);
				if (is_array($this->piVars['DATA']['setCategories']))	{
					reset($this->piVars['DATA']['setCategories']);
					while(list($k,$cat_uid)=each($this->piVars['DATA']['setCategories']))	{
						$query='INSERT INTO tx_extrep_keytable_tx_extrepmgm_group_mm
							 (uid_local,uid_foreign,tablenames,sorting)
							 VALUES ('.intval($this->internal['currentRow']['uid']).','.intval($cat_uid).",'tx_extrepmgm_extgroup',".intval($k).')';
						$res = mysql(TYPO3_db,$query);
					}
				}


					// Set members:
				$query='DELETE FROM tx_extrep_groupmem_mm WHERE uid_local='.intval($this->internal['currentRow']['uid']);
				$res = mysql(TYPO3_db,$query);
				if (is_array($this->piVars['DATA']['mem']))	{
					reset($this->piVars['DATA']['mem']);
					while(list($k,$fe_user_uid)=each($this->piVars['DATA']['mem']))	{
						$query='INSERT INTO tx_extrep_groupmem_mm (uid_local,uid_foreign,tablenames,sorting) VALUES ('.intval($this->internal['currentRow']['uid']).','.intval($fe_user_uid).",'fe_users',".intval($k).')';
						$res = mysql(TYPO3_db,$query);
					}
				}

					// Delete repository entries
				if ($this->piVars['DATA']['rep_delete'] && is_array($this->piVars['DATA']['rep']))	{
					reset($this->piVars['DATA']['rep']);
					while(list(,$repuid)=each($this->piVars['DATA']['rep']))	{
						$query='DELETE FROM tx_extrep_repository WHERE
							extension_uid='.intval($this->internal['currentRow']['uid']).
							' AND uid='.intval($repuid).
							' AND uid!='.intval($extRepEntry['uid']).
							' AND tx_extrepmgm_appr_fe_user=0';
						$res = mysql(TYPO3_db,$query);
					}
				}

					// Merge translations:
				if ($this->piVars['DATA']['merge_translation'] && is_array($this->piVars['DATA']['merge_translation_set']) && count($this->piVars['DATA']['merge_translation_set']))	{
						// This merges the CHIEF (!) translation into the extRepEntry (only selected items from array 'merge_translation_set'):
					$newRepEntry = $this->makeTranslationStatus($this->internal['currentRow']['uid'],$extRepEntry,'',$this->piVars['DATA']['merge_translation_set']);

						// ... and this inserts the new version into the repository:
					$this->insertRepositoryVersion($newRepEntry);

						// This deletes the old translation records related to the extension/'merge_translation_set' lang-keys
					$this->deletedUsedTranslationRecords($this->internal['currentRow']['extension_key'],$this->piVars['DATA']['merge_translation_set']);

						// Re-fetching the latest version - good idea...
					$extRepEntry = $this->getLatestRepositoryEntry($this->internal['currentRow']['uid'],'*');
				} elseif ($this->piVars['DATA']['new_dev']) {		// New development version will ONLY happen if language is not advanced as well.
						// Simply sending the current version to be inserted which will give us a new version.
					$this->insertRepositoryVersion($extRepEntry);

						// Re-fetching the latest version - good idea...
					$extRepEntry = $this->getLatestRepositoryEntry($this->internal['currentRow']['uid'],'*');
#debug('NEW DEV!!');
				}


					// Updating TOC:
				$sxwfile='doc/manual.sxw';
				if ($this->piVars['DATA']['oodoc_'.$sxwfile.'_save'])	{
					$e = $this->getOOdoc($extRepEntry,$sxwfile);
					if (!$e)	{
						$this->toc_current = $this->makeTOCfromLoadedOOdoc();
						$this->saved_toc = $this->getTOCfromSavedOOdoc($extRepEntry['extension_uid'],$sxwfile);
						if ($masterEl = $this->getTocPHElement($extRepEntry['extension_uid'],$sxwfile))	{
#debug($masterEl);
								// Now that the new TOC is being written we can safely remove the old 1) temp-file and 2) oodoc in database.
							$this->cleanUpOldOOdoc($masterEl['cur_oodoc_ref'], $masterEl['cur_tmp_file']);
// (robert 29.10.04) DISABLED for performance reasons until we find a better solution:
//							$this->clearPageCacheForExtensionDoc($extRepEntry['extension_uid']);
							$this->clearPageCacheForExtensionDoc(0);
							$editTable['toccachewarning'] = '<tr><td colspan="4"><strong>Your document has been updated.</br ><span  style="color:red">For performance reasons, it might take some hours until your changes are visible on typo3.org! However, we are working on an improved caching management.</span></strong></td></tr>';

						}

							// Update from current TOC POV
						if (is_array($this->piVars['DATA']['oodoc_'.$sxwfile]))	{
							reset($this->piVars['DATA']['oodoc_'.$sxwfile]);
							while(list($toc_k,$st_uid)=each($this->piVars['DATA']['oodoc_'.$sxwfile]))	{
								if (is_array($this->toc_current[$toc_k]))	{
									$this->updateInsertTOCEntry($extRepEntry['extension_uid'],$sxwfile,$this->toc_current[$toc_k], is_array($this->saved_toc[$st_uid])?$st_uid:0);
									unset($this->saved_toc[$st_uid]);
								} else debug('ERROR: No toc_current entry!');
							}
						}
#debug($this->saved_toc);
							// And now set
						reset($this->saved_toc);
						while(list($st_uid,$st_rec)=each($this->saved_toc))	{
							$this->updateInsertTOCEntry($extRepEntry['extension_uid'],$sxwfile,'', $st_uid);
							unset($this->saved_toc[$st_uid]);
						}

							// Set master record
						$this->updateInsertTOCph($extRepEntry['extension_uid'],$sxwfile,$extRepEntry['is_manual_included']);
						$this->buildCacheOfCurrentParts($extRepEntry['extension_uid'],$sxwfile);
#debug($this->saved_toc);
#debug($this->toc_current);
					} else debug('ERROR: '.$e);
				}




				$updateCachedToc=0;
					// Setting the types for TOC elements:
				if (is_array($this->piVars['DATA']['tocElements_types']))	{
					reset($this->piVars['DATA']['tocElements_types']);
					while(list($toc_uid,$type)=each($this->piVars['DATA']['tocElements_types']))	{
						$this->updateTOCField($extRepEntry['extension_uid'],$sxwfile,$toc_uid,$type,'typeofcontent');
						$updateCachedToc=1;
					}
				}

					// Setting the target audience for TOC elements:
				if (is_array($this->piVars['DATA']['tocElements_aud']))	{
					reset($this->piVars['DATA']['tocElements_aud']);
					while(list($toc_uid,$audDat)=each($this->piVars['DATA']['tocElements_aud']))	{
						$aud=($audDat['U']?1:0)+($audDat['A']?2:0)+($audDat['D']?4:0);
						$this->updateTOCField($extRepEntry['extension_uid'],$sxwfile,$toc_uid,$aud,'aud');
						$updateCachedToc=1;
					}
				}

					// Setting the level-3 flag for TOC elements:
				if (is_array($this->piVars['DATA']['tocElements_l3']))	{
					reset($this->piVars['DATA']['tocElements_l3']);
					while(list($toc_uid,$audDat)=each($this->piVars['DATA']['tocElements_l3']))	{
						$this->updateTOCField($extRepEntry['extension_uid'],$sxwfile,$toc_uid,$audDat?1:0,'show_level3');
						$updateCachedToc=1;
					}
				}

				if (is_array($this->piVars['DATA']['editTocPH']))	{
					$this->updateTocPH($extRepEntry['extension_uid'],$sxwfile,$this->piVars['DATA']['editTocPH']);
					$updateCachedToc=1;
				}

					// Update cached TOC:
				if ($updateCachedToc)	{
					$this->updateCachedToc($extRepEntry['extension_uid'],$sxwfile);
// (robert 29.10.04) DISABLED for performance reasons until we find a better solution:
//					$this->clearPageCacheForExtensionDoc($extRepEntry['extension_uid']);
					$this->clearPageCacheForExtensionDoc(0);	// Clear cache for the index page
					$editTable['toccachewarning'] = '<tr><td colspan="4"><strong>Your document has been updated.</br ><span  style="color:red">For performance reasons, it might take some hours until your changes are visible on typo3.org! However, we are working on an improved caching management.</span></strong></td></tr>';
				}




					// This updates the _cache-fields in the keytable and most likely that should not be done each time we look at the extension, but probably each time it's uploaded or settings are saved.
				$this->updateExtKeyCache($this->internal['currentRow']['uid'], $extRepEntry);
				$this->internal['currentRow'] = $this->pi_getRecord('tx_extrep_keytable',$this->internal['currentRow']['uid']);	// ... and re-fetches the keytable record since it has been updated here.
			}








			$fN = $this->prefixId.'[DATA]';

				// HEADER:
			$editTable[] = '<tr>
				<td'.$this->pi_classParam('HCell').'>&nbsp;</td>
				<td'.$this->pi_classParam('HCell').'>Extension key data:</td>
				<td'.$this->pi_classParam('HCell').'>&nbsp;</td>
				<td'.$this->pi_classParam('HCell').'>Most recent version ('.($extRepEntry['version']?$extRepEntry['version']:"N/A").'):</td>
			</tr>';



				// TITLE:
			$editTable[] = '<tr>
				<td'.$this->pi_classParam('HCell').'>Title:</td>
				<td><input type="text" name="'.$fN.'[editExt][title]" value="'.htmlentities($this->internal['currentRow']['title']).'" style="width: 200px;"></td>
				<td nowrap'.(strcmp($this->internal['currentRow']['title'],$extRepEntry['emconf_title'])?' style="background-color:red;"':'').'>
					'.(is_array($extRepEntry)?'
					<input type="radio" value="1" name="'.$fN.'[setTitleInRep]" title="Transfer extension key title to most recent version in repository (on save)."> --&gt;	<BR>
					<input type="radio" value="-1" name="'.$fN.'[setTitleInRep]" onClick="document.editForm[\''.$fN.'[editExt][title]\'].value=unescape(\''.rawurlencode($extRepEntry['emconf_title']).'\');" title="Loads title from most recent version in repository into the form field to the left (on click)."> &lt;--<BR>
					':'').'
				</td>
				<td width="200px;">'.htmlentities(is_array($extRepEntry)?$extRepEntry['emconf_title']:"N/A").'</td>
			</tr>';




				// DESCRIPTION:
			$editTable[] = '<tr>
				<td'.$this->pi_classParam('HCell').'>Description:</td>
				<td><textarea style="width: 200px;" rows="5" name="'.$fN.'[editExt][description]">'.htmlentities(trim($this->internal['currentRow']['description'])).'</textarea></td>
				<td nowrap'.(strcmp($this->internal['currentRow']['description'],$extRepEntry['emconf_description'])?' style="background-color:red;"':'').'>
					'.(is_array($extRepEntry)?'
					<input type="radio" value="1" name="'.$fN.'[setDescriptionInRep]" title="Transfer extension key description to most recent version in repository (on save)."> --&gt;	<BR>
					<input type="radio" value="-1" name="'.$fN.'[setDescriptionInRep]" onClick="document.editForm[\''.$fN.'[editExt][description]\'].value=unescape(\''.rawurlencode($extRepEntry['emconf_description']).'\');" title="Loads description from most recent version in repository into the form field to the left (on click)."> &lt;--
					':'').'
				</td>
				<td width="200px;">'.htmlentities(is_array($extRepEntry)?$extRepEntry['emconf_description']:"N/A").'</td>
			</tr>';

				// USER DATA:
			$editTable[] = '<tr>
				<td'.$this->pi_classParam('HCell').'>Name:<BR>Email:<BR>Company:</td>
				<td nowrap>'.
					htmlentities($GLOBALS['TSFE']->fe_user->user['name']).'&nbsp;<BR>'.
					htmlentities($GLOBALS['TSFE']->fe_user->user['email']).'&nbsp;<BR>'.
					htmlentities($GLOBALS['TSFE']->fe_user->user['company']).
					'</td>
				<td nowrap'.(strcmp($GLOBALS['TSFE']->fe_user->user['name'].'|'.$GLOBALS['TSFE']->fe_user->user['email'].'|'.$GLOBALS['TSFE']->fe_user->user['company'], $extRepEntry['emconf_author'].'|'.$extRepEntry['emconf_author_email'].'|'.$extRepEntry['emconf_author_company'])?' style="background-color:red;"':'').'>
					'.(is_array($extRepEntry)?'
					<input type="checkbox" value="1" name="'.$fN.'[setUserdatInRep]" title="Transfer typo3.org user data to most recent version in repository (on save)."> --&gt;
					':'').'
				</td>
				<td width="200px;" nowrap>'.(is_array($extRepEntry)?
						htmlentities($extRepEntry['emconf_author']).'&nbsp;<BR>'.
						htmlentities($extRepEntry['emconf_author_email']).'&nbsp;<BR>'.
						htmlentities($extRepEntry['emconf_author_company'])
					:"N/A").'</td>
			</tr>';

				// upload_password:
			$editTable[] = '<tr>
				<td'.$this->pi_classParam('HCell').'>Upload password:</td>
				<td colspan=3><input type="password" name="'.$fN.'[editExt][upload_password]" value="'.htmlentities($this->internal['currentRow']['upload_password']).'" style="width: 200px;"></td>
			</tr>';

				// Status notepad:
			$editTable[] = '<tr>
				<td'.$this->pi_classParam('HCell').'>Status notepad:<BR>(b,i,u,a,h3,ul,ol,li)</td>
				<td colspan=3><textarea style="width: 400px;" rows="5" name="'.$fN.'[editExt][tx_extrepmgm_homepage]">'.htmlspecialchars(trim($this->internal['currentRow']['tx_extrepmgm_homepage'])).'</textarea></td>
			</tr>';

				// Status notepad:
			$editTable[] = '<tr>
				<td'.$this->pi_classParam('HCell').'>Flags:</td>
				<td colspan=3>
					<input type="checkbox" name="'.$fN.'[editExt][tx_extrepmgm_flags][0]" value="1"'.(($this->internal['currentRow']['tx_extrepmgm_flags']&1)?" CHECKED":"").'>Don\'t show in EM for non-members<BR>
					<input type="checkbox" name="'.$fN.'[editExt][tx_extrepmgm_flags][1]" value="1"'.(($this->internal['currentRow']['tx_extrepmgm_flags']&2)?" CHECKED":"").'>Allow annotation of manual by members only!<BR>
					<input type="checkbox" name="'.$fN.'[editExt][tx_extrepmgm_flags][2]" value="1"'.(($this->internal['currentRow']['tx_extrepmgm_flags']&4)?" CHECKED":"").'>Disable Forum/Support?<BR>
				</td>
			</tr>';

				// Mentor Review:
			if ($GLOBALS['TSFE']->fe_user->user['tx_extrepmgm_isreviewer'])	{
				$editTable[] = '<tr>
					<td'.$this->pi_classParam('HCell').'>Disable Mentor Review:</td>
					<td colspan=3><input type="hidden" name="'.$fN.'[editExt][tx_extrepmgm_appr_flag]" value="0">
						<input type="checkbox" name="'.$fN.'[editExt][tx_extrepmgm_appr_flag]" value="1"'.($this->internal['currentRow']['tx_extrepmgm_appr_flag']?" CHECKED":"").'><BR>(You can disable reviews only because you are a reviewer yourself.)
					</td>
				</tr>';
			}

				// CATEGORIES:
				// finding selected cats for this extension:
			$opt=array();
			$query = 'SELECT * FROM tx_extrep_keytable_tx_extrepmgm_group_mm WHERE
						tx_extrep_keytable_tx_extrepmgm_group_mm.uid_local = '.intval($this->internal['currentRow']['uid']);
			$selCat=array();
			$res = mysql(TYPO3_db,$query);
			echo mysql_error();
			while($row=mysql_fetch_assoc($res))	{
				$selCat[$row['uid_foreign']]=1;
				$opt[$row['uid_foreign']]='';
			}

				// Selecting categories and displaying category table:
			$query = 'SELECT * FROM tx_extrepmgm_extgroup WHERE pid='.intval($this->dbPageId).
						$this->cObj->enableFields('tx_extrepmgm_extgroup').
						' ORDER BY title';
			if (count($opt))	$opt[0]='<option></option>';
			$res = mysql(TYPO3_db,$query);
			echo mysql_error();
			while($row=mysql_fetch_assoc($res))	{
				$opt[$row['uid']]='<option value="'.$row['uid'].'"'.($selCat[$row['uid']]?' SELECTED':'').'>'.htmlspecialchars($row['title']).'</option>';
			}
			$editTable[] = '<tr>
				<td'.$this->pi_classParam('HCell').'>Categories:</td>
				<td colspan=3>
				<select name="'.$fN.'[setCategories][]" multiple size=5>'.implode('',$opt).'</select>
				</td>
			</tr>';



			// STATE:
			$opt=array();
			$opt[]='<option value="">'.htmlspecialchars('Not Available').'</option>';
			reset($this->states);
			while(list($stateKey,$stateName)=each($this->states))	{
				$opt[]='<option value="'.$stateKey.'"'.(!strcmp($stateKey,$extRepEntry['emconf_state'])?' SELECTED':'').'>'.htmlspecialchars($stateName).'</option>';
			}

			$editTable[] = '<tr>
				<td'.$this->pi_classParam('HCell').'>State:</td>
				<td></td>
				<td></td>
				<td width="200px;"><select name="'.$fN.'[setState]">'.implode('',$opt).'</select></td>
			</tr>';





				// MEMBERS:
			$memberArray=array();
			$query = 'SELECT fe_users.* FROM tx_extrep_groupmem_mm,fe_users WHERE
						tx_extrep_groupmem_mm.uid_local = '.intval($this->internal['currentRow']['uid']).'
						AND tx_extrep_groupmem_mm.uid_foreign=fe_users.uid'.
						$this->cObj->enableFields('fe_users').
						' ORDER BY tx_extrep_groupmem_mm.sorting'
						;
			$res = mysql(TYPO3_db,$query);
			echo mysql_error();
			while($row=mysql_fetch_assoc($res))	{
				$memberArray[$row['uid']]=$row;
				$memberArray[$row['uid']]['_MEM']=1;
			}
				// Looking up potential users for membership:
			if ($this->piVars['DATA']['lookup'])	{
				$LU_query = $this->cObj->searchWhere($this->piVars['DATA']['lookup'],'uid,username,name,email,company,city,country','fe_users');
				$notIn = array_keys($memberArray);
				$query = 'SELECT * FROM fe_users WHERE '.
					' pid='.intval($this->dbPageId).
					(count($notIn) ? ' AND uid NOT IN ('.implode(',', $notIn).')' : '').
					' AND uid!='.intval($GLOBALS['TSFE']->fe_user->user['uid']).
					$LU_query.
					$this->cObj->enableFields('fe_users').
					' ORDER BY name,username'.
					' LIMIT 30';

#				debug(array($LU_query));

				$res = mysql(TYPO3_db,$query);
				echo mysql_error();
				while($row=mysql_fetch_assoc($res))	{
					$memberArray[$row['uid']]=$row;
				}
			}
#debug($memberArray);
			$formLines=array();
			reset($memberArray);
			while(list($mUid,$mDat)=each($memberArray))	{
				$formLines[]='<input type="checkbox" name="'.$fN.'[mem][]" value="'.$mUid.'"'.($mDat['_MEM']?" CHECKED":"").'>'.$mDat['username'].' ('.htmlentities(trim($mDat['name'])).', '.htmlentities(trim($mDat['email'])).')';
			}

			if ($this->internal['currentRow']['members_only'])	{	// Can only disable members-only
				$editTable[] = '<tr>
					<td'.$this->pi_classParam('HCell').'>Members only:</td>
					<td colspan=3><input type="hidden" name="'.$fN.'[editExt][members_only]" value="0">
						<input type="checkbox" name="'.$fN.'[editExt][members_only]" value="1"'.($this->internal['currentRow']['members_only']?" CHECKED":"").' onClick="alert(unescape(\''.rawurlencode('When you disable members-only the extension will be publicly available and you cannot by yourself make it members only again.').'\'));">
					</td>
				</tr>';
			}
			$editTable[] = '<tr>
				<td'.$this->pi_classParam('HCell').'>Members:</td>
				<td colspan=3 nowrap>'.(count($formLines)?implode('<BR>',$formLines):"<em>None</em>").'
					<hr>
					Lookup users:<BR>
					<input type="text" name="'.$fN.'[lookup]">
				</td>
			</tr>';


				// REPOSITORY
			$query = 'SELECT uid,version,upload_counter,upload_comment,private_key,emconf_private,emconf_download_password,tx_extrepmgm_appr_status,tx_extrepmgm_appr_comment,tx_extrepmgm_appr_fe_user,download_counter,crdate,upload_counter,datasize_gz,datasize,last_upload_date FROM tx_extrep_repository WHERE
						extension_uid = '.intval($this->internal['currentRow']['uid']).
						$this->cObj->enableFields('tx_extrep_repository').
						' ORDER BY version_int DESC';
			$formLines=array();
			$formLines[]='<tr bgcolor="#eeeeee">
					<td'.$this->pi_classParam('HCell').'>&nbsp;</td>
					<td'.$this->pi_classParam('HCell').'>Version:</td>
					<td'.$this->pi_classParam('HCell').'>Priv.</td>
					<td'.$this->pi_classParam('HCell').'>DL pass:</td>
					<td'.$this->pi_classParam('HCell').'>Upload cmt:</td>
					<td'.$this->pi_classParam('HCell').'>Upl.Date</td>
					<td'.$this->pi_classParam('HCell').'># UL.</td>
					<td'.$this->pi_classParam('HCell').'># DL.</td>
					<td'.$this->pi_classParam('HCell').'>Review</td>
					<td'.$this->pi_classParam('HCell').'>Size:</td>
				</tr>';
			$res = mysql(TYPO3_db,$query);
			echo mysql_error();
			$cc=0;
			$sizeFreed=0;
			while($row=mysql_fetch_assoc($res))	{
				$checkbox='<input type="checkbox" name="'.$fN.'[rep][]" value="'.$row['uid'].'" CHECKED>';
				if ($cc==0)	$checkbox='';	// First
				if ($extRepEntry['uid']==$row['uid'])	$checkbox='';	// First publicly available
				if ($row['tx_extrepmgm_appr_fe_user'])	$checkbox='';	// Reviewed
				$formLines[]='<tr>
						<td>'.($checkbox?$checkbox:'&nbsp;').'</td>
						<td>'.$row['version'].'</td>
						<td align="center">'.($row['emconf_private']?'YES':'&nbsp;').'</td>
						<td>'.($row['emconf_download_password']?$row['emconf_download_password']:'&nbsp;').'</td>
						<td>'.($row['upload_comment']?$row['upload_comment']:'&nbsp;').'</td>
						<td nowrap>'.date('d-m-Y',$row['last_upload_date']).'</td>
						<td align="center">'.($row['upload_counter']>1?$row['upload_counter']:"-").'</td>
						<td align="center">'.($row['download_counter']?$row['download_counter']:"-").'</td>
						<td>'.$this->getIcon_review($row,$this->internal['currentRow'],2,1).'</td>
						<td nowrap>'.(t3lib_div::formatSize($row['datasize']).'/'.t3lib_div::formatSize($row['datasize_gz'])).'</td>
					</tr>';
#debug($row);
				if ($checkbox)	$sizeFreed+=$row['datasize_gz'];
				$cc++;
			}
			$editTable[] = '<tr>
				<td'.$this->pi_classParam('HCell').'>Versions:</td>
				<td colspan=3>
					'.($checkbox?'
					<span style="background-color:red;"><input type="checkbox" name="'.$fN.'[rep_delete]" value="1" onClick="alert(unescape(\''.rawurlencode("If you tick of this checkbox\nALL versions below with their respective checkbox set\nwill be deleted permanently when you save the settings.").'\'));"><strong>DELETE!</strong></span><br>
					If - and only if - you set this red "DELETE" checkbox, all ticked-off versions below will be permanently deleted from the repository<BR>
					By deleting old versions you\'ll free up <strong>'.t3lib_div::formatSize($sizeFreed).'bytes</strong> of space in the repository! Thank you.<BR><BR>':'').'
					'.(count($formLines) ?
							'<table '.$this->conf['displayExt.']['tableParams_versionTable'].$this->pi_classParam('vTbl').'>'.implode('',$formLines).'</table>
								<input type="checkbox" name="'.$fN.'[new_dev]" value="1" onClick="alert(unescape(\''.rawurlencode("This will create a copy of the current repository version, increase the version number with 0.0.1\n and thereby the extension will appear in the TYPO3 Extension Manager as an updated version.").'\'));">Create a new dev-version based on current.
							' :
							'<em>None</em>'
						).'
				</td>
			</tr>';


				// Merge Translations:
			$this->ext_langInfo = $this->getLanguagesAndTranslators();
			$langStat = $this->getLangStatVisual($this->internal['currentRow']);
			if (is_array($langStat))	{
				$infoArr_dat = unserialize($this->internal['currentRow']['tx_extrepmgm_cache_infoarray']);
#debug($infoArr_dat);
				$totalCheckBox=0;
				reset($langStat);
				while(list($lK,$flag)=each($langStat))	{
					$thisStatus = $infoArr_dat['translation_status'][$lK];
					$displayCheckBox = !$thisStatus['non_chief_count'] && $thisStatus['chief_count'];
					if ($displayCheckBox)	$totalCheckBox++;
					$langStat[$lK]= '<tr><td>'.$langStat[$lK].'</td><td>'.
						($displayCheckBox ? 	// There cannot be any non-chief labels set and chief MUSt have set at least some labels...
							'<input type="checkbox" name="'.$fN.'[merge_translation_set][]" value="'.$lK.'" checked>' :
							'<input type="checkbox" name="'.$fN.'[merge_translation_set][]" value="'.$lK.'">').'</td><td>'.
						$this->ext_langInfo[0][$lK]['title'].'</td><td>'.($thisStatus['chief_count']?$thisStatus['chief_count']:"-").'</td></tr>';
				}

				$editTable[] = '<tr>
					<td'.$this->pi_classParam('HCell').'>Merge translations:</td>
					<td colspan=3>'.($totalCheckBox?'<span style="background-color:red;"><input type="checkbox" name="'.$fN.'[merge_translation]" value="1">Merge selected translations into a new dev-version number.</span><HR>':'').'
					<table border=0 cellpadding=0 cellspacing=1>'.implode('',$langStat).'</table>
					</td>
				</tr>';
			}



				// Open Office Manual, doc/manual.sxw
			$sxwfile='doc/manual.sxw';
			if ($extRepEntry['is_manual_included'])	{
				$cmp_content=$this->generateDocumentForm($extRepEntry,$sxwfile);
				if (!$cmp_content)	{
					$cmp_content = 'OOdoc content matches hash value for current TOC.<HR>';
					$cmp_content.= $this->generateTOCforMetaData($extRepEntry,$sxwfile,$this->piVars['DATA']['edit_toc_metadata']);
					$cmp_content.= '<BR><input type="checkbox" name="'.$fN.'[edit_toc_metadata]" value="1"'.($this->piVars['DATA']['edit_toc_metadata']?' CHECKED':'').'>Edit TOC meta data';

					$tocPHrec=$this->getTocPHElement($extRepEntry['extension_uid'],$sxwfile);

						// Set category
					$opt=array();
					reset($this->docCats);
					while(list($kk,$vv)=each($this->docCats))	{
						$opt[]='<option value="'.$kk.'"'.($kk==$tocPHrec['cat']?' SELECTED':'').'>'.htmlspecialchars($vv).'</option>';
					}
					$cmp_content.='<BR>Document Category: <select name="'.$fN.'[editTocPH][cat]">'.implode('',$opt).'</select>';

						// Set language
					$query = 'SELECT * FROM tx_extrepmgm_langadmin WHERE '.
							'pid='.intval($this->dbPageId).
							$this->cObj->enableFields('tx_extrepmgm_langadmin').
							' ORDER BY crdate';
					$res = mysql(TYPO3_db,$query);
						// For each language:
					$opt=array();
					$opt[]='<option value="0"></option>';
					while($lrow=mysql_fetch_assoc($res))	{
						$opt[]='<option value="'.$lrow['uid'].'"'.($lrow['uid']==$tocPHrec['lang']?' SELECTED':'').'>'.htmlspecialchars($lrow['title']).'</option>';
					}
					$cmp_content.='<BR>Document Language: <select name="'.$fN.'[editTocPH][lang]">'.implode('',$opt).'</select>';
				}
			} else {
				$cmp_content='Not available';

					// IF there is still a placeholder record for the manual sxw-file, then delete stuff related to this!
				$sxwfile='doc/manual.sxw';
				if ($masterEl = $this->getTocPHElement($extRepEntry['extension_uid'],$sxwfile))	{

						// And now clear toc-entries (don't delete since we don't want to loose relations from stuff... However we don't have anything yet to catch these broken relations when they occur...):
					$saved_toc = $this->getTOCfromSavedOOdoc($extRepEntry['extension_uid'],$sxwfile);
					reset($saved_toc);
					while(list($st_uid,$st_rec)=each($saved_toc))	{
						$this->updateInsertTOCEntry($extRepEntry['extension_uid'],$sxwfile,'', $st_uid);
					}

						// Remove the old 1) temp-file and 2) oodoc in database.
					$this->cleanUpOldOOdoc($masterEl['cur_oodoc_ref'], $masterEl['cur_tmp_file']);
// (robert 29.10.04) DISABLED for performance reasons until we find a better solution:
//					$this->clearPageCacheForExtensionDoc($extRepEntry['extension_uid']);
					$this->clearPageCacheForExtensionDoc(0);
					$editTable['toccachewarning'] = '<tr><td colspan="4"><strong>Your document has been updated.</br ><span  style="color:red">For performance reasons, it might take some hours until your changes are visible on typo3.org! However, we are working on an improved caching management.</span></strong></td></tr>';

						// Set master record
					$this->deleteTocPHElement($extRepEntry['extension_uid'],$sxwfile);
					$this->clearOOsliceCache($extRepEntry['extension_uid']);

					$cmp_content.= '<BR>Clearing out old content now...!';
				}
			}
			$editTable[] = '<tr>
				<td'.$this->pi_classParam('HCell').'>doc/manual.sxw:</td>
				<td colspan=3>'.$cmp_content.'</td>
			</tr>';




			$editTable[] = '<tr>
				<td'.$this->pi_classParam('HCell').'>&nbsp;</td>
				<td colspan=3>
					<input type="submit" name="_" value="Execute">
					<input type="submit" name="_2" value="Cancel" onClick="document.location=unescape(\''.rawurlencode($GLOBALS['TSFE']->baseUrl.$backLink_url).'\'); return false;">
					<input type="submit" name="_3" value="Redraw without save" onClick="document.location=unescape(\''.rawurlencode(t3lib_div::getIndpEnv("REQUEST_URI")).'\'); return false;">
				</td>
			</tr>';

			$content='';
			$content.='<form action="'.t3lib_div::getIndpEnv('REQUEST_URI').'" method="post" style="margin: 0px 0px 0px 0px;" name="editForm">
				<table  '.$this->conf['displayExt.']['tableParams'].$this->pi_classParam('dTbl').'>
				'.implode(chr(10),$editTable).'
				</table>
			</form>';
		}

		return $content;
	}

	/**
	 * Feedback form
	 *
	 * @return	[type]		...
	 */
	function feedback()	{
			// Admin info:
		$adminRec = $this->pi_getRecord('fe_users',$this->internal['currentRow']['owner_fe_user']);

		if (is_array($adminRec))	{
			$defMsg='Hi '.$adminRec['name'].'

...

Best regards
'.$GLOBALS['TSFE']->fe_user->user['name'].' ('.$GLOBALS['TSFE']->fe_user->user['username'].')';

			if (is_array($this->piVars['DATA']) && trim($this->piVars['DATA']['comment']) && trim($this->piVars['DATA']['sender_email']) && strcmp(trim(ereg_replace('[[:space:]]','',$defMsg)),trim(ereg_replace('[[:space:]]','',$this->piVars['DATA']['comment']))))	{
				if (t3lib_div::validEmail($this->piVars['DATA']['sender_email']))	{
					$msg='TER feedback - '.$this->internal['currentRow']['extension_key'].
						chr(10).trim($this->piVars['DATA']['comment']);
					$this->cObj->sendNotifyEmail($msg, $adminRec['email'], 'kasper2004@typo3.com', $this->piVars['DATA']['sender_email'], $this->piVars['DATA']['sender_name']);

					$content='<h3>Email sent</h3>';
					$content.='<p>The feedback was sent to '.$adminRec['name'].' / '.$this->cObj->gettypolink($adminRec['email'],$adminRec['email']).'</p>';
#					debug($this->piVars['DATA']);
				} else $content='<p>ERROR: You did not enter a valid email address ('.$this->piVars['DATA']['sender_email'].')</p>';
			} else {
				$content.='<h3>Feedback to the author</h3>';
				$content.='<p>Use this form to return <b>feedback</b> including <b>bug reports</b> to the author ('.$adminRec['name'].') of this extension. Remember that kind words are a special kind of fuel for the people donation time and talent to an Open Source project like TYPO3. So be kind, constructive, encouraging, but honest at the same time.</p>';
				$content.='<p>Please don\'t try to flatter the author for the sole purpose of getting some support for free. That is not the point here.</p>';
				$content.='<form action="'.t3lib_div::getIndpEnv('REQUEST_URI').'" method="POST" style="margin: 0px 0px 0px 0px;">

				<BR>
					<p><strong>Your name:</strong></p>
					'.($GLOBALS['TSFE']->loginUser ?
						'<input type="hidden" name="'.$this->prefixId.'[DATA][sender_name]" value="'.htmlspecialchars($GLOBALS['TSFE']->fe_user->user['name'].' ('.$GLOBALS['TSFE']->fe_user->user['username']).')"><p>'.htmlspecialchars($GLOBALS['TSFE']->fe_user->user['name'].' ('.$GLOBALS['TSFE']->fe_user->user['username'].')').'</p>' :
						'<input type="text" name="'.$this->prefixId.'[DATA][sender_name]" style="width: 400px;"><BR>').'<BR>

					<p><strong>Your email:</strong></p>
					'.($GLOBALS['TSFE']->loginUser ?
						'<input type="hidden" name="'.$this->prefixId.'[DATA][sender_email]" value="'.htmlspecialchars($GLOBALS['TSFE']->fe_user->user['email']).'"><p>'.htmlspecialchars($GLOBALS['TSFE']->fe_user->user['email']).'</p>' :
						'<input type="text" name="'.$this->prefixId.'[DATA][sender_email]" style="width: 400px;"><BR>').'<BR>

					<p><strong>Your comment:</strong></p>
					<textarea rows="5" name="'.$this->prefixId.'[DATA][comment]" style="width: 400px;">'.$defMsg.'</textarea><br><br>


					<input type="submit" value="Send feedback">
				</form>';

			}
		} else $content='<p>ERROR: There was no owner for this extension!</p>';
		return $content;
	}

	/**
	 * Makes the interface for translation of an extension
	 *
	 * @return	[type]		...
	 */
	function translateExtension()	{
		if ($row = $this->getLatestRepositoryEntry($this->internal['currentRow']['uid']))	{
			$datStr = gzuncompress($row['datablob']);
			if (md5($datStr)==$row['datablob_md5'])	{
				$dB = unserialize($datStr);
					// Finding locallang.php files + manual HTML.
				$LL=array();
				while(list($file)=each($dB))	{
					if ($dB[$file]['LOCAL_LANG'])	{
						$LL[]=$dB[$file];
					}
				}
				$langInfo = $this->getLanguagesAndTranslators();
#debug($langInfo);
					// **************************************
					// ***** DOING TRANSLATION OF A FILE:
					// **************************************
#debug($this->piVars);
				if ($this->piVars['doT'])	{
					if ($GLOBALS['TSFE']->fe_user->user['uid'])	{
						$content='';
						list($fIdx,$lK) = explode('|',$this->piVars['doT']);

						$langRec = $langInfo[0][$lK];

						if (is_array($langRec))	{
								// First, get reference languages if any is entered:
							$temp_refLangs = t3lib_div::trimExplode(',',strtolower($langRec['ref_lang_keys']),1);
							$refLangs=array();
							foreach($temp_refLangs as $val_rl)	{
									// Set true/false whether a copy-link should be provided or not:
								$refLangs[str_replace('*','',$val_rl)]=strstr($val_rl,'*')?1:0;
							}
								// Then, get the file content for the file we are editing ($fIdx)
							$LLr=$LL[$fIdx];
							if (is_array($LLr))	{
									// Getting current array from stored extension, cleaned up:
								$LLarr = unserialize($LLr['LOCAL_LANG'][1]);

									// Importing possible external arrays:
								$temp_refLangs = $refLangs;
								$temp_refLangs[$lK]=1;
								foreach($temp_refLangs as $key_rL => $temptemp)	{
									if ($key_rL!='default' && isset($langInfo[0][$key_rL]) && is_string($LLarr[$key_rL]) && $LLarr[$key_rL]=='EXT')		{
										$fParts = t3lib_div::revExplode('.',$LLr['name'],2);
										$fileName = $fParts[0].'.'.$key_rL.'.'.$fParts[1];
										foreach($LL as $tempLL)	{
											if ($tempLL['name']==$fileName)	{
												$tempLLarr = unserialize($tempLL['LOCAL_LANG'][1]);
												$LLarr[$key_rL] = $tempLLarr[$key_rL];
											}
										}
									}
								}
#					debug($LLarr);

									// Clean up the current array (something with no labels in other languages which are not in the default...)
								$LLarr = $this->cleanUpLLArray($LLarr,$langInfo[0]);
#debug($LLarr);
									// Getting authentication and possibly saving incoming data:
								$auth = $langRec['auth_translator']==$GLOBALS['TSFE']->fe_user->user['uid'] || isset($langInfo[1][$langRec['langkey'].'_'.$GLOBALS['TSFE']->fe_user->user['uid']]);
								if ($auth)	{
									$this->translateSaveIncoming($GLOBALS['TSFE']->fe_user->user['uid'], $lK, $LLr['name'], $this->internal['currentRow']['extension_key'],$LLarr);
									if (is_array($this->piVars['DATA']))	{
										$this->updateExtKeyCache($this->internal['currentRow']['uid']);
#debug('update extension...');
									}
								}

									// Getting stored translations from admin, assistants (and self?)
								list($LLarr_comp_users, $assist_arr, $chief_arr, $user_list)	= $this->getCompiledTranslations($langInfo[2][$lK],$langRec['auth_translator'],$this->internal['currentRow']['extension_key'],$lK,$LLr['name']);
#debug(array($LLarr_comp_users, $assist_arr, $chief_arr, $user_list));
#debug($LLarr[$lK]);
								$LLarr_comp=t3lib_div::array_merge($LLarr[$lK],$LLarr_comp_users);
								$rev_assist_arr = array_reverse($user_list);
#								$dat=$this->getDataContent($GLOBALS['TSFE']->fe_user->user['uid'],$this->internal['currentRow']['extension_key'],$lK);
#debug($dat);
#debug($assist_arr);
#debug($rev_assist_arr);
#debug($dat);
#debug($LLarr_comp);
#debug($LLarr_comp_users);
								$header='Translating '.$LLr['name'].' to '.$langRec['title'].' ('.$langRec['langkey'].')';
								$content='';

								$ofTr_rec=$this->pi_getRecord('fe_users',$langRec['auth_translator']);
								if (is_array($ofTr_rec))	{
									$ofTr=$this->cObj->getTypoLink($ofTr_rec['name'].' ('.$ofTr_rec['username'].')',$ofTr_rec['email']);
								} else {
									$ofTr='<em>[No chief translator available currently!]</em>';
								}

								$content.= $langRec['auth_translator']==$GLOBALS['TSFE']->fe_user->user['uid'] ?
											'<p><strong>'.$GLOBALS['TSFE']->fe_user->user['username'].'</strong>, you are the <strong>official translator-in-charge</strong> of the "'.$langRec['langkey'].'" language. Your translation is the only one the extension owner can apply to the extension so do your best! Thanks for your work!</p>' :
											(isset($langInfo[1][$langRec['langkey'].'_'.$GLOBALS['TSFE']->fe_user->user['uid']]) ?
												'<p><strong>'.$GLOBALS['TSFE']->fe_user->user['username'].'</strong>, you are an <strong>assisting translator</strong> of the "'.$langRec['langkey'].'" language. Your translation will be evaluated by the chief translator, '.$ofTr.', and probably it will be very helpful to him if you do your best!</p>':
												'<p><strong>'.$GLOBALS['TSFE']->fe_user->user['username'].'</strong>, you have no official position as a translator of the "'.$langRec['langkey'].'" so you cannot make translations. If you want to be an assistant translator helping out with the official translation please ask '.$ofTr.' to be approved for that!</p>');

								$charset=trim($langRec['charset']) ? trim($langRec['charset']) : 'ISO-8859-1';

								$content.='<p><span style="background-color: #cccccc;"><strong>'.$this->pi_linkTP_keepPIvars('Go back',array('doT'=>'','debug'=>'')).'</strong></span></p>';
								$content.='<pre>'.htmlentities(trim(str_replace('<?php','',$LLr['LOCAL_LANG'][0]))).'</pre>';
								$content.='<p>Charset used: <strong>'.$charset.'</strong></p>';


									// Rendering the translation forms:
								$rows=array();

								if (is_array($LLarr['default']))	{
										// Add extra ref languages to title bar:
									$extraReferences='';
									foreach($refLangs as $key_rL => $val_rL)	{
										$extraReferences.='<td><strong>'.$langInfo[0][$key_rL]['title'].'</strong></td>';
									}

										// Set header of table
									$rows[]='<tr bgcolor="#cccccc">
										<td bgcolor="#cccccc"><strong>key</strong></td>
										<td><strong>English (default) text:</strong></td>
										'.$extraReferences.'
										<td><strong>'.$langRec['title'].' ('.$langRec['langkey'].') translation:</strong></td>
										<td>&nbsp;</td>
										</tr>';

										// Then, traverse all default labels:
									reset($LLarr['default']);
									while(list($kk,$vv)=each($LLarr['default']))	{
										if (substr($kk,0,1)!='_')	{
											$fN = $this->prefixId.'[DATA]['.$lK.']['.$kk.']';
											$msg=array();
											if ($auth) {
												if (strstr($vv,chr(10)))	{
													$formField='<textarea name="'.$fN.'" rows="'.(count(explode(chr(10),$vv))+1).'" style="width:400px;" wrap="OFF">'.t3lib_div::formatForTextarea($LLarr_comp[$kk]).'</textarea>';
												} else {
													$formField='<input style="width:400px;" name="'.$fN.'" value="'.htmlspecialchars($LLarr_comp[$kk]).'">';
												}

												$bg='';
												if (isset($LLarr_comp_users[$kk]))	{
														// Finding out if this current user does have access to change this value or if it has been overridden by someone higher in hierarchy...
													$this_user_id = $GLOBALS['TSFE']->fe_user->user['uid'];
													$thisUserIsOverridding=0;
													$someOneElse=0;
													reset($rev_assist_arr);
													while(list(,$au_uid)=each($rev_assist_arr))	{
															// If this current user is the one with the most recent entry, then fear not ...
														if ($au_uid==$this_user_id)	$thisUserIsOverridding=1;
														if ($au_uid!=$this_user_id && isset($assist_arr[$au_uid][$kk])) {
															if (!$thisUserIsOverridding)	{	// ... because that user will not be overridden.
																$bg=' bgcolor="'.$this->conf['displayExt.']['translation_higher_priority'].'"'; $msg[]='Overridden by higher priority ('.$this->getUserName($au_uid).').';
																$formField=nl2br(htmlspecialchars($LLarr_comp[$kk])).(isset($assist_arr[$this_user_id][$kk])?'<input type="hidden" name="'.$fN.'" value="'.htmlspecialchars($assist_arr[$this_user_id][$kk]).'">':'');
															}
															$someOneElse=$au_uid;
															break;
														}
													}
													if (!$bg)	{
														if (strcmp($LLarr_comp[$kk],$assist_arr[$this_user_id][$kk]))	{
															$bg=' bgcolor="'.$this->conf['displayExt.']['translation_changed_by_someelse'].'"'; $msg[]='Changed by some one else ('.$this->getUserName($someOneElse).').';
														} else {
															$bg=' bgcolor="'.$this->conf['displayExt.']['translation_changed_by_you'].'"'; $msg[]='Changed by you!';
														}
													}
												} elseif (!strcmp($LLarr[$lK][$kk],''))	{$bg=' bgcolor="'.$this->conf['displayExt.']['translation_color_missing'].'"'; $msg[]='Empty in current ext.'; }		// This field is empty in the original
											} else {
												$formField=nl2br(htmlspecialchars($LLarr[$lK][$kk]));
											}

												// Add extra ref languages to title bar:
											$extraReferences='';
											foreach($refLangs as $key_rL => $val_rL)	{
												if ($val_rL) {	// Draw MOVE arrow
													$onclick="document.forms[0]['".$fN."'].value=unescape('".rawurlencode($LLarr[$key_rL][$kk])."');";
													$move='<a href="#" onclick="'.$onclick.'" style="color: black;"><strong>[C]</strong></a> ';
												} else $move='';
												$testValue = $LLarr[$key_rL][$kk];
												$testCharset=trim($langInfo[0][$key_rL]['charset']) ? trim($langInfo[0][$key_rL]['charset']) : 'ISO-8859-1';
												if (strtolower($testCharset) != strtolower($charset))	{
													$testValue = $GLOBALS['TSFE']->csConvObj->conv($testValue,strtolower($testCharset),strtolower($charset),1);
												}

												$extraReferences.='<td>'.$move.nl2br(t3lib_div::deHSCentities(htmlspecialchars($testValue))).'</td>';
											}

											$rows[]='<tr bgcolor="'.$this->conf['displayExt.']['translation_color_ok'].'">
												<td bgcolor="#cccccc">['.htmlspecialchars($kk).']</td>
												<td>'.nl2br(htmlspecialchars($vv)).'</td>
												'.$extraReferences.'
												<td'.$bg.'>'.$formField.'</td>
												<td>'.implode('<br>',$msg).'</td>
												</tr>';
										}
									}
								}

								$content.='<form action="'.htmlspecialchars(t3lib_div::getIndpEnv('REQUEST_URI')).'" method="post" style="margin: 0px 0px 0px 0px;"><table border=0 cellpadding=0 cellspacing=1>
									'.implode('',$rows).'
									</table>
									'.($auth?'<input type="submit" name="_" value="Save values">':'').'
								</form>';

								$content.=$this->pi_linkTP_keepPIvars('(debug)',array('debug'=>1));
								if ($this->piVars['debug'])		{
									$debug_ref=mysql(TYPO3_db,'SELECT * FROM tx_extrepmgm_langelements
										WHERE fe_user='.intval($GLOBALS['TSFE']->fe_user->user['uid']).'
										AND extension_key="'.$this->internal['currentRow']['extension_key'].'"
										AND langkey="'.addslashes($lK).'"');

									while($debug_row=mysql_fetch_assoc($debug_ref))	{
										$content.=t3lib_div::view_array(array($debug_row['deleted_tstamp'],unserialize($debug_row['data_content'])));
									}
								}

								$content='<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
									<html>
									<head>
										<meta http-equiv="Content-Type" content="text/html; charset='.$charset.'">
										<title>'.htmlentities($header).'</title>
										<style>
										P {font-family: verdana, arial; font-size:11px;}
										H2 {font-family: verdana, arial; font-size:16px; background-color: #eeccff;}
										TD {font-family: verdana, arial; font-size:11px; padding: 0px 4px 0px 4px;}
										INPUT {font-family: verdana, arial; font-size:11px;}
										TEXTAREA {font-family: verdana, arial; font-size:11px;}
										</style>
									</head>
									<body>
									<h3>'.$header.'</h3>
									'.$content.'
									</body>
									</html>
									';
									echo $content;
									exit;
							} else $content.='<p>Error: Couldn\'t find file.</p>';
						} else $content.='<p>Error: Couldn\'t find language record.</p>';
					} else $content.='<p>You were not logged in!</p>';
					$content.='<p>'.$this->pi_linkTP_keepPIvars('Cancel',array('doT'=>'')).'</p>';
				} else {

					// **************************************
					// ***** MENU OF FILES TO TRANSLATE:
					// **************************************

	#				debug($langInfo);
					$menu=array();

					if (count($LL))	{
						$translateStat = $this->makeTranslationStatus($this->internal['currentRow']['uid'], $row, $langInfo);
#debug($translateStat);
							// Making header row:
						$cells=array();
						$cells[]='<td>Filename:</td>';
						$cells[]='<td>#Labels:</td>';
						reset($langInfo[0]);
						while(list($kk,$lR)=each($langInfo[0]))	{
							if ($GLOBALS['TSFE']->fe_user->user['uid'])	{
								$state = $langInfo[0][$kk]['_state'] = $lR['auth_translator']==$GLOBALS['TSFE']->fe_user->user['uid'] ? 'ADMIN' : (isset($langInfo[1][$lR['langkey'].'_'.$GLOBALS['TSFE']->fe_user->user['uid']]) ? 'SUB': '');
								if ($state)	$state='<BR>('.$state.')';
							}
							$cells[]='<td nowrap><span title="'.$lR['title'].'">'.$lR['langkey'].$state.'</span></td>';
						}
						$menu[]='<tr '.$this->pi_classParam('HRow').'>'.implode('',$cells).'</tr>';

							// Init counters:
						$totalCount=0;
						$totalSize=0;

							// Traversing all files for translation:
						foreach($LL as $fIdx => $LLr)	{
								// Check, if the locallang file has a lang key before the .php extension:
							$reg=array();
							ereg('^locallang_.*\.([a-z]+)\.php',$LLr['name'],$reg);

								// If no lang key extension OR if it is not found in the language records:
							if (!$reg[1] || !isset($langInfo[0][$reg[1]]))	{

								$LLarr = unserialize($LLr['LOCAL_LANG'][1]);

									// Importing possible external arrays:
								foreach($langInfo[0] as $key_rL => $temptemp)	{
									if ($key_rL!='default' && is_string($LLarr[$key_rL]) && $LLarr[$key_rL]=='EXT')		{
										$fParts = t3lib_div::revExplode('.',$LLr['name'],2);
										$fileName = $fParts[0].'.'.$key_rL.'.'.$fParts[1];
										foreach($LL as $tempLL)	{
											if ($tempLL['name']==$fileName)	{
												$tempLLarr = unserialize($tempLL['LOCAL_LANG'][1]);
												$LLarr[$key_rL] = $tempLLarr[$key_rL];
											}
										}
									}
								}

								$LLarr = $this->cleanUpLLArray($LLarr,$langInfo[0]);

								$totalCount+= $baseCount = is_array($LLarr['default']) ? count($LLarr['default']) : -1;
								$totalSize+= is_array($LLarr['default']) ? strlen(serialize($LLarr['default'])) : 0;

								$cells=array();
								$cells[]='<td'.$this->pi_classParam('HCell').'>'.$LLr['name'].'</td>';
								$cells[]='<td>'.$baseCount.'</td>';

								reset($langInfo[0]);
								while(list($lK,$lR)=each($langInfo[0]))	{
									if ($baseCount==-1)	{
										$cells[]='<td><span style="color:red;">Err.</span></td>';
									} elseif ($baseCount==0)	{
										$cells[]='<td>-</td>';
									} else {
										$bg='';
										if (is_array($LLarr[$lR['langkey']]))	{
											list($cur_count, $missing_count, $non_chief_count, $chief_count) = $translateStat['files'][$LLr['name']][$lK];

											if ($lR['_state'])	{
												if ($missing_count>0)	{
													$bg=' bgcolor="'.$this->conf['displayExt.']['translation_color_missing'].'"';
												} elseif ($non_chief_count) {
													$bg=' bgcolor="'.$this->conf['displayExt.']['translation_changed_by_someelse'].'"';
												} else {
													$bg=' bgcolor="'.$this->conf['displayExt.']['translation_color_ok'].'"';
												}
											}
											$content = $this->pi_linkTP_keepPIvars(''.($missing_count||$cur_count||$non_chief_count ? $cur_count.'/'.$missing_count.'/'.$non_chief_count : 'x'.($chief_count?' ('.$chief_count.')':'')),array('doT'=>$fIdx.'|'.$lR['langkey']));
										} else {
											$content = 'Err';
										}
										$cells[]='<td'.$bg.'>'.$content.'</td>';
									}
								}

								$menu[]='<tr>'.implode('',$cells).'</tr>';
							}
						}


						$menu[]='<tr><td colspan="'.(count($langInfo[0])+2).'">&nbsp;</td></tr>';

						// Bottom line:
						$cells=array();
						$cells[]='<td'.$this->pi_classParam('HCell').'>Bottomline:</td>';
						$cells[]='<td>'.$totalCount.'</td>';

						reset($langInfo[0]);
						while(list($lK,$lR)=each($langInfo[0]))	{
							$bg='';
							if (is_array($LLarr[$lR['langkey']]))	{
								$missing_count = $translateStat['lang'][$lK]['missing_count'];
								$non_chief_count = $translateStat['lang'][$lK]['non_chief_count'];
								$cur_count = $translateStat['lang'][$lK]['cur_count'];

								if ($lR['_state'])	{
									if ($missing_count>0)	{
										$bg=' bgcolor="'.$this->conf['displayExt.']['translation_color_missing'].'"';
									} elseif ($non_chief_count) {
										$bg=' bgcolor="'.$this->conf['displayExt.']['translation_changed_by_someelse'].'"';
									} else {
										$bg=' bgcolor="'.$this->conf['displayExt.']['translation_color_ok'].'"';
									}
								}
								$content = ''.($missing_count||$non_chief_count ? $cur_count.'/'.$missing_count.'/'.$non_chief_count : 'x');
							} else {
								$content = 'Err';
							}
							$cells[]='<td'.$bg.'>'.$content.'</td>';
						}
						$menu[]='<tr>'.implode('',$cells).'</tr>';

							// Output everything:
						$content='';
						$content.='<p>Language File Matrix: </p>';
						$content.='<table '.$this->conf['displayExt.']['tableParams_translation'].$this->pi_classParam('lTbl').'>'.implode('',$menu).'</table><BR>';
						$content.='<p>Totals in english (default) langauges: <strong>'.$totalCount.' labels / '.$totalSize.' bytes</strong></p>';
		#				debug($LL);
					} else $content = '<p>There were no language files in this extension, so nothing to translate.</p>';
				}
			} else $content = '<p>Error: MD5 hash did not match.</p>';
		} else $content = '<p>Error: No repository entry was found.</p>';
		return $content;
	}

	/**
	 * Save translation.
	 *
	 * @param	[type]		$fe_user_uid: ...
	 * @param	[type]		$langkey: ...
	 * @param	[type]		$fileName: ...
	 * @param	[type]		$extKey: ...
	 * @param	[type]		$LLarr: ...
	 * @return	[type]		...
	 */
	function translateSaveIncoming($fe_user_uid, $langkey, $fileName, $extKey, $LLarr)	{
			// If something is submitted and every else is OK:
		if ($langkey && $fe_user_uid && $extKey)	{
				// Storing incoming??
			$langValues = $this->piVars['DATA'][$langkey];
			if (is_array($langValues))	{
				$dat=$this->getDataContent($fe_user_uid,$extKey,$langkey);

					// Cleaning incoming
				if (is_array($langValues))	{
					reset($langValues);
					while(list($k,$v)=each($langValues))	{
						$langValues[$k]=trim($v);
							// Removing incoming value if 1) it's empty, 2) it matches the current AND is not in any case found in the array already.
						if (!strcmp($langValues[$k],'') || (!strcmp($langValues[$k],$LLarr[$langkey][$k]) && !isset($dat[$fileName][$k])))	{
							unset($langValues[$k]);
						}
					}
				}

					// Combining:
				$dat[$fileName]=$langValues;
#debug($fileName);
#debug($dat);
#debug($langValues);
					// Remove old if any.
				$query = 'DELETE FROM tx_extrepmgm_langelements WHERE
									pid='.intval($this->dbPageId).'
								AND deleted_tstamp=0
								AND fe_user='.$fe_user_uid."
								AND extension_key='".addslashes($extKey)."'
								AND langkey='".addslashes($langkey)."'";
				$res = mysql(TYPO3_db,$query);
#debug($query,1);
					// Insert new:
				$query = "INSERT INTO tx_extrepmgm_langelements
					(fe_user,extension_key,langkey,data_content,pid)
					VALUES ('".$fe_user_uid."','".addslashes($extKey)."','".addslashes($langkey)."','".addslashes(serialize($dat))."',".intval($this->dbPageId).')';
#debug($query,1);
				$res = mysql(TYPO3_db,$query);
				echo mysql_error();
			}
		}
	}

	/**
	 * Deletes the translation records for a number of languages of a certain ext_key
	 *
	 * @param	[type]		$extKey: ...
	 * @param	[type]		$langKeyArray: ...
	 * @return	[type]		...
	 */
	function deletedUsedTranslationRecords($extKey,$langKeyArray)	{
		if (is_array($langKeyArray))	{
			$query = 'UPDATE tx_extrepmgm_langelements SET deleted_tstamp='.time().' WHERE
							pid='.intval($this->dbPageId)."
							AND extension_key='".addslashes($extKey)."'
							AND langkey IN ('".implode("','",$langKeyArray)."')";
#debug(array($query));
			$res = mysql(TYPO3_db,$query);
		}
	}

	/**
	 * Mentor Review
	 *
	 * @return	[type]		...
	 */
	function mentorReview()	{
		if ($GLOBALS['TSFE']->loginUser)	{
				// If the user is a true reviewer/mentor:
			$isRV = $GLOBALS['TSFE']->fe_user->user['tx_extrepmgm_isreviewer'];

				// Only Mentors can capture versions for review:
			if ($isRV && is_array($this->piVars['DATA']['capture']))	{
				reset($this->piVars['DATA']['capture']);
				$uid = key($this->piVars['DATA']['capture']);

				$repRec = $this->pi_getRecord('tx_extrep_repository',$uid);
				if ($repRec['extension_uid']==$this->internal['currentRow']['uid'] && !$repRec['tx_extrepmgm_appr_fe_user'])	{
					$query = 'UPDATE tx_extrep_repository SET tx_extrepmgm_appr_fe_user='.intval($GLOBALS['TSFE']->fe_user->user['uid']).
								' WHERE uid='.$uid.' AND tx_extrepmgm_appr_fe_user=0';
					$res = mysql(TYPO3_db,$query);
#					debug(array($query));
#					debug($uid);
#					debug($this->piVars['DATA']);
				}
			}
				// An assigned reviewer can save status
			if (is_array($this->piVars['DATA']['submit']))	{
				$uid = key($this->piVars['DATA']['submit']);
				if (is_array($this->piVars['DATA']['review'][$uid]))	{
					$query = "UPDATE tx_extrep_repository SET
								tx_extrepmgm_appr_comment='".addslashes($this->piVars['DATA']['review'][$uid]['tx_extrepmgm_appr_comment'])."',
								tx_extrepmgm_appr_status=".intval($this->piVars['DATA']['review'][$uid]['tx_extrepmgm_appr_status']).
								' WHERE uid='.$uid.'
								AND tx_extrepmgm_appr_fe_user='.intval($GLOBALS['TSFE']->fe_user->user['uid']);
#debug($query);
					$res = mysql(TYPO3_db,$query);

#					debug($this->piVars['DATA']['review'][$uid]);
					$this->updateExtKeyCache($this->internal['currentRow']['uid']);
				}
			}


				// Selecting versions from the extension repository.
			$query = 'SELECT uid,version,tx_extrepmgm_appr_fe_user,tx_extrepmgm_appr_comment,tx_extrepmgm_appr_status FROM tx_extrep_repository WHERE
					extension_uid = '.intval($this->internal['currentRow']['uid']).
					' AND emconf_private=0'.
					($isRV?'':' AND tx_extrepmgm_appr_fe_user='.intval($GLOBALS['TSFE']->fe_user->user['uid'])).
					$this->cObj->enableFields('tx_extrep_repository').
					' ORDER BY version_int DESC';

			$res = mysql(TYPO3_db,$query);
			echo mysql_error();
			if (mysql_num_rows($res))	{
				$lines=array();
				$lines[]='<tr>
					<td'.$this->pi_classParam('HCell').'>Version:</td>
					<td'.$this->pi_classParam('HCell').'>Username:</td>
					<td'.$this->pi_classParam('HCell').'>Status:</td>
					<td'.$this->pi_classParam('HCell').'>Comment:</td>
					<td'.$this->pi_classParam('HCell').'>Review settings:</td>
				</tr>';
				while($row=mysql_fetch_assoc($res))	{
					$rvUser = $row['tx_extrepmgm_appr_fe_user'] ? $this->pi_getRecord('fe_users',$row['tx_extrepmgm_appr_fe_user']) : "";

					$comment = nl2br(htmlentities($row['tx_extrepmgm_appr_comment'])).'&nbsp;';
					if (is_array($rvUser))	{
						$username = $rvUser['username'];

						reset($this->reviewStates);
						while(list($kk,$vv)=each($this->reviewStates))	{
							if ($kk!=20 || $kk==$row['tx_extrepmgm_appr_status'])	{	// COHIBA cannot be assigned by anyone but the backend admin...
								$opt[$kk]='<option value="'.$kk.'"'.($kk==$row['tx_extrepmgm_appr_status']?" SELECTED":"").'>'.htmlentities($vv[0].' - '.$vv[1]).'</option>';
							}
						}

						$ctrl='
						<textarea rows="5" style="width:200px;" name="'.$this->prefixId.'[DATA][review]['.$row['uid'].'][tx_extrepmgm_appr_comment]">'.htmlentities($row['tx_extrepmgm_appr_comment']).'</textarea><BR>
						<select name="'.$this->prefixId.'[DATA][review]['.$row['uid'].'][tx_extrepmgm_appr_status]" style="width:200px;">'.implode('',$opt).'</select>
						<input type="submit" name="'.$this->prefixId.'[DATA][submit]['.$row['uid'].']" value="Save review status">';
					} else {
						$username = '-';
						$ctrl='<input type="submit" name="'.$this->prefixId.'[DATA][capture]['.$row['uid'].']" value="Review this">';
					}

					$lines[]='<tr>
						<td>'.$row['version'].'</td>
						<td>'.$username.'</td>
						<td>'.$this->getIcon_review($row,$this->internal['currentRow'],1,1).'</td>
						<td width="50%">'.$comment.'</td>
						<td>'.$ctrl.'</td>
					</tr>';
				}

				$content='<form action="'.t3lib_div::getIndpEnv('REQUEST_URI').'" method="POST" style="margin: 0px 0px 0px 0px;">
					<table '.$this->conf['displayExt.']['tableParams_mentorRv'].$this->pi_classParam('mentorRv').'>'.implode('',$lines).'</table>
					</form>';
			} else {
				if ($isRV)	{
					$content = '<p>No versions in repository to review.</p>';
				} else $content = '<p>Error: You are either not an authorized mentor or you are not specifically assigned the job of reviewing this extension.</p>';
			}
		} else $content = '<p>Error: You were not logged in.</p>';
		return $content;
	}

	/**
	 * Open Office document Review
	 *
	 * @param	[type]		$isOwner: ...
	 * @param	[type]		$extRepEntry: ...
	 * @return	[type]		...
	 */
	function oodocReview($isOwner,$extRepEntry)	{
		$maxMB=3;
		if ($GLOBALS['TSFE']->loginUser && ($GLOBALS['TSFE']->fe_user->user['tx_extrepmgm_isdocreviewer'] || $isOwner))	{
			if ($GLOBALS['HTTP_POST_FILES']['_oodoc_for_review'])	{
				$fI=pathinfo($GLOBALS['HTTP_POST_FILES']['_oodoc_for_review']['name']);
				if (strtolower($fI['extension'])=='sxw')	{
					if ($GLOBALS['HTTP_POST_FILES']['_oodoc_for_review']['size'] < $maxMB*1024*1024)	{
						$fC = t3lib_div::getUrl($GLOBALS['HTTP_POST_FILES']['_oodoc_for_review']['tmp_name']);
						$query = "DELETE FROM tx_extrepmgm_oodocreview WHERE
									extension_key='".$this->internal['currentRow']['extension_key']."'
									AND fe_user=".intval($GLOBALS['TSFE']->fe_user->user['uid'])."
									AND oodoc_filename='".addslashes($GLOBALS['HTTP_POST_FILES']['_oodoc_for_review']['name'])."'";
#debug($query);
						$res = mysql(TYPO3_db,$query);

						$query = "INSERT INTO tx_extrepmgm_oodocreview (extension_key,fe_user,oodoc,oodoc_md5,oodoc_size,crdate,oodoc_filename)
									VALUES ('".$this->internal['currentRow']['extension_key']."',
										".intval($GLOBALS['TSFE']->fe_user->user['uid']).",
										'".addslashes($fC)."',
										'".md5($fC)."',
										".strlen($fC).',
										'.time().",
										'".addslashes($GLOBALS['HTTP_POST_FILES']['_oodoc_for_review']['name'])."')";
						$res = mysql(TYPO3_db,$query);
						echo mysql_error();
#debug('insert: '.strlen($query));
#debug(array($query));
					} else $content.='<p>Error: Document larger than '.$maxMB.' MB.</p>';
				} else $content.='<p>Error: Not an open office writer document (.sxw)</p>';
			}
#debug($this->piVars['DATA']);
			if ($this->piVars['DATA']['submit'] && is_array($this->piVars['DATA']['delete']))	{
				reset($this->piVars['DATA']['delete']);
				while(list(,$docUid)=each($this->piVars['DATA']['delete']))	{
					$query = 'DELETE FROM tx_extrepmgm_oodocreview WHERE
								uid='.intval($docUid).'
								AND fe_user='.intval($GLOBALS['TSFE']->fe_user->user['uid']);
#debug($query);
					$res = mysql(TYPO3_db,$query);
				}
			}



			$content.='<BR><p>Send reviewed OpenOffice Writer document:</p>
				<form action="'.t3lib_div::getIndpEnv('REQUEST_URI').'" method="POST" style="margin: 0px 0px 0px 0px;" enctype="multipart/form-data">
					<input type="file" style="width:400;" name="_oodoc_for_review"><BR>
					<input type="submit" name="'.$this->prefixId.'[DATA][submit]" value="Send">
				</form>
				<BR>
				';

				// Management of submitted documents:
			$query = "SELECT uid,crdate,oodoc_filename,oodoc_size,oodoc_md5,fe_user FROM tx_extrepmgm_oodocreview WHERE extension_key='".$this->internal['currentRow']['extension_key']."'";
			$res = mysql(TYPO3_db,$query);
			$lines=array();
			while($row=mysql_fetch_assoc($res))	{
				$user = $this->pi_getRecord('fe_users',$row['fe_user']);
				$lines[]='<tr>
					<td bgcolor="red">'.($user['uid']==$GLOBALS['TSFE']->fe_user->user['uid'] ? '<input type="checkbox" name="'.$this->prefixId.'[DATA][delete][]" value="'.$row['uid'].'">':'').'</td>
					<td>'.$row['oodoc_filename'].'</td>
					<td>'.date('d-m-Y H:i',$row['crdate']).'</td>
					<td>'.t3lib_div::formatSize($row['oodoc_size']).'</td>
					<td>'.$user['username'].'</td>
					<td>'.hexdec(substr(md5($row['oodoc_md5']),0,7)).'</td>
				</tr>';
			}
			if (count($lines))	{
				$content.='<p>Current reviewed documents in queue:</p>';
				$content.='<form action="'.t3lib_div::getIndpEnv('REQUEST_URI').'" method="POST" style="margin: 0px 0px 0px 0px;">
				<table border=1 cellpadding=1 cellspacing=0>'.implode(chr(10),$lines).'</table>
				<input type="submit" name="'.$this->prefixId.'[DATA][submit]" value="REMOVE SELECTED">
				</form>';
			}



			if ($isOwner)	{
				$content.='<hr>';

					// Management of submitted documents:
				$lines=array();
				$lines[]='<tr>
					<td bgcolor="red">Src1:</td>
					<td bgcolor="green">Src2:</td>
					<td>Filename:</td>
					<td>Date:</td>
					<td>Size:</td>
					<td>Merge?</td>
					<td>DL:</td>
					<td>MD5:</td>
				</tr>';

					// Submitted documents for review etc.
				$query = "SELECT uid,crdate,oodoc_filename,oodoc_size,oodoc_md5,fe_user FROM tx_extrepmgm_oodocreview WHERE extension_key='".$this->internal['currentRow']['extension_key']."'";
				$res = mysql(TYPO3_db,$query);
				while($row=mysql_fetch_assoc($res))	{
					$lines[]='<tr>
						<td><input type="radio" name="'.$this->prefixId.'[DATA][src1]" value="rv_'.$row['uid'].'"></td>
						<td><input type="radio" name="'.$this->prefixId.'[DATA][src2]" value="rv_'.$row['uid'].'"></td>
						<td>'.$this->pi_linkTP_keepPIvars($row['oodoc_filename'],array('DATA'=>array('show'=>'rv_'.$row['uid']))).'</td>
						<td>'.date('d-m-Y H:i',$row['crdate']).'</td>
						<td>'.t3lib_div::formatSize($row['oodoc_size']).'</td>
						<td><input type="submit" name="'.$this->prefixId.'[DATA][merge][rv_'.$row['uid'].']" value="Insert in rep."></td>
						<td>'.$this->pi_linkTP_keepPIvars('D/L',array('DATA'=>array('dl'=>'rv_'.$row['uid']))).'</td>
						<td>'.hexdec(substr(md5($row['oodoc_md5']),0,7)).'</td>
					</tr>';
				}

					// Selecting the CURRENT document for display
				$query = "SELECT * FROM tx_extrepmgm_oodoctoc WHERE extension_uid='".$this->internal['currentRow']['uid']."'";
				$res = mysql(TYPO3_db,$query);
				$docContentHash=array();
				while($row=mysql_fetch_assoc($res))	{
					$docContentHash[]=$row['is_included_hash'];
					$lines[]='<tr>
						<td><input type="radio" name="'.$this->prefixId.'[DATA][src1]" value="ch_'.$row['document_unique_ref'].'"></td>
						<td><input type="radio" name="'.$this->prefixId.'[DATA][src2]" value="ch_'.$row['document_unique_ref'].'"></td>
						<td>'.$this->pi_linkTP_keepPIvars($row['cur_tmp_file'],array('DATA'=>array('show'=>'ch_'.$row['document_unique_ref']))).'</td>
						<td>'.date('d-m-Y H:i',$row['doc_mtime']).'</td>
						<td>'.t3lib_div::formatSize($row['doc_size']).'</td>
						<td>&nbsp;</td>
						<td>'.$this->pi_linkTP_keepPIvars('D/L',array('DATA'=>array('dl'=>'ch_'.$row['document_unique_ref']))).'</td>
						<td>'.$row['is_included_hash'].'</td>
					</tr>';
				}
					// Showing the most recent version in repository IF it is not among the currently displayed.
				if (!in_array($extRepEntry['is_manual_included'],$docContentHash))	{
					$lines[]='<tr>
						<td><input type="radio" name="'.$this->prefixId.'[DATA][src1]" value="recent"></td>
						<td><input type="radio" name="'.$this->prefixId.'[DATA][src2]" value="recent"></td>
						<td>'.$this->pi_linkTP_keepPIvars('Most recent "doc/manual.sxw"',array('DATA'=>array('show'=>'recent'))).'</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>'.$this->pi_linkTP_keepPIvars('D/L',array('DATA'=>array('dl'=>'recent'))).'</td>
						<td>'.$extRepEntry['is_manual_included'].'</td>
					</tr>';
				}

				if (count($lines))	{
					$content.='<p>Documents available:</p>';
					$content.='<form action="'.t3lib_div::getIndpEnv('REQUEST_URI').'" method="POST" style="margin: 0px 0px 0px 0px;">
					<table border=1 cellpadding=1 cellspacing=0>'.implode(chr(10),$lines).'</table>
					<input type="submit" name="'.$this->prefixId.'[DATA][compare]" value="COMPARE SELECTED">
					</form>';

					if (is_array($this->piVars['DATA']['merge']))	{
						if (is_array($extRepEntry) && $extRepEntry['datablob'])	{
							reset($this->piVars['DATA']['merge']);
							$key = key($this->piVars['DATA']['merge']);

								// Submitted documents for review etc.
							$parts = explode('_',$key,2);
							if ($parts[0]=='rv')	{
								$query = 'SELECT * FROM tx_extrepmgm_oodocreview WHERE uid='.intval($parts[1]);
								$res = mysql(TYPO3_db,$query);
								if ($rvRec=mysql_fetch_assoc($res))	{
									$datStr = gzuncompress($extRepEntry['datablob']);
									if (md5($datStr)==$extRepEntry['datablob_md5'])	{
										$dB = unserialize($datStr);

										$sxwfile='doc/manual.sxw';
										if (is_array($dB[$sxwfile]))	{

												// Insert the new document
	#unset($dB[$sxwfile]['content']);
	#debug($dB[$sxwfile]);
											$dB[$sxwfile]=array();
											$dB[$sxwfile]['content']=$rvRec['oodoc'];
											$dB[$sxwfile]['size']=strlen($dB[$sxwfile]['content']);
											$dB[$sxwfile]['content_md5']=md5($dB[$sxwfile]['content']);
											$extRepEntry['is_manual_included']=hexdec(substr(md5($dB[$sxwfile]['content_md5']),0,7));
											$dB[$sxwfile]['mtime']=time();

												// ...
											$datStr = serialize($dB);
											$extRepEntry['datablob_md5'] = md5($datStr);
											$extRepEntry['datablob'] = gzcompress($datStr);
											$extRepEntry['datasize_gz'] = strlen($extRepEntry['datablob']);
											$extRepEntry['datasize'] = strlen($datStr);
	#		debug('INSERT!');
												// Insert new version:
											$this->insertRepositoryVersion($extRepEntry);
											$this->updateExtKeyCache($this->internal['currentRow']['uid']);

											$content.='<p>The manual was insert into the repository as a new version and you can now delete this preview version. Please go to the "Edit" menu and update the TOC of the new document to make it active.</p>';
										} else $content.='<p>There didn\'t exist a doc/manual.sxw in the repository at this time, so you can\'t merge something in there.</p>';
									} else $content.='<p>MD5 error</p>';
								} else $content.='<p>ERROR: No review-document found in database</p>';
							} else $content.='<p>ERROR: Not a review-document</p>';
						} else $content.='<p>SYSTEM-ERROR: The extRepEntry record seemed to be incomplete!</p>';
					} elseif ($this->piVars['DATA']['compare']) {
						// Comparing (diff) of two documents:
						if ($this->piVars['DATA']['src1'] && $this->piVars['DATA']['src2'] && $this->piVars['DATA']['src1']!=$this->piVars['DATA']['src2'])	{
							require_once(PATH_t3lib.'class.t3lib_diff.php');
							$diffObj = t3lib_div::makeInstance('t3lib_diff');

		//					$str1='Det er godt vejr i dag'.chr(10);
		//					$str2='Det er dï¿½ligt vejr idag'.chr(10);

							$str1 = strip_tags($this->showPreviewOfDocument($this->piVars['DATA']['src1'],$extRepEntry),'<a><img>');
							$str2 = strip_tags($this->showPreviewOfDocument($this->piVars['DATA']['src2'],$extRepEntry),'<a><img>');
	debug(array(md5($str2),md5($str1)));
							$content.=nl2br($diffObj->makeDiffDisplay($str1,$str2));


	#						debug($this->piVars['DATA']['src1']);
	#						debug($this->piVars['DATA']['src2']);
						} else $content.='<p>ERROR: You didn\'t select two source documents for comparison OR you selected the same document.</p>';
					} elseif ($this->piVars['DATA']['show']) {
							// Displaying preview content of a document:
						$content.= $this->showPreviewOfDocument($this->piVars['DATA']['show'],$extRepEntry);
					} elseif ($this->piVars['DATA']['dl']) {
						$this->downloadDocument($this->piVars['DATA']['dl'],$extRepEntry);
					}
				}
			}
		} else {
			$content = '<p>Error: You are NOT a OOdoc reviewer!</p>';
		}
		return $content;
	}

	/**
	 * Download document
	 *
	 * @param	[type]		$showDat: ...
	 * @param	[type]		$extRepEntry: ...
	 * @return	[type]		...
	 */
	function downloadDocument($showDat,$extRepEntry)	{
		$content='';
		$parts = explode('_',$showDat,2);
		switch($parts[0])	{
			case 'rv':
				$query = 'SELECT * FROM tx_extrepmgm_oodocreview WHERE uid='.intval($parts[1]);
				$res = mysql(TYPO3_db,$query);
				if ($rvRec=mysql_fetch_assoc($res))	{
					$fileRelName = 'oodocreview_'.$rvRec['oodoc_md5'].'.sxw';
					$content=$rvRec['oodoc'];
				}
			break;
			case 'ch':
				$query = 'SELECT * FROM tx_extrepmgm_oodoctoc WHERE document_unique_ref='.intval($parts[1]);
				$res = mysql(TYPO3_db,$query);
				if ($tocElRec=mysql_fetch_assoc($res))	{
					$fileRelName = basename($tocElRec['cur_tmp_file']);
					$tempFile=PATH_site.$tocElRec['cur_tmp_file'];
					if (@is_file($tempFile))	{
						$content=t3lib_div::getUrl($tempFile);
					}
				}
			break;
			case 'recent':
				$sxwfile='doc/manual.sxw';
				$e = $this->getOOdoc($extRepEntry,$sxwfile);
#debug($e);
				if (!$e)	{
					$tempFile=$this->oodoc_tempFile;
					$fileRelName = 'manual.sxw';
					if (@is_file($tempFile))	{
						$content=t3lib_div::getUrl($tempFile);
					}
				}
			break;
			default:
			break;
		}

		if (strlen($content))	{
			$mimeType = 'application/octet-stream';
			Header('Content-Type: '.$mimeType);
			Header('Content-Disposition: attachment; filename='.$fileRelName);
			echo $content;
			exit;
		}
	}


	/**
	 * Displays the content of an open office writer file for preview-.
	 *
	 * @param	[type]		$showDat: ...
	 * @param	[type]		$extRepEntry: ...
	 * @return	[type]		...
	 */
	function showPreviewOfDocument($showDat,$extRepEntry)	{
		$parts = explode('_',$showDat,2);
		switch($parts[0])	{
			case 'rv':
				$query = 'SELECT * FROM tx_extrepmgm_oodocreview WHERE uid='.intval($parts[1]);
				$res = mysql(TYPO3_db,$query);
				if ($rvRec=mysql_fetch_assoc($res))	{
					$fileRelName = 'typo3temp/oodocreview_'.$rvRec['oodoc_md5'].'.sxw';
					$tempFile = $DELETE_file = PATH_site.$fileRelName;
					if (!is_file($tempFile))	{
						t3lib_div::writeFile($tempFile,$rvRec['oodoc']);
					}
					$idKey='PI:extrep.'.$rvRec['extension_key'].'.'.$fileRelName;
				}
			break;
			case 'ch':
				$query = 'SELECT * FROM tx_extrepmgm_oodoctoc WHERE document_unique_ref='.intval($parts[1]);
				$res = mysql(TYPO3_db,$query);
				if ($tocElRec=mysql_fetch_assoc($res))	{
					$idKey='PI:extrep.'.$tocElRec['extension_uid'].'.'.$tocElRec['cur_tmp_file'];
					$tempFile=PATH_site.$tocElRec['cur_tmp_file'];
				}
			break;
			case 'recent':
				$sxwfile='doc/manual.sxw';
				$e = $this->getOOdoc($extRepEntry,$sxwfile);
#debug($e);
				if (!$e)	{
					$tempFile=$this->oodoc_tempFile;
					$idKey=$this->oodoc_idKey;
				}
			break;
			default:
			break;
		}

		if ($tempFile && $idKey)	{
#debug(array($tempFile,$idKey));
				// Make OOdoc instance
			$this->ooDocObj_loaded='';
			$this->ooDocObj = t3lib_div::makeInstance('tx_oodocs');
			$this->ooDocObj->compressedStorage=1;

			if (@is_file($tempFile))	{
				$e = $this->ooDocObj->init($tempFile,$idKey);
				if (!$e)	{

								$dB = 'border: 1px dotted #cccccc;';
								$this->ooDocObj->PstyleMap = array(														// THIS IS THE NAMES of the styles (from the "Automatic" palette) in OpenOffice Writer 1.0 :
										// Bodytext formats
									'Text body' => array ('<p class="tx-oodocs-TB" style="'.$dB.'">','</p>'),				// "Text body"
									'Preformatted Text' => array ('<pre style="margin: 0px 0px 0px 0px; line-height:100%; font-size: 11px;'.$dB.'">','</pre>', 1),						// "Preformatted Text" (HTML-menu)
									'Table Contents' => array ('<p class="tx-oodocs-TC" style="'.$dB.' background-color:#eeffff;">','</p>'),			// "Table Contents"
									'Table Contents/PRE' => array ('<pre style="margin: 0px 0px 0px 0px; line-height:100%; font-size: 11px;'.$dB.' background-color:#eeffff;">','</pre>', 1),			// "Table Contents"
									'Table Heading' => array ('<p class="tx-oodocs-TH" style="'.$dB.' background-color:#ffffee;">','</p>'),			// "Table Heading"

										// Headers:
									'Heading 1' => array('<H1 style="'.$dB.' background-color:#000066; color:white;">','</H1>'),									// Heading 1
									'Heading 2' => array('<H2 style="'.$dB.' background-color:#0099cc;">','</H2>'),									// Heading 2
									'Heading 3' => array('<H3 style="'.$dB.' background-color:#99ccFF;">','</H3>'),									// Heading 3
									'Heading 4' => array('<H4 style="'.$dB.' background-color:#cceeFF;">L4: ','</H4>'),									// Heading 4
									'Heading 5' => array('<H5 style="'.$dB.' background-color:#eeeeFF;">L5: ','</H5>'),									// Heading 5

										// DEFAULT (non-rendered)
									'_default' => array('<p style="color: #999999;'.$dB.' background-color:red;">','</p>'),										// [everything else...]
								);
	/*								debug(array(
									$this->ooDocObj->file,
									$this->ooDocObj->mtime,
									$this->ooDocObj->fileHash,
									$this->ooDocObj->ext_ID
								));
	*/
					$this->ooDocObj->prepareOfficeBodyArray();
					reset($this->ooDocObj->officeBody);

					$res2 = $this->ooDocObj->renderOOBody($this->ooDocObj->officeBody);
					$content.=implode(chr(10),$res2);

/*



		$fileArray=array();
		$query = 'SELECT * FROM tx_oodocs_filestorage WHERE rel_id='.intval($this->ooDocObj->ext_ID);
		$res = mysql(TYPO3_db,$query);
		while($row=mysql_fetch_assoc($res))	{
			$fileArray[$row['filepath']]=$row;
		}

			// Setting Author title:
		if ($fileArray['meta.xml'])	{
			$XML_content = gzuncompress($fileArray['meta.xml']['content']);
			if ($XML_content)	{
				$p = xml_parser_create();
				xml_parse_into_struct($p,$XML_content,$vals,$index);
				xml_parser_free($p);

				$structure = $this->ooDocObj->indentSubTagsRec($vals,999);
#				$structure[0]['subTags'][0]['subTags']['12']['value']=utf8_encode('Alfons ï¿½erg');
				if ($structObj = &$this->ooDocObj->getObjectFromStructure($structure,'OFFICE:DOCUMENT-META/OFFICE:META/META:USER-DEFINED#2'))	{
					$structObj['value']=utf8_encode('Alfons ï¿½erg');
					unset($structObj);
				}
				$fileArray['meta.xml']['content'] = gzcompress($this->ooDocObj->compileDocument($structure));
			}
		}
			// Using the stylesheet of another document:
		$sxwfile='doc/manual.sxw';
		$extKeyRec = $this->getExtKeyRecord('test_deaez');
		$placeholder = $this->getTocPHElement($extKeyRec['uid'],$sxwfile);
		if (is_array($placeholder))	{
			$query = 'SELECT * FROM tx_oodocs_filestorage WHERE rel_id='.intval($placeholder['cur_oodoc_ref'])." AND filepath='styles.xml'";
			$res = mysql(TYPO3_db,$query);
			if($row=mysql_fetch_assoc($res))	{
				$XML_content = gzuncompress($row['content']);
				if ($XML_content)	{
					$p = xml_parser_create();
					xml_parse_into_struct($p,$XML_content,$vals,$index);
					xml_parser_free($p);

						// Parsing structure:
					$structure = $this->ooDocObj->indentSubTagsRec($vals,999);
						// Finding footer image:
					if ($structObj = &$this->ooDocObj->getObjectFromStructure($structure,'OFFICE:DOCUMENT-STYLES/OFFICE:MASTER-STYLES/STYLE:MASTER-PAGE/STYLE:FOOTER/TEXT:P/DRAW:IMAGE'))	{
						if (substr($structObj['attributes']['XLINK:HREF'],0,10)=='#Pictures/')	{
							$query = 'SELECT * FROM tx_oodocs_filestorage WHERE rel_id='.intval($placeholder['cur_oodoc_ref'])." AND filepath='".substr($structObj['attributes']['XLINK:HREF'],1)."'";
							$res = mysql(TYPO3_db,$query);
							if($imgrow=mysql_fetch_assoc($res))	{
								$newPath = 'Pictures/'.md5($imgrow['filepath']).'.png';
								$fileArray[$newPath]=$imgrow;
								$fileArray[$newPath]['filepath']=$newPath;
								$fileArray[$newPath]['filename']=md5($imgrow['filepath']).'.png';
								$structObj['attributes']['XLINK:HREF']='#'.$newPath;
debug($structObj);
#								$structObj['attributes']['SVG:WIDTH']='5.345cm';
#								debug($structObj);
							}
						}
					}
					$fileArray['styles.xml']['content'] = gzcompress($this->ooDocObj->compileDocument($structure));
#					debug($structure);
				}
			}
		}

			// CONTENT!
		if ($fileArray['content.xml'])	{
			$XML_content = gzuncompress($fileArray['content.xml']['content']);
			if ($XML_content)	{
				$p = xml_parser_create();
				xml_parse_into_struct($p,$XML_content,$vals,$index);
				xml_parser_free($p);

				$structure = $this->ooDocObj->indentSubTagsRec($vals,999);
#debug($structure);
				// Dï¿½ï¿½				if ($structObj = &$this->ooDocObj->getObjectFromStructure($structure,'OFFICE:DOCUMENT-CONTENT/OFFICE:BODY'))	{
					$structObj['subTags'][]=array(
						'tag' => 'TEXT:P',
						'type' => 'complete',
						'attributes' => array('TEXT:STYLE-NAME'=>'Text body'),
						'value' => utf8_encode('Kasper Skï¿½hj ï¿½ï¿½ï¿½'),
						'subTags' => array(
							array(
								'tag' => 'DRAW:IMAGE',
								'type' => 'complete',
								'attributes' => array(
									'DRAW:NAME' => 'Graphic 2',
									'TEXT:ANCHOR-TYPE' => 'paragraph',
									'XLINK:HREF' => '#Pictures/9f4044bcb768a78cb613720168ba4a14.png',
									'XLINK:TYPE' => 'simple',
									'XLINK:SHOW' => 'embed',
									'XLINK:ACTUATE' => 'onLoad',
								)
							)
						)
					);

					$styleObj = &$this->ooDocObj->getObjectFromStructure($structure,'OFFICE:DOCUMENT-CONTENT/OFFICE:AUTOMATIC-STYLES');
#					debug($styleObj);

					$register=array();
					reset($styleObj['subTags']);
					while(list($k4,$v4)=each($styleObj['subTags']))	{
						$register[substr($v4['attributes']['STYLE:NAME'],0,1)][]=substr($v4['attributes']['STYLE:NAME'],1);
					}

					$styleCache=array();
#debug($register);

					$parts = $this->_temp_get_oodoc_element(200);
#					$fullStruct = $this->ooDocObj->indentSubTagsRec($parts[2],999);
					reset($parts[1]);
					while(list($kk,$vv)=each($parts[1]))	{
						if (is_array($vv['subTags']))	{
							reset($vv['subTags']);
							while(list($kkk,$vvv)=each($vv['subTags']))	{
								if ($vvv['attributes']['TEXT:STYLE-NAME'])	{
									$st=$vvv['attributes']['TEXT:STYLE-NAME'];
									if (t3lib_div::inList('P,T',substr($st,0,1)) && t3lib_div::testInt(substr($st,1)))	{
										if (!isset($styleCache[$st]))	{
											reset($parts[2]->officeStyles);
											while(list($k4,$v4)=each($parts[2]->officeStyles))	{
												if ($v4['attributes']['STYLE:NAME']==$st)	{
													if (is_array($register[substr($st,0,1)]))	{
														$n=max($register[substr($st,0,1)])+1;
													} else $n=1;
													$v4['attributes']['STYLE:NAME']=substr($st,0,1).$n;
													$styleObj['subTags'][]=$v4;
													$styleCache[$st]=substr($st,0,1).$n;
#debug('!');
												}
											}
										}
										if (isset($styleCache[$st]))	{
											$parts[1][$kk]['subTags'][$kkk]['attributes']['TEXT:STYLE-NAME']=$styleCache[$st];
										}
									}
								}
							}
						}
					}
#debug($parts[1]);
#debug($styleObj);

					$parts[1] = $this->ooDocObj->indentSubTagsRec($parts[1],999);

					$structObj['subTags']=array_merge($structObj['subTags'],$parts[1]);
#					debug($structObj);
				}
debug($structure);
				$fileArray['content.xml']['content'] = gzcompress($this->ooDocObj->compileDocument($structure));
			}
		}


		$msg = $this->ooDocObj->fileArrayToSxw($fileArray,$this->ooDocObj->compressedStorage,PATH_site.'typo3temp/writeOOdoc.sxw');
		debug($msg);
		debug(array_keys($fileArray));

*/


						// DELETES the temporary file and oodoc-filestorage position IF that should be done (review-previews...)
					if ($DELETE_file && t3lib_div::isFirstPartOfStr($DELETE_file,PATH_site.'typo3temp/oodocreview_'))	{
						unlink($DELETE_file);
						$query = 'DELETE FROM tx_oodocs_filestorage WHERE rel_id='.intval($this->ooDocObj->ext_ID);
						$res = mysql(TYPO3_db,$query);
#						debug(array($query));
#						debug(mysql_affected_rows());

/*
						debug(array($DELETE_file));
									debug(array(
									$this->ooDocObj->file,
									$this->ooDocObj->mtime,
									$this->ooDocObj->fileHash,
									$this->ooDocObj->ext_ID
								));
*/
					}
					return $content;
				} else return "ERROR: The OOdoc parser returned an error: ".$e; // $e
			} else return "ERROR: No temporary sxw file found in '".$tempFile."'"; // is_file
		} else return "ERROR: Command not understood."; // is_file
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/extrep_mgm/pi1/class.tx_extrepmgm_singleviews.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/extrep_mgm/pi1/class.tx_extrepmgm_singleviews.php']);
}

?>

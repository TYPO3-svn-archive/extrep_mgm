<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2002-2004 Kasper Sk�hj (kasperYYYY@typo3.com)
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
 * @author		Kasper Sk�hj <kasperYYYY@typo3.com>
 * @co-author 	Robert Lemke <robert@typo3.org>
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *  104: class tx_extrepmgm_pi1 extends tx_extrep
 *  182:     function main($content,$conf)
 *  232:     function main2($content,$conf)
 *  242:     function basicInit($conf)
 *
 *              SECTION: Shared functions
 *  284:     function checkLogin()
 *  298:     function validateUploadUser()
 *  313:     function getLangStatVisual($extRow)
 *  358:     function linkToDocumentation($str,$extUid,$tocUid="",$anchor="")
 *  381:     function linkToExtension($str,$extUid)
 *  403:     function downloadDocument($showDat,$extRepEntry)
 *  458:     function getUserName($fe_users_uid)
 *  472:     function getIcon_state($extRepRow)
 *  487:     function getIcon_review($extRepRow,$keyRow,$textMode=0,$dontShowPrev=0)
 *  549:     function showPreviewOfDocument($showDat,$extRepEntry)
 *  822:     function pi_list_modeSelector($items=array())
 *
 *              SECTION: Open Office Functions
 *  852:     function renderMasterToc()
 * 1075:     function renderDocumentationForExtension()
 * 1203:     function renderOOdocSlice($extUid,$tocEl,$sameLevel=0,$offsetFirst=0)
 * 1319:     function clearPageCacheForExtensionDoc($extUid)
 * 1331:     function clearOOsliceCache($extUid)
 * 1345:     function getOOdoc($row2,$sxwfile)
 * 1397:     function findOOdocTables($extUid)
 * 1439:     function getPropertyTable($tableCode,$all=0)
 * 1495:     function cleanUpText($text)
 * 1523:     function renderTOC($extUid,$toc)
 * 1564:     function renderTOC_main($extUid,$toc)
 * 1609:     function getAUDIcons($audInt)
 * 1628:     function prepareTOCdata($hToc,$path=array(),$num=array(),$nextSection=array(),$level3=0,$aud_prev=0)
 * 1693:     function generateTOCforMetaData($extRepEntry,$sxwfile,$edit=0)
 * 1809:     function generateDocumentForm($extRepEntry,$sxwfile)
 * 1920:     function makeTOCfromLoadedOOdoc()
 * 1958:     function updateTOCField($extUid,$sxwfile,$st_uid,$type,$field)
 * 1977:     function updateInsertTOCph($extUid,$sxwfile,$isIncHash)
 * 2091:     function updateCachedToc($extUid,$sxwfile)
 * 2126:     function updateTocPH($extUid,$sxwfile,$dat)
 * 2145:     function getTocPHElement($extUid,$sxwfile)
 * 2159:     function deleteTocPHElement($extUid,$sxwfile)
 * 2174:     function updateInsertTOCEntry($extUid,$sxwfile,$TOCarray,$st_uid=0)
 * 2221:     function cleanUpOldOOdoc($oodoc_fileStorage_id, $tempFile)
 * 2246:     function buildCacheOfCurrentParts($extUid,$sxwfile)
 * 2271:     function getTOCfromSavedOOdoc($extUid,$sxwfile,$noZeros=0)
 * 2301:     function makeHierarchyTOC($toc,$level=0,$cmp=0)
 * 2339:     function cmpArrays($hToc_curDoc,$hToc_saved,$hToc_curDoc_full,$hToc_saved_full,$level=0)
 * 2484:     function _temp_get_oodoc_element($tocEl)
 *
 *              SECTION: Translator listing
 * 2546:     function listTranslations()
 *
 *              SECTION: Teams/Projects listing
 * 2743:     function listTeamsProjects()
 *
 * TOTAL FUNCTIONS: 45
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */

require_once(t3lib_extMgm::extPath("extrep")."pi/class.tx_extrep.php");
if (t3lib_extMgm::isLoaded("t3annotation"))	{
	require_once(t3lib_extMgm::extPath("t3annotation")."pi1/class.tx_t3annotation_pi1.php");
}

/**
 * [Describe function...]
 *
 */
class tx_extrepmgm_pi1 extends tx_extrep {
	var $prefixId = "tx_extrepmgm_pi1";		// Same as class name
	var $scriptRelPath = "pi1/class.tx_extrepmgm_pi1.php";	// Path to this script relative to the extension dir.
	var $extKey = "extrep_mgm";	// The extension key.
	var $byPassMemberOK=1;

	var $reviewStates = Array(
		0 => array("No review", "The extension is not reviewed yet or is currently being reviewed.", "0_notreviewed.gif"),
		5 => array("No cigar", "Still missing a lot before it's generally useful.", "1_nocigar.gif"),
		10 => array("Close, but no cigar","Almost there, you are close! Take that last step!", "2_closebutnocigar.gif"),
		15 => array("Cigar","Stable, working fine. Good extension. Celebrate it!","3_cigar.gif"),
		20 => array("Cohiba!","Extraordinary good work, documentation and attention to detail.","4_cohiba.gif"),
	);

	var $priorityLabels = Array (
		'5' => 'High',
		'3' => 'Mid',
		'1' => 'Low',
	);

	var $kinds = Array(
		1 => "Introduction",
		2 => "Users manual",
		3 => "Adminstration",
		4 => "Configuration",
		5 => "Tutorial",
		6 => "Known problems",
		7 => "To-Do list",
	);
	var $kinds_short = Array(
		1 => "Intro.",
		2 => "Users M.",
		3 => "Admin.",
		4 => "Config.",
		5 => "Tutor.",
		6 => "Kn. Prob.",
		7 => "To-Do",
	);

	var $docCats = Array(
		6 => '"Getting Started"',
		7 => '"Modern Template Building"',
		2 => "Other Tutorials",
		3 => "Installation",
		5 => "End-User Manuals",
		1 => "Core documentation",
		8 => "References",
		4 => "Miscellaneous",
		0 => "Extension manuals",
	);

	var $docPage=0;
	var $extPage=0;
	var $annoPage=0;

		// Internal
	var $cmp_hiddenFields=0;
	var $toc_current = array();
	var $saved_toc = array();
	var $pairs=array();
	var $oodoc_inKey="_doc";

	var $linearToc=array();
	var $linearTocOrder=array();

	var $ooDocObj;
	var $ooDocObj_loaded="";

	var $oodoc_tempFile="";
	var $oodoc_idKey="";

	/**
	 * Main function
	 *
	 * @param	[type]		$content: ...
	 * @param	[type]		$conf: ...
	 * @return	[type]		...
	 */
	function main($content,$conf)	{
			// Basic initialization
		$this->basicInit($conf);

		if ($this->dbPageId<=0)	{
			$content='<p>You must add a reference to a page (called "Starting point") in the "Insert Plugin" content element. That page should be where Frontend Users and all repository records are stored.</p>';
		} else {

				// Choose resposible sub class for the required function:
			$subFunctionMethodName = 'main';
			switch($this->cObj->data["tx_extrepmgm_function"])	{
				case 1:	// Registering of extension keys:
					$subFunctionClassName = 'tx_extrepmgm_registerextkeys';
				break;
				case 2:	// The Open Office document viewing:
					$subFunctionClassName = 'tx_extrepmgm_oodocuments';
					$subFunctionMethodName = ($this->piVars["extUid"] ? 'renderDocumentationForExtension' : 'renderMasterToc');
				break;
				case 3: //
					$subFunctionClassName = 'tx_extrepmgm_listtranslations';
				break;
				case 4:	// Listing of frontend users
					$subFunctionClassName = 'tx_extrepmgm_listviews';
					$subFunctionMethodName = 'listUsers';
				break;
				case 5:	// Listing of the teams and projects page
					$subFunctionClassName = 'tx_extrepmgm_listteamsprojects'; break;
				default:																		// List view or single / edit view
					$subFunctionClassName = ($this->piVars["showUid"]) ? 'tx_extrepmgm_singleviews' : 'tx_extrepmgm_listviews';
			}

				// Instantiate sub class and call rendering function or show the default list view:
			require_once(t3lib_extMgm::extPath('extrep_mgm').'pi1/class.'.$subFunctionClassName.'.php');
			$subFunctionObj = t3lib_div::makeInstance($subFunctionClassName);
			$subFunctionObj->cObj =& $this->cObj;
			$subFunctionObj->basicInit($conf);
			$subFunctionObj->getPIdata();
			$content = $subFunctionObj->$subFunctionMethodName();
		}

		return $this->pi_wrapInBaseClass($content);
	}

	/**
	 * "Main2" is in control of CACHED USER cObjects - main() is designed to be USER_INT.
	 *
	 * @param	[type]		$content: ...
	 * @param	[type]		$conf: ...
	 * @return	[type]		...
	 */
	function main2($content,$conf)	{
		return $this->main($content, $conf);
	}

	/**
	 * Doing very basic configuration
	 *
	 * @param	[type]		$conf: ...
	 * @return	[type]		...
	 */
	function basicInit($conf)	{
		$this->dbPageId = $conf["pidList"] = intval($this->cObj->data["pages"]);
		if (!$this->dbPageId)	{
			$d=$GLOBALS["TSFE"]->getStorageSiterootPids();
			$this->dbPageId = intval($d["_STORAGE_PID"]);
		}
#debug($this->dbPageId);
		$GLOBALS['TSFE']->pEncAllowedParamNames[$this->prefixId.'[extUid]']=1;
		$GLOBALS['TSFE']->pEncAllowedParamNames[$this->prefixId.'[tocEl]']=1;

		$conf["recursive"] = 0;
		$this->conf = $conf;

		$this->docPage=intval($this->conf["docPage"]);	//1387;
		$this->extPage=intval($this->conf["extPage"]);	//1383;
		$this->annoPage=intval($this->conf["annoPage"]);	//1394;
#debug(array($this->docPage,$this->extPage,$this->annoPage));
/*
		$cF = t3lib_div::makeInstance("t3lib_TSparser");
		list($name, $conf) = $cF->getVal("plugin.tx_extrepmgm_pi1.default",$GLOBALS["TSFE"]->tmpl->setup);
		debug(array($name, $conf));
	*/
	}








	/*********************************************************
	 *
	 * 	Shared functions
	 *
	 *********************************************************/

	/**
	 * Checks if a user is logged in, and if so returns 1 and returns 2 if the fe_users pid is different from ->dbPageId
	 *
	 * @return	[type]		...
	 */
	function checkLogin()	{
		if (!$GLOBALS["TSFE"]->loginUser)	{
			return 1;
		} elseif ($GLOBALS["TSFE"]->fe_user->user["pid"]!=$this->dbPageId) {
			return 2;
		}
		return 0;
	}

	/**
	 * Returns the fe_user array if a user is logged in.
	 *
	 * @return	[type]		...
	 */
	function validateUploadUser()	{
		if ($GLOBALS["TSFE"]->fe_user->user["uid"])	{
			return $GLOBALS["TSFE"]->fe_user->user;
		} else {
			return "No user logged in";
		}
	}

	/**
	 * Returns a visual representation of the translation statistics for an extension row
	 * Requires $this->ext_langInfo to be loaded with the language info prior to calls.
	 *
	 * @param	[type]		$extRow: ...
	 * @return	[type]		...
	 */
	function getLangStatVisual($extRow)	{
		$infoArr_dat = unserialize($extRow["tx_extrepmgm_cache_infoarray"]);
		if (is_array($infoArr_dat["translation_status"]))	{
			$langStat=array();
			reset($infoArr_dat["translation_status"]);
			while(list($lK,$dat)=each($infoArr_dat["translation_status"]))	{
				$langRec = $this->ext_langInfo[0][$lK];

					// Find if user is chief or assistant or maybe owner:
				$auth=0;
				if (is_array($langRec) && $GLOBALS["TSFE"]->loginUser)	{
						// Getting authentication and possibly saving incoming data:
					$auth = $GLOBALS["TSFE"]->fe_user->user["uid"]==$extRow["owner_fe_user"] ||
								$langRec["auth_translator"]==$GLOBALS["TSFE"]->fe_user->user["uid"] ||
								isset($this->ext_langInfo[1][$langRec["langkey"]."_".$GLOBALS["TSFE"]->fe_user->user["uid"]]);
				}

				$col="";
				if ($auth) {
					if ($dat["missing_count"]>0)	{
						$col=$this->conf["displayExt."]["translation_color_missing"];
					} elseif ($dat["non_chief_count"]) {
						$col=$this->conf["displayExt."]["translation_changed_by_someelse"];
					} else {
						$col=$this->conf["displayExt."]["translation_color_ok"];
					}
				}
				$content = "".($dat["missing_count"]||$dat["non_chief_count"]||$dat["cur_count"] ? $dat["cur_count"]."/".$dat["missing_count"]."/".$dat["non_chief_count"] : '*');
				$langStat[$lK]='<span '.($col?'style="background-color:'.$col.';font-weight: bold;"':'').' title="'.htmlspecialchars($lK.': '.$content).'">'.($content=="*"?"*":"X").'</span>';
			}
		} else {
			$langStat="-";
		}
		return $langStat;
	}

	/**
	 * Makes a link to a documentation page.
	 *
	 * @param	[type]		$str: ...
	 * @param	[type]		$extUid: ...
	 * @param	[type]		$tocUid: ...
	 * @param	[type]		$anchor: ...
	 * @return	[type]		...
	 */
	function linkToDocumentation($str,$extUid,$tocUid="",$anchor="")	{
		$cache=1;

		$urlParameters=array("tx_extrepmgm_pi1"=>array(
										"extUid"=>$extUid,
										"tocEl"=>$tocUid
									));
		$conf=array();
		$conf["useCacheHash"]=$cache;
		$conf["no_cache"]=!$cache;
		$conf["parameter"]= $this->docPage.($anchor?"#".$anchor:"");
		$conf["additionalParams"]=$this->conf["parent."]["addParams"].t3lib_div::implodeArrayForUrl("",$urlParameters,"",1).$this->pi_moreParams;

		return $this->cObj->typoLink($str, $conf);
	}

	/**
	 * Makes a link to an extension page.
	 *
	 * @param	[type]		$str: ...
	 * @param	[type]		$extUid: ...
	 * @return	[type]		...
	 */
	function linkToExtension($str,$extUid)	{
		$cache=0;

		$urlParameters=array("tx_extrepmgm_pi1"=>array(
										"showUid"=>$extUid
									));
		$conf=array();
		$conf["useCacheHash"]=$cache;
		$conf["no_cache"]=!$cache;
		$conf["parameter"]= $this->extPage;
		$conf["additionalParams"]=$this->conf["parent."]["addParams"].t3lib_div::implodeArrayForUrl("",$urlParameters,"",1).$this->pi_moreParams;

		return $this->cObj->typoLink($str, $conf);
	}

	/**
	 * Download document
	 *
	 * @param	[type]		$showDat: ...
	 * @param	[type]		$extRepEntry: ...
	 * @return	[type]		...
	 */
	function downloadDocument($showDat,$extRepEntry)	{
		$content="";
		$parts = explode("_",$showDat,2);
		switch($parts[0])	{
			case "rv":
				$query = "SELECT * FROM tx_extrepmgm_oodocreview WHERE uid=".intval($parts[1]);
				$res = mysql(TYPO3_db,$query);
				if ($rvRec=mysql_fetch_assoc($res))	{
					$fileRelName = "oodocreview_".$rvRec["oodoc_md5"].".sxw";
					$content=$rvRec["oodoc"];
				}
			break;
			case "ch":
				$query = "SELECT * FROM tx_extrepmgm_oodoctoc WHERE document_unique_ref=".intval($parts[1]);
				$res = mysql(TYPO3_db,$query);
				if ($tocElRec=mysql_fetch_assoc($res))	{
					$fileRelName = basename($tocElRec["cur_tmp_file"]);
					$tempFile=PATH_site.$tocElRec["cur_tmp_file"];
					if (@is_file($tempFile))	{
						$content=t3lib_div::getUrl($tempFile);
					}
				}
			break;
			case "recent":
				$sxwfile="doc/manual.sxw";
				$e = $this->getOOdoc($extRepEntry,$sxwfile);
#debug($e);
				if (!$e)	{
					$tempFile=$this->oodoc_tempFile;
					$fileRelName = "manual.sxw";
					if (@is_file($tempFile))	{
						$content=t3lib_div::getUrl($tempFile);
					}
				}
			break;
			default:
			break;
		}

		if (strlen($content))	{
			$mimeType = "application/octet-stream";
			Header("Content-Type: ".$mimeType);
			Header("Content-Disposition: attachment; filename=".$fileRelName);
			echo $content;
			exit;
		}
	}


	/**
	 * Returns the user name of a fe_users.uid
	 *
	 * @param	[type]		$fe_users_uid: ...
	 * @return	[type]		...
	 */
	function getUserName($fe_users_uid)	{
		if (!isset($this->cache_fe_user_names[$fe_users_uid]))	{
			$fe_user_rec = $this->pi_getRecord("fe_users", $fe_users_uid);
			$this->cache_fe_user_names[$fe_users_uid] = "".$fe_user_rec["username"];
		}
		return $this->cache_fe_user_names[$fe_users_uid];
	}

	/**
	 * Returns the proper state image for the repository record given
	 *
	 * @param	[type]		$extRepRow: ...
	 * @return	[type]		...
	 */
	function getIcon_state($extRepRow)	{
		return isset($this->states[$extRepRow["emconf_state"]]) ?
			'<img src="'.t3lib_extMgm::siteRelPath("extrep_mgm").'res/state_'.$extRepRow["emconf_state"].'.gif" width="109" height="17">' :
			'<img src="'.t3lib_extMgm::siteRelPath("extrep_mgm").'res/state_na.gif" width="109" height="17">';
	}

	/**
	 * Returns the review image for the state of the repository record given.
	 *
	 * @param	[type]		$extRepRow: ...
	 * @param	[type]		$keyRow: ...
	 * @param	[type]		$textMode: ...
	 * @param	[type]		$dontShowPrev: ...
	 * @return	[type]		...
	 */
	function getIcon_review($extRepRow,$keyRow,$textMode=0,$dontShowPrev=0)	{
		$review='';
		$text='';
		$dimmed=0;
		if (!$keyRow["tx_extrepmgm_appr_flag"])	{
			$stat = $extRepRow["tx_extrepmgm_appr_status"];
			$revUser = $extRepRow["tx_extrepmgm_appr_fe_user"] ? $this->pi_getRecord("fe_users",$extRepRow["tx_extrepmgm_appr_fe_user"]) : "";

			if (!$dontShowPrev && (!is_array($revUser) && !$stat))	{	// If no reviewer is set and if no status is set, then show the previous state...
				$infoA = unserialize($keyRow["tx_extrepmgm_cache_infoarray"]);
#debug($infoA);
				$stat = $infoA["tx_extrepmgm_appr_status"];
				$dimmed=1;
				$revUser = $infoA["tx_extrepmgm_appr_fe_user"] ? $this->pi_getRecord("fe_users",$infoA["tx_extrepmgm_appr_fe_user"]) : "";
#debug(array($stat,$revUser));
			}

			if (!is_array($revUser))	{$stat=0; $dimmed=0;}
			if ($img = $this->reviewStates[$stat][2])	{
				if (is_array($revUser) && !$stat)	{
					$review='<img src="'.t3lib_extMgm::siteRelPath("extrep_mgm").'res/0_reviewing.gif" width=50 height=50 '.$imgParams.'>';
					$text='Being reviewed';
				} else {
					$review='<img src="'.t3lib_extMgm::siteRelPath("extrep_mgm").'res/'.($dimmed?'dim_':'').$img.'" width=50 height=50 title="'.htmlentities(is_array($revUser)?'Reviewer: '.$revUser["username"]:'').'"'.$imgParams.'>';
					$text=$this->reviewStates[$stat][0];
				}
				if ($dimmed)	$text='<span style="color:#999999;">'.$text.'</span>';
			} else $review=$stat;
		} else {
			$review="";
		}

		if ($review)	{
			switch($textMode)	{
				case 2:
					return $review;
				break;
				case 1:
					return '<table border=0 cellpadding=0 cellspacing=3><tr><td>'.$review.'</td><td>'.$text.'</td></tr></table>';
				break;
				default:
					return $review.'<br>'.$text;
				break;
			}
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
		$parts = explode("_",$showDat,2);
		switch($parts[0])	{
			case "rv":
				$query = "SELECT * FROM tx_extrepmgm_oodocreview WHERE uid=".intval($parts[1]);
				$res = mysql(TYPO3_db,$query);
				if ($rvRec=mysql_fetch_assoc($res))	{
					$fileRelName = "typo3temp/oodocreview_".$rvRec["oodoc_md5"].".sxw";
					$tempFile = $DELETE_file = PATH_site.$fileRelName;
					if (!is_file($tempFile))	{
						t3lib_div::writeFile($tempFile,$rvRec["oodoc"]);
					}
					$idKey="PI:extrep.".$rvRec["extension_key"].".".$fileRelName;
				}
			break;
			case "ch":
				$query = "SELECT * FROM tx_extrepmgm_oodoctoc WHERE document_unique_ref=".intval($parts[1]);
				$res = mysql(TYPO3_db,$query);
				if ($tocElRec=mysql_fetch_assoc($res))	{
					$idKey="PI:extrep.".$tocElRec["extension_uid"].".".$tocElRec["cur_tmp_file"];
					$tempFile=PATH_site.$tocElRec["cur_tmp_file"];
				}
			break;
			case "recent":
				$sxwfile="doc/manual.sxw";
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
			$this->ooDocObj_loaded="";
			$this->ooDocObj = t3lib_div::makeInstance("tx_oodocs");
			$this->ooDocObj->compressedStorage=1;

			if (@is_file($tempFile))	{
				$e = $this->ooDocObj->init($tempFile,$idKey);
				if (!$e)	{

								$dB = 'border: 1px dotted #cccccc;';
								$this->ooDocObj->PstyleMap = array(														// THIS IS THE NAMES of the styles (from the "Automatic" palette) in OpenOffice Writer 1.0 :
										// Bodytext formats
									"Text body" => array ('<p class="tx-oodocs-TB" style="'.$dB.'">','</p>'),				// "Text body"
									"Preformatted Text" => array ('<pre style="margin: 0px 0px 0px 0px; line-height:100%; font-size: 11px;'.$dB.'">','</pre>', 1),						// "Preformatted Text" (HTML-menu)
									"Table Contents" => array ('<p class="tx-oodocs-TC" style="'.$dB.' background-color:#eeffff;">','</p>'),			// "Table Contents"
									"Table Contents/PRE" => array ('<pre style="margin: 0px 0px 0px 0px; line-height:100%; font-size: 11px;'.$dB.' background-color:#eeffff;">','</pre>', 1),			// "Table Contents"
									"Table Heading" => array ('<p class="tx-oodocs-TH" style="'.$dB.' background-color:#ffffee;">','</p>'),			// "Table Heading"

										// Headers:
									"Heading 1" => array('<H1 style="'.$dB.' background-color:#000066; color:white;">','</H1>'),									// Heading 1
									"Heading 2" => array('<H2 style="'.$dB.' background-color:#0099cc;">','</H2>'),									// Heading 2
									"Heading 3" => array('<H3 style="'.$dB.' background-color:#99ccFF;">','</H3>'),									// Heading 3
									"Heading 4" => array('<H4 style="'.$dB.' background-color:#cceeFF;">L4: ','</H4>'),									// Heading 4
									"Heading 5" => array('<H5 style="'.$dB.' background-color:#eeeeFF;">L5: ','</H5>'),									// Heading 5

										// DEFAULT (non-rendered)
									"_default" => array('<p style="color: #999999;'.$dB.' background-color:red;">','</p>'),										// [everything else...]
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
		$query = "SELECT * FROM tx_oodocs_filestorage WHERE rel_id=".intval($this->ooDocObj->ext_ID);
		$res = mysql(TYPO3_db,$query);
		while($row=mysql_fetch_assoc($res))	{
			$fileArray[$row["filepath"]]=$row;
		}

			// Setting Author title:
		if ($fileArray["meta.xml"])	{
			$XML_content = gzuncompress($fileArray["meta.xml"]["content"]);
			if ($XML_content)	{
				$p = xml_parser_create();
				xml_parse_into_struct($p,$XML_content,$vals,$index);
				xml_parser_free($p);

				$structure = $this->ooDocObj->indentSubTagsRec($vals,999);
#				$structure[0]["subTags"][0]["subTags"]["12"]["value"]=utf8_encode("Alfons �erg");
				if ($structObj = &$this->ooDocObj->getObjectFromStructure($structure,"OFFICE:DOCUMENT-META/OFFICE:META/META:USER-DEFINED#2"))	{
					$structObj["value"]=utf8_encode("Alfons �erg");
					unset($structObj);
				}
				$fileArray["meta.xml"]["content"] = gzcompress($this->ooDocObj->compileDocument($structure));
			}
		}
			// Using the stylesheet of another document:
		$sxwfile='doc/manual.sxw';
		$extKeyRec = $this->getExtKeyRecord("test_deaez");
		$placeholder = $this->getTocPHElement($extKeyRec["uid"],$sxwfile);
		if (is_array($placeholder))	{
			$query = "SELECT * FROM tx_oodocs_filestorage WHERE rel_id=".intval($placeholder["cur_oodoc_ref"])." AND filepath='styles.xml'";
			$res = mysql(TYPO3_db,$query);
			if($row=mysql_fetch_assoc($res))	{
				$XML_content = gzuncompress($row["content"]);
				if ($XML_content)	{
					$p = xml_parser_create();
					xml_parse_into_struct($p,$XML_content,$vals,$index);
					xml_parser_free($p);

						// Parsing structure:
					$structure = $this->ooDocObj->indentSubTagsRec($vals,999);
						// Finding footer image:
					if ($structObj = &$this->ooDocObj->getObjectFromStructure($structure,"OFFICE:DOCUMENT-STYLES/OFFICE:MASTER-STYLES/STYLE:MASTER-PAGE/STYLE:FOOTER/TEXT:P/DRAW:IMAGE"))	{
						if (substr($structObj["attributes"]["XLINK:HREF"],0,10)=="#Pictures/")	{
							$query = "SELECT * FROM tx_oodocs_filestorage WHERE rel_id=".intval($placeholder["cur_oodoc_ref"])." AND filepath='".substr($structObj["attributes"]["XLINK:HREF"],1)."'";
							$res = mysql(TYPO3_db,$query);
							if($imgrow=mysql_fetch_assoc($res))	{
								$newPath = "Pictures/".md5($imgrow["filepath"]).".png";
								$fileArray[$newPath]=$imgrow;
								$fileArray[$newPath]["filepath"]=$newPath;
								$fileArray[$newPath]["filename"]=md5($imgrow["filepath"]).".png";
								$structObj["attributes"]["XLINK:HREF"]="#".$newPath;
debug($structObj);
#								$structObj["attributes"]["SVG:WIDTH"]="5.345cm";
#								debug($structObj);
							}
						}
					}
					$fileArray["styles.xml"]["content"] = gzcompress($this->ooDocObj->compileDocument($structure));
#					debug($structure);
				}
			}
		}

			// CONTENT!
		if ($fileArray["content.xml"])	{
			$XML_content = gzuncompress($fileArray["content.xml"]["content"]);
			if ($XML_content)	{
				$p = xml_parser_create();
				xml_parse_into_struct($p,$XML_content,$vals,$index);
				xml_parser_free($p);

				$structure = $this->ooDocObj->indentSubTagsRec($vals,999);
#debug($structure);
				// D��				if ($structObj = &$this->ooDocObj->getObjectFromStructure($structure,"OFFICE:DOCUMENT-CONTENT/OFFICE:BODY"))	{
					$structObj["subTags"][]=array(
						"tag" => "TEXT:P",
						"type" => "complete",
						"attributes" => array("TEXT:STYLE-NAME"=>"Text body"),
						"value" => utf8_encode("Kasper Sk�hj ���"),
						"subTags" => array(
							array(
								"tag" => "DRAW:IMAGE",
								"type" => "complete",
								"attributes" => array(
									"DRAW:NAME" => "Graphic 2",
									"TEXT:ANCHOR-TYPE" => "paragraph",
									"XLINK:HREF" => "#Pictures/9f4044bcb768a78cb613720168ba4a14.png",
									"XLINK:TYPE" => "simple",
									"XLINK:SHOW" => "embed",
									"XLINK:ACTUATE" => "onLoad",
								)
							)
						)
					);

					$styleObj = &$this->ooDocObj->getObjectFromStructure($structure,"OFFICE:DOCUMENT-CONTENT/OFFICE:AUTOMATIC-STYLES");
#					debug($styleObj);

					$register=array();
					reset($styleObj["subTags"]);
					while(list($k4,$v4)=each($styleObj["subTags"]))	{
						$register[substr($v4["attributes"]["STYLE:NAME"],0,1)][]=substr($v4["attributes"]["STYLE:NAME"],1);
					}

					$styleCache=array();
#debug($register);

					$parts = $this->_temp_get_oodoc_element(200);
#					$fullStruct = $this->ooDocObj->indentSubTagsRec($parts[2],999);
					reset($parts[1]);
					while(list($kk,$vv)=each($parts[1]))	{
						if (is_array($vv["subTags"]))	{
							reset($vv["subTags"]);
							while(list($kkk,$vvv)=each($vv["subTags"]))	{
								if ($vvv["attributes"]["TEXT:STYLE-NAME"])	{
									$st=$vvv["attributes"]["TEXT:STYLE-NAME"];
									if (t3lib_div::inList("P,T",substr($st,0,1)) && t3lib_div::testInt(substr($st,1)))	{
										if (!isset($styleCache[$st]))	{
											reset($parts[2]->officeStyles);
											while(list($k4,$v4)=each($parts[2]->officeStyles))	{
												if ($v4["attributes"]["STYLE:NAME"]==$st)	{
													if (is_array($register[substr($st,0,1)]))	{
														$n=max($register[substr($st,0,1)])+1;
													} else $n=1;
													$v4["attributes"]["STYLE:NAME"]=substr($st,0,1).$n;
													$styleObj["subTags"][]=$v4;
													$styleCache[$st]=substr($st,0,1).$n;
#debug("!");
												}
											}
										}
										if (isset($styleCache[$st]))	{
											$parts[1][$kk]["subTags"][$kkk]["attributes"]["TEXT:STYLE-NAME"]=$styleCache[$st];
										}
									}
								}
							}
						}
					}
#debug($parts[1]);
#debug($styleObj);

					$parts[1] = $this->ooDocObj->indentSubTagsRec($parts[1],999);

					$structObj["subTags"]=array_merge($structObj["subTags"],$parts[1]);
#					debug($structObj);
				}
debug($structure);
				$fileArray["content.xml"]["content"] = gzcompress($this->ooDocObj->compileDocument($structure));
			}
		}


		$msg = $this->ooDocObj->fileArrayToSxw($fileArray,$this->ooDocObj->compressedStorage,PATH_site."typo3temp/writeOOdoc.sxw");
		debug($msg);
		debug(array_keys($fileArray));

*/


						// DELETES the temporary file and oodoc-filestorage position IF that should be done (review-previews...)
					if ($DELETE_file && t3lib_div::isFirstPartOfStr($DELETE_file,PATH_site."typo3temp/oodocreview_"))	{
						unlink($DELETE_file);
						$query = "DELETE FROM tx_oodocs_filestorage WHERE rel_id=".intval($this->ooDocObj->ext_ID);
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

	/**
	 * Returns a list mode selector, clickmenu in a table.
	 *
	 * @param	[type]		$items: ...
	 * @return	[type]		...
	 */
	function pi_list_modeSelector($items=array())	{
			// Making menu table:
		$cells=array();
		reset($items);
		while(list($k,$v)=each($items))	{
			$cells[]='<td'.($this->piVars["mode"]==$k?$this->pi_classParam("modeSelector-SCell"):"").'><P>'.$this->pi_linkTP($v,Array($this->prefixId=>array("mode"=>$k))).'</P></td>';
			;
		}

		$sTables = '<DIV'.$this->pi_classParam("modeSelector").'><table>
			<tr>'.implode("",$cells).'</tr>
		</table></DIV>';
		return $sTables;
	}





	/*********************************************************
	 *
	 * 	Open Office Functions
	 *
	 *********************************************************/

	/**
	 * Rendering master table-of-contents
	 *
	 * @return	[type]		...
	 */
	function renderMasterToc()	{

			// MAKE MENU:
		$mItems =array();
		$mItems[""]='Introduction';
		$mItems["startHere"]='Start here!';
		$mItems["matrix"]='Matrix';
		$mItems["fullToc"]='Full TOC';
		$mItems["glossary"]='Glossary';

		$topmenu='';
		reset($mItems);
		while(list($kk,$vv)=each($mItems))	{
			$topmenu.='<td'.($this->piVars["show"]==$kk?$this->pi_classParam("SCell"):'').'>'.$this->pi_linkTP_keepPIvars(htmlentities($vv),array("show"=>$kk),1).'</td>';
		}
		$topmenu='<table '.$this->conf["toc."]["tableParams_topmenu"].$this->pi_classParam("topmenu").'>'.$topmenu.'</table>';




			// Getting languages
		switch($this->piVars["show"])	{
			case 'matrix':
			case "fullToc":
				$query = "SELECT * FROM tx_extrepmgm_langadmin WHERE ".
						"pid=".intval($this->dbPageId).
						$this->cObj->enableFields("tx_extrepmgm_langadmin").
						" ORDER BY crdate";
				$res = mysql(TYPO3_db,$query);
					// For each language:
				$lang=array();
				$lang[0]["flag"]='?';
				while($lrow=mysql_fetch_assoc($res))	{
					$langKey = $lrow["langkey"];
					$langKey = $langKey=="default" ? "uk" : $langKey;
					$flag = 'media/flags/flag_'.$langKey.'.gif';
					$lang[$lrow["uid"]]["flag"]=is_file(PATH_site.$flag) ? '<img src="'.$flag.'" width=21 height=13 title="'.$lrow["title"].'">' : $langKey;
				}



					// Finding all extensions with manuals:
				$query = 'SELECT tx_extrepmgm_oodoctoc.*,tx_extrep_keytable.tx_extrepmgm_documentation,tx_extrep_keytable.tx_extrepmgm_nodoc_flag,tx_extrep_keytable.tx_extrepmgm_rev FROM tx_extrep_keytable,tx_extrepmgm_oodoctoc WHERE
								tx_extrep_keytable.uid=tx_extrepmgm_oodoctoc.extension_uid
								AND tx_extrep_keytable.members_only=0
								'.$this->cObj->enableFields("tx_extrep_keytable").
								' ORDER BY (tx_extrep_keytable.tx_extrepmgm_nodoc_flag && 4) DESC';

				$res=mysql(TYPO3_db,$query);
				echo mysql_error();
				$uidL=array();
				while($r=mysql_fetch_assoc($res))	{
					$r["doc_title"] = trim(ereg_replace('^EXT:','',$r["doc_title"]));

					$cK = $r["cat"];
					if ($cK<0 || $cK>(count($this->docCats)-1))	$cK=0;
					$cats[$cK][]=$r;
					$catsSort[$cK][]=strtolower(ereg_replace('[^[:alnum:]]*','',$r["doc_title"]));
				}
		#debug($cats);

				$totalDocs=0;
				$totalWords=0;
				$totalCharacters=0;
				$totalPages=0;
				$totalImages=0;
				$totalTables=0;

				$rows=array();
				if ($this->piVars["show"]=='matrix')	{
					$hR='';
					reset($this->kinds);
					while(list($kkey,$kstr)=each($this->kinds))	{
						$hR.='<td nowrap>'.$this->kinds_short[$kkey].'</td>';
					}
					$rows[]='<tr'.$this->pi_classParam("HRow").'>
						<td>SXW</td>
						<td>PDF</td>
						<td>Title:</td>
						<td>Pages:</td>
						<td>&nbsp;</td>
						'.$hR.'
					</tr>';
				}

				reset($this->docCats);
				while(list($k,$v)=each($this->docCats))	{
					if (is_array($cats[$k]))	{
						asort($catsSort[$k]);

						switch($this->piVars["show"])	{
							case "fullToc":
								$rows[]='<h2>'.$v.'</h2>';

								foreach($catsSort[$k] as $dokPointer => $docTitle)	{
									$rec = $cats[$k][$dokPointer];

									$totalDocs++;
									$totalWords+=$rec["doc_words"];
									$totalCharacters+=$rec["doc_chars"];
									$totalPages+=$rec["doc_pages"];
									$totalImages+=$rec["doc_images"];
									$totalTables+=$rec["doc_tables"];


									$cachedToc = unserialize($rec["toc_cache"]);
									$this->linearToc = $cachedToc["linearToc"];
									$this->linearTocOrder = $cachedToc["linearTocOrder"];
									$tocHTML = $this->renderTOC_main($rec["extension_uid"],$this->linearToc);

									$rows[]='<h3>'.$this->linkToExtension('<img src="t3lib/gfx/zoom2.gif" width="12" height="12" border="0" alt="Extension info..." title="Extension info..." align="absmiddle" style="margin-right: 5px;">',$rec["extension_uid"]).
												$this->linkToDocumentation($rec["doc_title"],$rec["extension_uid"]).'</h3>';
									$rows[]=$tocHTML;
								}
							break;
							case "matrix":
								$rows[]='<tr>
									<td colspan="'.(count($this->kinds)+3).'"><h3>'.$v.'</h3></td>
								</tr>';

								foreach($catsSort[$k] as $dokPointer => $docTitle)	{
									$rec = $cats[$k][$dokPointer];

									$cells=array();
									$totalDocs++;
									$totalWords+=$rec["doc_words"];
									$totalCharacters+=$rec["doc_chars"];
									$totalPages+=$rec["doc_pages"];
									$totalImages+=$rec["doc_images"];
									$totalTables+=$rec["doc_tables"];

									if ($tocPHrec=$this->getTocPHElement($rec['extension_uid'],'doc/manual.sxw'))	{
										$cells[]='<td><a href="'.$tocPHrec["cur_tmp_file"].'" target="_blank"><img src="t3lib/gfx/fileicons/sxw.gif" width="18" height="16" title="'.htmlspecialchars('Download as OpenOffice Writer document ('.t3lib_div::formatSize(@filesize(PATH_site.$tocPHrec["cur_tmp_file"])).'bytes)').'" border="0" /></a></td>';
									} else {
										$cells[]='<td>&nbsp;</td>';
									}

									$sxwNameAsPDF = 'fileadmin/pdf_manuals/'.substr(basename($tocPHrec["cur_tmp_file"]),0,-4).'.pdf';
									if (@is_file(PATH_site.$sxwNameAsPDF))	{
										$cells[]='<td><a href="'.$sxwNameAsPDF.'" target="_blank"><img src="t3lib/gfx/fileicons/pdf.gif" width="18" height="16" title="'.htmlspecialchars('Download as PDF ('.t3lib_div::formatSize(filesize(PATH_site.$sxwNameAsPDF)).'bytes)').'" border="0" /></a></td>';
									} else {
										$cells[]='<td>&nbsp;</td>';
									}

									$cells[]='<td nowrap'.(($rec["tx_extrepmgm_nodoc_flag"]&2)?' class="important"':'').'>'.$this->linkToDocumentation(t3lib_div::fixed_lgd($rec["doc_title"],40),$rec["extension_uid"]).'</td>';
									$cells[]='<td align="center">'.$rec["doc_pages"].'</td>';
									$cells[]='<td>'.$lang[$rec["lang"]]["flag"].'</td>';

									$docInfo = unserialize($rec["tx_extrepmgm_documentation"]);

									reset($this->kinds);

									while(list($kkey,$kstr)=each($this->kinds))	{
										$tdP = $kkey%2 ? $this->pi_classParam("OCol") : $this->pi_classParam("ECol") ;
										$tdP.= ' align="center"';
										if ($docInfo["doc_kind"][$kkey])	{
											$kstr='<img src="t3lib/gfx/icon_ok.gif" width="18" height="16" border="0" alt="'.$kstr.'">';
											$cells[]='<td'.$tdP.' nowrap>'.$this->linkToDocumentation($kstr,$rec["extension_uid"],$docInfo["doc_kind"][$kkey]).'</td>';
										} else {
											$cells[]='<td'.$tdP.'>&nbsp;</td>';
										}
									}

									$rows[]='<tr>
										'.implode("",$cells).'
									</tr>';
								}
							break;
						}
					}
				}

				$output=$topmenu;
				switch($this->piVars["show"])	{
					case "fullToc":
						$output.=implode(chr(10),$rows);
					break;
					case "matrix":
						$output.='<table '.$this->conf["toc."]["tableParams"].$this->pi_classParam("matrix").'>'.implode("",$rows).'</table>';
					break;
				}

				$output.='<table '.$this->conf["toc."]["tableParams_status"].$this->pi_classParam("status").'>
					<tr>
						<td>Total documents:</td>
						<td>Total words:</td>
						<td>Total characters:</td>
						<td>Total pages:</td>
						<td>Total images:</td>
						<td>Total tables:</td>
					</tr>
					<tr>
						<td>'.$totalDocs.'</td>
						<td>'.$totalWords.'</td>
						<td>'.$totalCharacters.'</td>
						<td>'.$totalPages.'</td>
						<td>'.$totalImages.'</td>
						<td>'.$totalTables.'</td>
					</tr>
				</table>';
			break;
			case 'startHere':
				$output=$topmenu;
				$output.=$this->cObj->cObjGetSingle($this->conf['cObj.']['docView_startHere'],$this->conf['cObj.']['docView_startHere.']);
			break;
			case 'glossary':
				$output=$topmenu;
				$output.=$this->cObj->cObjGetSingle($this->conf['cObj.']['docView_glossary'],$this->conf['cObj.']['docView_glossary.']);
			break;
			default:
				$output=$topmenu;
				$output.=$this->cObj->cObjGetSingle($this->conf['cObj.']['docView_overview'],$this->conf['cObj.']['docView_overview.']);
			break;
		}

		return '<div'.$this->pi_classParam("toc").'>'.$output.'</div>';
	}

	/**
	 * Rendering OOW document for extension
	 *
	 * @return	[type]		...
	 */
	function renderDocumentationForExtension()	{
		$currentUser = $this->validateUploadUser();
		$extUid = intval($this->piVars["extUid"]);

		$sxwfile="doc/manual.sxw";

		$query = "SELECT uid,members_only,owner_fe_user,extension_key,tx_extrepmgm_flags FROM tx_extrep_keytable WHERE uid=".$extUid.
					$GLOBALS["TSFE"]->sys_page->enableFields("tx_extrep_keytable");
		$res = mysql(TYPO3_db,$query);

		if ($row=mysql_fetch_assoc($res))	{
			$access = $this->checkUserAccessToExtension($row,$currentUser);
			$GLOBALS["TSFE"]->page_cache_reg1=$row["uid"];

#debug($access);
			if ($access)	{
				if ($tocPHrec=$this->getTocPHElement($extUid,$sxwfile))	{
					$this->cObj->lastChanged($tocPHrec["doc_mtime"]);
#debug(date("d-m-Y H:i",$tocPHrec["doc_mtime"]));

					$cachedToc = unserialize($tocPHrec["toc_cache"]);
					$this->linearToc = $cachedToc["linearToc"];
					$this->linearTocOrder = $cachedToc["linearTocOrder"];

					$tocHTML = $this->renderTOC($extUid,$this->linearToc);
#debug($this->linearToc);
#debug($this->linearTocOrder);
#debug($tocPHrec);
					$GLOBALS["TSFE"]->altPageTitle=$tocPHrec["doc_title"].": ";

					$copyInfo = 'Copyright &copy; '.$tocPHrec["doc_author"].($tocPHrec["doc_author_email"]?' &lt;'.$this->cObj->getTypoLink($tocPHrec["doc_author_email"],$tocPHrec["doc_author_email"]).'&gt;':'');
					$ocLicense = 'Published under the Open Content License available from http://www.opencontent.org/opl.shtml';

					$content = "";
					$tocEl=$this->piVars["tocEl"];
					if ($tocEl>0 && isset($this->linearToc[$tocEl]))	{
						$HTMLpath='<p'.$this->pi_classParam("path").'>Path: '.t3lib_div::fixed_lgd_pre($this->linearToc[$tocEl]["path"],100).'</p>';
							// NEXT/PREV
						$orderKey = $this->linearToc[$tocEl]["orderkey"];
						$tocLink='<span'.$this->pi_classParam("bbarSpan").'>'.$this->linkToDocumentation('Table Of Contents',$extUid).'</span>';

						$prev=$this->linearToc[$this->linearTocOrder[$orderKey-1]];
						$prev = is_array($prev) ? '<span'.$this->pi_classParam("bbarSpan").'>'.$this->linkToDocumentation('&lt;&lt; '.$prev["num"].' '.t3lib_div::fixed_lgd($prev["title"],30),$extUid,$prev["linkUid"]?$prev["linkUid"]:$prev["uid"]).'</span>' : '';

						$next=$this->linearToc[$this->linearTocOrder[$orderKey+1]];
						$next = is_array($next) ? '<span'.$this->pi_classParam("bbarSpan").'>'.$this->linkToDocumentation($next["num"].' '.t3lib_div::fixed_lgd($next["title"],30).' &gt;&gt;',$extUid,$next["linkUid"]?$next["linkUid"]:$next["uid"]).'</span>' : '';

						$HTMLnavbar= '<table '.$this->conf["doc."]["tableParams_bbar"].$this->pi_classParam("bbar").'><tr>
							<td width="30%" nowrap>'.$prev.'</p></td>
							<td width="30%" align="center" nowrap>'.$tocLink.'</td>
							<td width="30%" align="right" nowrap>'.$next.'</td>
							<td>'.$this->getAUDIcons(intval($this->linearToc[$tocEl]["aud_sum"])).'</td>
							</tr></table>';

							// Behver IKKE htmlspecialchars() da det vist allerede er tilfjet...
						$titleContent = $this->linearToc[$tocEl]["num"].' '.$this->linearToc[$tocEl]["title"];
						$GLOBALS["TSFE"]->altPageTitle.=$this->linearToc[$tocEl]["title"];

							// Render main section:
						$content.=$HTMLpath.$HTMLnavbar;

						$content.='<div'.$this->pi_classParam("cnt").'><H2>'.$titleContent.'</H2>';
						$content.= $this->renderOOdocSlice($extUid,$tocEl,0,1);

							// Sub sections, if any...
						if ($this->linearToc[$tocEl]["showAddSections"])	{
							$addSections = t3lib_div::intExplode(",",$this->linearToc[$tocEl]["showAddSections"]);
							reset($addSections);
							while(list(,$subTocEl)=each($addSections))	{
								$content.= '<a name="oodoc_part_'.$subTocEl.'"></a>'.$this->renderOOdocSlice($extUid,$subTocEl);
							}
						}
						$content.='</div>';

						if (t3lib_extMgm::isLoaded("t3annotation"))	{
							$annotationObj = t3lib_div::makeInstance("tx_t3annotation_pi1");
							$annotationObj->cObj = &$this->cObj;
							$annotationObj->initRel("EXTREP",$row["extension_key"].":doc:".$tocEl,$this->docPage);
							$annotationCode = $annotationObj->listAnnotations();
							if ($annotationCode)	{
								$content.='<h3'.$this->pi_classParam("annoHead").'>User annotations:</h3><div'.$this->pi_classParam("anno").'>'.$annotationCode.'</div>';
							}


#							if ($GLOBALS["TSFE"]->loginUser)	{		// DON'T CHECK FOR loginUser - we want the pages to be indexed the same whether or not there is a login
								$onlyM=1;
								if ($row["tx_extrepmgm_flags"]&2)	{
									$onlyM = $this->isUserMemberOrOwnerOfExtension($currentUser,$row);
								}
								if ($onlyM)		$content.='<p'.$this->pi_classParam("addcomment").'>'.$annotationObj->addCommentLink('Annotate documentation',$this->annoPage,$this->docPage,$extUid).'</p>';
#							}
						}

						$content.=$HTMLnavbar;

					} else {
						// Only TOC:
						$content.= '<div>
							<h2>'.$tocPHrec["doc_title"].'</h2>
							<p>'.$copyInfo.'</p>
							<p>'.$ocLicense.'</p>
							<h3>Table of Contents</h3>
							'.$tocHTML.'</div>';
						$GLOBALS["TSFE"]->altPageTitle.=htmlspecialchars("Table Of Contents");
					}
					$GLOBALS["TSFE"]->indexedDocTitle=$GLOBALS["TSFE"]->altPageTitle;

					$content.='<BR><p align="center"><a href="'.$tocPHrec["cur_tmp_file"].'" target="_blank"><strong><img src="t3lib/gfx/fileicons/sxw.gif" width="18" height="16" hspace="2" border="0" align="absmiddle" alt="" />'.$tocPHrec["doc_title"].'</strong></a> - '.$copyInfo.' - Ext: '.$this->linkToExtension($row["extension_key"],$row["uid"]).'<br>
							'.$ocLicense.'<BR>
							Last modified: '.date("d-m-Y H:i",$tocPHrec["doc_mtime"]).', '.t3lib_div::formatSize($tocPHrec["doc_size"]).'bytes, '.$tocPHrec["doc_pages"].' pages, '.$tocPHrec["doc_words"].' words, '.$tocPHrec["doc_chars"].' characters.</p>';

					return '<div'.$this->pi_classParam("doc").'>'.$content.'</div>';
				} else return 'ERROR: No Table of Contents';
			} else $content='<p>Error: You did not have access to this extension "'.$extUid.'"</p>';
		} else $content='<p>Error: Couldn\'t find extension "'.$extUid.'"</p>';

		return $content;
	}

	/**
	 * Render oodoc slice
	 *
	 * @param	[type]		$extUid: ...
	 * @param	[type]		$tocEl: ...
	 * @param	[type]		$sameLevel: ...
	 * @param	[type]		$offsetFirst: ...
	 * @return	[type]		...
	 */
	function renderOOdocSlice($extUid,$tocEl,$sameLevel=0,$offsetFirst=0)	{
#$pt=t3lib_div::milliseconds();
			// Try cache first:
		$cache_hash = hexdec(substr(md5($extUid.'|'.$tocEl.'|'.$sameLevel.'|'.$offsetFirst),0,7));
#debug(array($extUid.'|'.$tocEl.'|'.$sameLevel.'|'.$offsetFirst,$cache_hash));
		$query = "SELECT content FROM tx_extrepmgm_oodoccache WHERE cache_ref=".intval($cache_hash);
		$res=mysql(TYPO3_db,$query);
		if ($row=mysql_fetch_assoc($res))	{

#debug(t3lib_div::milliseconds()-$pt);
#debug("-cache");
			return $row["content"];
		} else {
			$query='SELECT tx_extrepmgm_oodoctocel.*,tx_extrepmgm_oodoctoc.cur_tmp_file
					FROM tx_extrepmgm_oodoctocel,tx_extrepmgm_oodoctoc
					WHERE tx_extrepmgm_oodoctocel.document_unique_ref = tx_extrepmgm_oodoctoc.document_unique_ref
					AND tx_extrepmgm_oodoctocel.extension_uid='.intval($extUid).'
					AND tx_extrepmgm_oodoctocel.uid='.intval($tocEl);

			$res = mysql(TYPO3_db,$query);
			echo mysql_error();
	#debug(mysql_num_rows($res));
			if ($tocElRec=mysql_fetch_assoc($res))	{
#debug($tocElRec);

					// Make OOdoc instance
				$idKey="PI:extrep.".$extUid.".".$tocElRec["cur_tmp_file"];
				$doLoad = strcmp($this->ooDocObj_loaded,$idKey)?1:0;
				$this->ooDocObj_loaded=$idKey;

#debug($doLoad);
				if ($doLoad)	{
					$this->ooDocObj = t3lib_div::makeInstance("tx_oodocs");
					$this->ooDocObj->compressedStorage=1;
					$this->ooDocObj->designConf["tableParams"] = 'border=0 cellpadding=2 cellspacing=0';
					$this->ooDocObj->PstyleMap["Table Contents/PRE"] = array ('<pre class="inTable">','</pre>', 1);
				}

				$tempFile=PATH_site.$tocElRec["cur_tmp_file"];
				if (@is_file($tempFile))	{
					$e = $doLoad ? $this->ooDocObj->init($tempFile,$idKey) : '';
					if (!$e)	{
	/*
									$dB = 'border: 1px dotted #cccccc;';
									$this->ooDocObj->PstyleMap = array(														// THIS IS THE NAMES of the styles (from the "Automatic" palette) in OpenOffice Writer 1.0 :
											// Bodytext formats
										"Text body" => array ('<p class="tx-oodocs-TB" style="'.$dB.'">','</p>'),				// "Text body"
										"Preformatted Text" => array ('<pre style="margin: 0px 0px 0px 0px; line-height:100%; font-size: 11px;'.$dB.'">','</pre>', 1),						// "Preformatted Text" (HTML-menu)
										"Table Contents" => array ('<p class="tx-oodocs-TC" style="'.$dB.' background-color:#eeffff;">','</p>'),			// "Table Contents"
										"Table Contents/PRE" => array ('<pre style="margin: 0px 0px 0px 0px; line-height:100%; font-size: 11px;'.$dB.' background-color:#eeffff;">','</pre>', 1),			// "Table Contents"
										"Table Heading" => array ('<p class="tx-oodocs-TH" style="'.$dB.' background-color:#ffffee;">','</p>'),			// "Table Heading"

											// Headers:
										"Heading 1" => array('<H1 style="'.$dB.' background-color:#000066; color:white;">','</H1>'),									// Heading 1
										"Heading 2" => array('<H2 style="'.$dB.' background-color:#0099cc;">','</H2>'),									// Heading 2
										"Heading 3" => array('<H3 style="'.$dB.' background-color:#99ccFF;">','</H3>'),									// Heading 3
										"Heading 4" => array('<H4 style="'.$dB.' background-color:#cceeFF;">L4: ','</H4>'),									// Heading 4
										"Heading 5" => array('<H5 style="'.$dB.' background-color:#eeeeFF;">L5: ','</H5>'),									// Heading 5

											// DEFAULT (non-rendered)
										"_default" => array('<p style="color: #999999;'.$dB.' background-color:red;">','</p>'),										// [everything else...]
									);
	*/
	/*								debug(array(
										$this->ooDocObj->file,
										$this->ooDocObj->mtime,
										$this->ooDocObj->fileHash,
										$this->ooDocObj->ext_ID
									));
	*/
						if ($doLoad)	$this->ooDocObj->prepareOfficeBodyArray();


							// Slicing the content to display the part we want:
							// Finding arr_key of next part:
						$query='SELECT xmlarr_index FROM tx_extrepmgm_oodoctocel WHERE
							extension_uid='.intval($extUid).'
							AND arr_key>'.intval($tocElRec["arr_key"]).
							($sameLevel?' AND hlevel='.intval($tocElRec["hlevel"]):'').
							' ORDER BY arr_key LIMIT 1';
						$res = mysql(TYPO3_db,$query);
						if ($tocElRec_next = mysql_fetch_assoc($res))	{
							$oBody = array_slice(
									$this->ooDocObj->officeBody,
									$tocElRec["xmlarr_index"]+$offsetFirst,
									$tocElRec_next["xmlarr_index"]-$tocElRec["xmlarr_index"]-$offsetFirst);
#debug(array($tocElRec["xmlarr_index"],$offsetFirst,$tocElRec_next["xmlarr_index"]));
						} else {
							$oBody = array_slice(
									$this->ooDocObj->officeBody,
									$tocElRec["xmlarr_index"]+$offsetFirst);
						}
						$res2 = $this->ooDocObj->renderOOBody($oBody);
						$content.=implode(chr(10),$res2);

						$content = $this->cObj->stdWrap($content,$this->conf["doc."]["content_stdWrap."]);
#debug(t3lib_div::milliseconds()-$pt);

							// Insert into cache:
						$query = 'INSERT INTO tx_extrepmgm_oodoccache (cache_ref,document_unique_ref,content,tstamp) VALUES ('.intval($cache_hash).','.intval($extUid).',"'.addslashes($content).'",'.time().')';
						$res=mysql(TYPO3_db,$query);
						echo mysql_error();

						return $content;
					} else return "ERROR: The OOdoc parser returned an error: ".$e; // $e
				} else return "ERROR: No temporary sxw file found in '".$tempFile."'"; // is_file
			} else return "ERROR: No toc-record by that UID for this extension uid"; // tocElRec
		}
	}

	/**
	 * Clears document page cache content for extension
	 *
	 * @param	[type]		$extUid: ...
	 * @return	[type]		...
	 */
	function clearPageCacheForExtensionDoc($extUid)	{
		$query = "DELETE FROM cache_pages WHERE page_id=".intval($this->docPage)." AND reg1=".intval($extUid);
t3lib_div::debug($query);
		$res = mysql (TYPO3_db, $query);
	}

	/**
	 * Clearing cache for OOdoc slices for a certain extension uid.
	 *
	 * @param	[type]		$extUid: ...
	 * @return	[type]		...
	 */
	function clearOOsliceCache($extUid)	{
		$query = "DELETE FROM tx_extrepmgm_oodoccache WHERE document_unique_ref=".intval($extUid);
#debug($query);
		$res = mysql (TYPO3_db, $query);
	}

	/**
	 * Get the OOdocument $sxwfile (filename) from repository record $row2
	 * Returns blank string on success, otherwise error msg.
	 *
	 * @param	[type]		$row2: ...
	 * @param	[type]		$sxwfile: ...
	 * @return	[type]		...
	 */
	function getOOdoc($row2,$sxwfile)	{
		$this->oodoc_tempFile="";
		$this->oodoc_idKey="";

		$datStr = gzuncompress($row2["datablob"]);
		if (md5($datStr)==$row2["datablob_md5"])	{
			$dB = unserialize($datStr);

				// Finding the manual
			if (is_array($dB[$sxwfile]))	{
#				$fileRelName = "typo3temp/extrep_manual_".$dB[$sxwfile]["content_md5"].".sxw";
				$fileRelName = "typo3temp/manual-".$row2["extension_key"]."-".date("d-m-Y_H-i-s",$dB[$sxwfile]["mtime"]).".sxw";
				$tempFile = PATH_site.$fileRelName;
				if (!is_file($tempFile))	{
					t3lib_div::writeFile($tempFile,$dB[$sxwfile]["content"]);
				}

				if (is_file($tempFile))	{
					$this->ooDocObj = t3lib_div::makeInstance("tx_oodocs");
					$this->ooDocObj->compressedStorage=1;

#					$idKey="PI:extrep.".$row2["extension_uid"].".".$sxwfile;
					$idKey="PI:extrep.".$row2["extension_uid"].".".$fileRelName;
					$e = $this->ooDocObj->init($tempFile,$idKey);

					if (!$e)	{
						$this->oodoc_tempFile=$tempFile;
						$this->oodoc_idKey=$idKey;
						return "";
					} else $content='Error: '.$e;
				} else $content='Could not write temporary Open Office writer file to disk!';
			} else $content='Error: Couldn\'t find file "'.$sxwfile.'" for extension "'.$row2["extension_uid"].'"';
		} else $content='Error: MD5 hash of stored extension content was faulty.';

		return $content;
	}






	// ***************************
	// TSconfig Property Tables:
	// ***************************

	/**
	 * Based on a loaded oo-document, this traverses the whole thing and finds all tables with TS properties in.
	 *
	 * @param	[type]		$extUid: ...
	 * @return	[type]		...
	 */
	function findOOdocTables($extUid)	{
		if (t3lib_extMgm::isLoaded("tsconfig_help"))	{
			$query = "DELETE FROM static_tsconfig_help WHERE guide=".intval($extUid);
#debug(array($query));
			$res = mysql(TYPO3_db,$query);

			reset($this->ooDocObj->officeBody);
			while(list($k,$part)=each($this->ooDocObj->officeBody))	{
				if ($part["tag"]=="TABLE:TABLE" && $this->ooDocObj->officeBody[$k+1]["tag"]=="TEXT:P")	{
					$tableIdent = implode("",$this->ooDocObj->renderOOBody(array($this->ooDocObj->officeBody[$k+1])));
					$input = trim(strip_tags($tableIdent));
					$pp=explode("]",$input);

					$tableIdString = trim(substr($pp[0],1));
					if ($tableIdString && substr($pp[0],0,1)=="[" && count($pp)==2)	{
						$tableHTML = implode("",$this->ooDocObj->renderOOBody(array($part)));
						$tableCode = $this->getPropertyTable($tableHTML,1);
					}
	#				debug($this->ooDocObj->officeBody[$k+1]);
	#				debug($tableIdString);
	#				debug($tableHTML);
	#				debug($tableCode);
					$md5 = md5(ereg_replace("[[:space:]]","",$tableIdString));

					$query = "INSERT INTO static_tsconfig_help
					 	(guide,md5hash,description,obj_string,appdata,title) VALUES
						(".intval($extUid).", '".$md5."', '', '".addslashes($tableIdString)."', '".addslashes(serialize($tableCode))."', '')";
#debug(array($query));
					$res = mysql(TYPO3_db,$query);
					echo mysql_error();
				}
			}
		}
	}

	/**
	 * [Describe function...]
	 *
	 * @param	[type]		$tableCode: ...
	 * @param	[type]		$all: ...
	 * @return	[type]		...
	 */
	function getPropertyTable($tableCode,$all=0)	{
		$parser = t3lib_div::makeInstance("t3lib_parsehtml");
		$tableBody=$parser->getAllParts($parser->splitIntoBlock("tr",$tableCode,1),1,0);

		reset($tableBody);

			// Header:
		$thParts = $parser->getAllParts($parser->splitIntoBlock("th",current($tableBody),1),1,0);
		$colMap=array();
		reset($thParts);
		while(list($k,$thV)=each($thParts))	{
			$thV = ereg_replace("[^[:alnum:]]*","",trim(strtolower(strip_tags($thV))));
			$colMap[$thV]=$k;
		}
	//debug($colMap);
	//debug($thParts);

		if (count($colMap) && ((isset($colMap["property"]) && isset($colMap["description"]))||$all))	{
			next($tableBody);
			$table["rows"]=array();
			while(list(,$v)=each($tableBody))	{
				$tdParts = $parser->getAllParts($parser->splitIntoBlock("td",$v,1),1,0);
				if (count($tdParts))	{
					if (isset($colMap["property"]) && isset($colMap["description"]))	{
						$table["rows"][] = array (
							"property" => trim(strip_tags($tdParts[$colMap["property"]])),
							"datatype" => trim($this->cleanUpText($tdParts[$colMap["datatype"]])),
							"description" => trim($this->cleanUpText($tdParts[$colMap["description"]])),
							"default" => trim($this->cleanUpText($tdParts[$colMap["default"]])),
							"column_count" => count($tdParts),
							"is_propertyTable" => 1
						);
					} else {
						$table["rows"][] = array (
							"property" => trim(strip_tags($tdParts[0])),
							"datatype" => trim($this->cleanUpText($tdParts[1])),
							"description" => trim($this->cleanUpText($tdParts[2])),
							"default" => trim($this->cleanUpText($tdParts[3])),
							"column_count" => count($tdParts)
						);
					}
				}
			}
			return $table;
	//		echo printTable($table);
		} else {
			debug("Skipping table");
		}
	}

	/**
	 * [Describe function...]
	 *
	 * @param	[type]		$text: ...
	 * @return	[type]		...
	 */
	function cleanUpText($text)	{
		$parser = t3lib_div::makeInstance("t3lib_parsehtml");
		$textBody=$parser->getAllParts($parser->splitIntoBlock("p,h1,h2,h3,h4,h5",$text,1),1,0);

		reset($textBody);
		$lines=array();
		while(list(,$v)=each($textBody))	{
			$lines[]=str_replace(chr(10),"",str_replace("<br>",chr(10),$parser->stripTagsExcept(trim($v),"b,u,i,br,p")));
		}
		return implode(chr(10),$lines);
	}






	// *****************
	// TOC stuff:
	// *****************

	/**
	 * Renderes the TOC for HTML display
	 *
	 * @param	[type]		$extUid: ...
	 * @param	[type]		$toc: ...
	 * @return	[type]		...
	 */
	function renderTOC($extUid,$toc)	{
		$lines=array();
		if (is_array($toc))	{
			reset($toc);
			while(list($uid,$tocRec)=each($toc))	{
				$level = t3lib_div::intInRange($tocRec["hlevel"],1,3);
				$title = $tocRec["num"].' '.$tocRec["title"];

				$tocUid = $tocRec["linkUid"]?$tocRec["linkUid"]:$tocRec["uid"];
				$anchor="";
				if ($tocRec["level3"])	{
					$tocUid=$tocRec["level3"];
					$anchor="oodoc_part_".$tocRec["uid"];
				}


				$titleTxt = $this->linkToDocumentation($title, $extUid, $tocUid,$anchor);

				$lines[]='<tr class="level'.$level.'">
					<td colspan="'.$level.'" align="right"><img src="'.t3lib_extMgm::siteRelPath("extrep_mgm").'res/dot'.($level).'.gif" width="8" height="8" border="0" vspace=3 hspace=8></td>
					<td colspan="'.(4-$level).'">'.$titleTxt.'</td>
				</tr>';
			}
			$lines[]='<tr>
				<td>&nbsp</td>
				<td>&nbsp</td>
				<td>&nbsp</td>
				<td>&nbsp</td>
			</tr>';
			$out='<table border=0 cellpadding=0 cellspacing=0'.$this->pi_classParam("toctable").'>'.implode("",$lines).'</table>';
		}
		return $out;
	}

	/**
	 * Renderes the TOC for main TOC HTML display
	 *
	 * @param	[type]		$extUid: ...
	 * @param	[type]		$toc: ...
	 * @return	[type]		...
	 */
	function renderTOC_main($extUid,$toc)	{
		$lines=array();
		if (is_array($toc))	{
			reset($toc);
			while(list($uid,$tocRec)=each($toc))	{
				if ($tocRec["hlevel"]<3)	{
					$level = t3lib_div::intInRange($tocRec["hlevel"],1,3);
					$title = $tocRec["num"].' '.$tocRec["title"];

					$tocUid = $tocRec["linkUid"]?$tocRec["linkUid"]:$tocRec["uid"];
					$anchor="";
					if ($tocRec["level3"])	{
						$tocUid=$tocRec["level3"];
						$anchor="oodoc_part_".$tocRec["uid"];
					}


					$titleTxt = $this->linkToDocumentation($title, $extUid, $tocUid,$anchor);

					$aud_text=$this->getAUDIcons(intval($tocRec["aud"]));

					$lines[]='<tr class="level'.$level.'">
						<td nowrap>'.$aud_text.'</td>
						<td colspan="'.$level.'" align="right"><img src="'.t3lib_extMgm::siteRelPath("extrep_mgm").'res/dot'.($level==1?1:3).'.gif" width="8" height="8" border="0" vspace=3 hspace=4></td>
						<td colspan="'.(3-$level).'">'.$titleTxt.'</td>
					</tr>';
				}
			}
			$lines[]='<tr>
				<td>&nbsp</td>
				<td>&nbsp</td>
				<td>&nbsp</td>
				<td>&nbsp</td>
			</tr>';
			$out='<table border=0 cellpadding=0 cellspacing=0'.$this->pi_classParam("toctable_main").'>'.implode("",$lines).'</table>';
		}
		return $out;
	}

	/**
	 * Returns Image tags with the icons indicating audience flags.
	 *
	 * @param	[type]		$audInt: ...
	 * @return	[type]		...
	 */
	function getAUDIcons($audInt)	{
		$aud_text='';
		$aud_text.=($audInt&1) ? '<img src="'.t3lib_extMgm::siteRelPath("extrep_mgm").'res/ta_users.gif" width="20" height="18" border="0" title="Users (content authors)" align="top">':'<img src="clear.gif" width=20 height=18 align="top">';
		$aud_text.=($audInt&2) ? '<img src="'.t3lib_extMgm::siteRelPath("extrep_mgm").'res/ta_admins.gif" width="20" height="18" border="0" title="Administrators" align="top">':'<img src="clear.gif" width=20 height=18 align="top">';
		$aud_text.=($audInt&4) ? '<img src="'.t3lib_extMgm::siteRelPath("extrep_mgm").'res/ta_dev.gif" width="20" height="18" border="0" title="Developers" align="top">':'<img src="clear.gif" width=20 height=18 align="top">';
		return $aud_text;
	}

	/**
	 * Prepares a linear version of the toc from hierarchical data
	 *
	 * @param	[type]		$hToc: ...
	 * @param	[type]		$path: ...
	 * @param	[type]		$num: ...
	 * @param	[type]		$nextSection: ...
	 * @param	[type]		$level3: ...
	 * @param	[type]		$aud_prev: ...
	 * @return	[type]		...
	 */
	function prepareTOCdata($hToc,$path=array(),$num=array(),$nextSection=array(),$level3=0,$aud_prev=0)	{
		reset($hToc["hToc"]);
		while(list($uid,$tocRec)=each($hToc["hToc"]))	{
				// Finding data for this entry
			$this->linearToc[$tocRec["dat"]["uid"]]=array(
				"title" => $tocRec["dat"]["stripped_value"],
				"next" => $tocRec["dat"]["stripped_next"],
				"path" => implode(" > ",array_merge($path,array($tocRec["dat"]["stripped_value"]))),
				"num" => implode(".",array_merge($num,array($uid))),
				"level3" => $level3,
				"uid" => $tocRec["dat"]["uid"],
				"hlevel" => $tocRec["dat"]["hlevel"],
				"aud" => $tocRec["dat"]["aud"],
				"aud_sum" => $tocRec["dat"]["aud"]?$tocRec["dat"]["aud"]:$aud_prev
			);

				// If stripped_next (indicating there is value in this section) then set nextSection and the "linkUid" value.
			if (strcmp($tocRec["dat"]["stripped_next"],""))	{
				$nextSection[]=$tocRec["dat"]["uid"];
				$this->linearToc[$tocRec["dat"]["uid"]]["linkUid"]=$tocRec["dat"]["uid"];
			}

			$l3 = $tocRec["dat"]["hlevel"]==2 && $tocRec["dat"]["show_level3"] ? $tocRec["dat"]["uid"] : 0;

			if (!$level3 && (strcmp($tocRec["dat"]["stripped_next"],"") || $l3))	$this->linearTocOrder[]=$tocRec["dat"]["uid"];
			end($this->linearTocOrder);
			$this->linearToc[$tocRec["dat"]["uid"]]["orderkey"] = key($this->linearTocOrder);

				// Getting sub sections
			$subNextSection=array();
			if (is_array($tocRec["sub"]["hToc"]))	{
				list($subNextSection) = $this->prepareTOCdata(
					$tocRec["sub"],
					array_merge($path,array($tocRec["dat"]["stripped_value"])),
					array_merge($num,array($uid)),
					array(),
					$level3?$level3:$l3,
					$tocRec["dat"]["aud"]?$tocRec["dat"]["aud"]:$aud_prev
				);

#				debug(array($tocRec["dat"]["stripped_value"],$subNextSection));
				$nextSection=array_merge($nextSection,$subNextSection);
			}

			if (!$this->linearToc[$tocRec["dat"]["uid"]]["linkUid"] && count($subNextSection))	{
				$this->linearToc[$tocRec["dat"]["uid"]]["linkUid"]=$subNextSection[0];
			}

			if ($l3)	{
				$this->linearToc[$tocRec["dat"]["uid"]]["showAddSections"]=implode(",",$subNextSection);
				$this->linearToc[$tocRec["dat"]["uid"]]["linkUid"]=$tocRec["dat"]["uid"];
			}

		}
		return array($nextSection);
	}

	/**
	 * Makes the form of the TOC which is used to assign meta data to the entries
	 *
	 * @param	[type]		$extRepEntry: ...
	 * @param	[type]		$sxwfile: ...
	 * @param	[type]		$edit: ...
	 * @return	[type]		...
	 */
	function generateTOCforMetaData($extRepEntry,$sxwfile,$edit=0)	{
		$saved_toc = $this->getTOCfromSavedOOdoc($extRepEntry["extension_uid"],$sxwfile,1);

		$kinds=$this->kinds;

		$types=array(
			1 => "FAQ",	// Target
			2 => "Examples",
			3 => "Reference",
		);

		// FLAGS: faq, reference, examples

		$docArray = unserialize($this->internal["currentRow"]["tx_extrepmgm_documentation"]);
#debug($docArray);

		if (t3lib_extMgm::isLoaded("t3annotation"))	{
			$annotationObj = t3lib_div::makeInstance("tx_t3annotation_pi1");
			$annotationObj->cObj = &$this->cObj;
		}

		$lines=array();
		reset($saved_toc);
		while(list(,$sDat)=each($saved_toc))	{
			$sel="";
			$flag="";
			$aud="";
			$level3="";

			$sel_text="";
			$flag_text="";
			$aud_text="";
			$level3_text="";

			if ($sDat["level"]==1 || $sDat["level"]==2)	{
				if ($edit)	{
					$aud.='<input type="hidden" name="'.$this->prefixId.'[DATA][tocElements_aud]['.$sDat["uid"].'][x]" value="0">';
					$aud.='<img src="'.t3lib_extMgm::siteRelPath("extrep_mgm").'res/ta_users.gif" width="20" height="18" border="0" title="Users (content authors)" align="top">';
					$aud.='<input type="checkbox" name="'.$this->prefixId.'[DATA][tocElements_aud]['.$sDat["uid"].'][U]" value="1"'.(($sDat["aud"]&1) ? 'CHECKED':'').' align="top">';
					$aud.='<img src="'.t3lib_extMgm::siteRelPath("extrep_mgm").'res/ta_admins.gif" width="20" height="18" border="0" title="Administrators" style="margin-left: 5px;" align="top">';
					$aud.='<input type="checkbox" name="'.$this->prefixId.'[DATA][tocElements_aud]['.$sDat["uid"].'][A]" value="1"'.(($sDat["aud"]&2) ? 'CHECKED':'').' align="top">';
					$aud.='<img src="'.t3lib_extMgm::siteRelPath("extrep_mgm").'res/ta_dev.gif" width="20" height="18" border="0" title="Developers" style="margin-left: 5px;" align="top">';
					$aud.='<input type="checkbox" name="'.$this->prefixId.'[DATA][tocElements_aud]['.$sDat["uid"].'][D]" value="1"'.(($sDat["aud"]&4) ? 'CHECKED':'').' align="top">';
				} else {
					$aud_text.=($sDat["aud"]&1) ? '<img src="'.t3lib_extMgm::siteRelPath("extrep_mgm").'res/ta_users.gif" width="20" height="18" border="0" title="Users (content authors)" align="top">':'<img src="clear.gif" width=20 height=18 align="top">';
					$aud_text.=($sDat["aud"]&2) ? '<img src="'.t3lib_extMgm::siteRelPath("extrep_mgm").'res/ta_admins.gif" width="20" height="18" border="0" title="Administrators" align="top">':'<img src="clear.gif" width=20 height=18 align="top">';
					$aud_text.=($sDat["aud"]&4) ? '<img src="'.t3lib_extMgm::siteRelPath("extrep_mgm").'res/ta_dev.gif" width="20" height="18" border="0" title="Developers" align="top">':'<img src="clear.gif" width=20 height=18 align="top">';
				}
			}
			if ($sDat["level"]==2)	{
				$opt=array();
				$opt[]='<option value=""></option>';
				reset($kinds);
				while(list($key,$text)=each($kinds))	{
					if (isset($docArray["doc_kind"][$key]))	{
						$issel = $docArray["doc_kind"][$key]==$sDat["uid"]?1:0;
						if ($issel)	$sel_text=$text;
					} else {
						$issel = $text==$sDat["stripped_value"] ? 2 : 0;
					}
					$opt[]='<option value="'.$key.'"'.($issel?' SELECTED':'').'>'.htmlspecialchars(($issel==2?'(?) ':'').$text).'</option>';
				}
				$sel='<select name="'.$this->prefixId.'[DATA][tocElements_kind]['.$sDat["uid"].']">'.implode("",$opt).'</select>';
				$flag='<input type="hidden" name="'.$this->prefixId.'[DATA][tocElements_types]['.$sDat["uid"].']" value=0>';

				$l3msg='Level 3 is displayed together with level 2 on one single page.';
				$level3='<input type="hidden" name="'.$this->prefixId.'[DATA][tocElements_l3]['.$sDat["uid"].']" value="0"><input type="checkbox" name="'.$this->prefixId.'[DATA][tocElements_l3]['.$sDat["uid"].']" value="1"'.($sDat["show_level3"] ? 'CHECKED':'').' title="'.$l3msg.'">';
				$level3_text=$sDat["show_level3"] ? '<img src="t3lib/gfx/icon_ok.gif" width="18" height="16" border="0" title="'.$l3msg.'">':'&nbsp';
			} elseif ($sDat["level"]==3)	{
				$opt=array();
				$opt[]='<option value=""></option>';
				reset($types);
				while(list($key,$text)=each($types))	{
					$issel = !strcmp($sDat["typeofcontent"],$key);
					if ($issel)	$flag_text=$text;
					$opt[]='<option value="'.$key.'"'.($issel?' SELECTED':'').'>'.htmlspecialchars($text).'</option>';
				}
				$flag='<select name="'.$this->prefixId.'[DATA][tocElements_types]['.$sDat["uid"].']">'.implode("",$opt).'</select>';
			}

			$annoCnt="";
			if (t3lib_extMgm::isLoaded("t3annotation"))	{
				$annotationObj->initRel("EXTREP",$this->internal["currentRow"]["extension_key"].":doc:".$sDat["uid"],0);
				$annoCnt=$annotationObj->countAnnotations();
				$annoCnt = $annoCnt ? $annoCnt : "";
			}

			$lines[]='<tr>
				<td>'.htmlspecialchars(str_pad("", 4*($sDat["level"]-1), "--").$sDat["stripped_value"]).'</td>
				<td>'.($edit ? $sel : $sel_text).'&nbsp;</td>
				<td>'.($edit ? $level3 : $level3_text).'</td>
				<td>'.($edit ? $flag : $flag_text).'&nbsp;</td>
				<td>'.($edit ? $aud : $aud_text).'</td>
				<td>'.$annoCnt.'</td>
			</tr>';
		}
		$out='<table border=1 cellpadding=0 cellspacing=0>
			<tr>
				<td'.$this->pi_classParam("HCell").'>TOC element:</td>
				<td'.$this->pi_classParam("HCell").'>Category:</td>
				<td'.$this->pi_classParam("HCell").'>L3:</td>
				<td'.$this->pi_classParam("HCell").'>Content type:</td>
				<td'.$this->pi_classParam("HCell").'>Target Aud:</td>
				<td'.$this->pi_classParam("HCell").'>Ann.</td>
			</tr>
		'.implode("",$lines).'</table>';
		return $out;
	}

	/**
	 * This makes the interface for editing the TOC of a open Office document attached to this extension.
	 *
	 * @param	[type]		$extRepEntry: ...
	 * @param	[type]		$sxwfile: ...
	 * @return	[type]		...
	 */
	function generateDocumentForm($extRepEntry,$sxwfile)	{
		$masterEl = $this->getTocPHElement($extRepEntry["extension_uid"],$sxwfile);
#debug($masterEl);
		if ($extRepEntry["is_manual_included"] != $masterEl["is_included_hash"])	{
			$e = $this->getOOdoc($extRepEntry,$sxwfile);
			if (!$e)	{
				$this->toc_current = $this->makeTOCfromLoadedOOdoc();
				$this->saved_toc = $this->getTOCfromSavedOOdoc($extRepEntry["extension_uid"],$sxwfile);
				if (count($this->toc_current) && !count($this->saved_toc))	{
					reset($this->toc_current);
					while(list($k,$TOCarray)=each($this->toc_current))	{
						$this->updateInsertTOCEntry($extRepEntry["extension_uid"],$sxwfile,$TOCarray);
					}
					$this->updateInsertTOCph($extRepEntry["extension_uid"],$sxwfile,$extRepEntry["is_manual_included"]);
					return 'Writing new TOC right away... Please reload and set meta-data.';
				} else {
					$this->pairs=array();
					$this->oodoc_inKey="oodoc_".$sxwfile;

		#debug($this->saved_toc);

					$hToc_curDoc = $this->makeHierarchyTOC($this->toc_current,1,1);
					$hToc_curDoc_full = $this->makeHierarchyTOC($this->toc_current,1,0);
					$saved_toc_no_zeros = $this->getTOCfromSavedOOdoc($extRepEntry["extension_uid"],$sxwfile,1);
					$hToc_saved = $this->makeHierarchyTOC($saved_toc_no_zeros,1,1);
					$hToc_saved_full = $this->makeHierarchyTOC($saved_toc_no_zeros,1,0);

					$cmp_content="";
					$this->cmp_hiddenFields=!$this->piVars["DATA"][$this->oodoc_inKey.'_showAllSelectorBoxes'];
					if (serialize($hToc_curDoc)!=serialize($hToc_saved))	{	// THERE IS A DIFFERENCE:

							// Making comparison/matching table:
						$cmp_content = $this->cmpArrays($hToc_curDoc,$hToc_saved,$hToc_curDoc_full,$hToc_saved_full);
						$cmp_content = '
						<span style="color:red;"><strong>Please syncronize Table of Contents</strong></span><BR>
							If you move, add or delete header-sections in the OO-document the Table of Contents (TOC) in the Open Office Manual will be out of sync with the TOC in the database.
							The reason for keeping a redundant TOC in the database is that we can apply meta-data as well as have a unique reference to each section in the Open Office document. <BR>
							Since the TOC of the current OO document is out of sync with the database you should use this interface below to sync it.<BR>
						<table border=1 cellspacing=0 cellpadding=0>
							<tr>
								<td'.$this->pi_classParam("HCell").'>&nbsp;</td>
								<td'.$this->pi_classParam("HCell").'>TOC in SXW:</td>
								<td'.$this->pi_classParam("HCell").'>Linking to element in database:</td>
							</tr>
						'.$cmp_content.'</table>';

							// Making report over user of elements:
						$local_saved_toc = $this->saved_toc;
						$local_cur_toc = $this->toc_current;
						$msg=array();
						reset($this->pairs);
						while(list($toc_k,$db_uid)=each($this->pairs))	{
							unset($local_cur_toc[$toc_k]);
							if (is_array($local_saved_toc[$db_uid]))	{
								$local_saved_toc[$db_uid]="CLEAR";
							} elseif ($local_saved_toc[$db_uid]=="CLEAR") {
								$msg[]='ERROR: You have used the element "'.$this->saved_toc[$db_uid]["stripped_value"].'" ('.$this->saved_toc[$db_uid]["uid"].') twice, which you cannot.';
							} elseif ($db_uid && !isset($local_saved_toc[$db_uid]))	{
								$msg[]='ERROR: For some reason you have used an element that does not exist! Eh...';
							}
						}
						reset($local_cur_toc);
						while(list($toc_k,$dat)=each($local_cur_toc))	{
							$msg[]='ERROR: TOC element "'.$dat["stripped_value"].'" not registered yet';
						}
						$is_error=count($msg);

						reset($local_saved_toc);
						while(list($uid,$dbrec)=each($local_saved_toc))	{
							if (is_array($dbrec))	{
								$msg[]='NOT USED: Element "'.$this->saved_toc[$uid]["stripped_value"].'" ('.$this->saved_toc[$uid]["uid"].')';
							}
						}

						if (count($msg))	$cmp_content.='<hr>'.implode('<br>',$msg);
						if (!$is_error)	{
							$fN = $this->prefixId.'[DATA]['.$this->oodoc_inKey.'_save]';
							$cmp_content.='<BR><span style="background-color:red;"><input type="checkbox" name="'.$fN.'" value="1" onClick="alert(unescape(\''.rawurlencode("This will syncronize the database TOC with the current TOC. All NOT USED db-TOC entries will be disabled.").'\'));"><strong>UPDATE TOC</strong></span>';
						} else {
							$cmp_content.='<BR><strong>There was an error you have to correct before you can sync the TOC.</strong>';
						}
						$cmp_content.='<BR><input type="checkbox" name="'.$this->prefixId.'[DATA]['.$this->oodoc_inKey.'_showAllSelectorBoxes]" value="1">Show all selectorboxes on next update.';
						return $cmp_content;
					} else {
							// Now that the new content hash including oodoc_reference and temp-file name is written to the TOC in database, we can safely remove the old 1) temp-file and 2) oodoc in database.
						$this->cleanUpOldOOdoc($masterEl["cur_oodoc_ref"], $masterEl["cur_tmp_file"]);

							// Update with new TOC information:
#debug($this->saved_toc);
#debug($this->toc_current);
						reset($this->saved_toc);
						while(list($st_uid,$st_rec)=each($this->saved_toc))	{
							$this->updateInsertTOCEntry($extRepEntry["extension_uid"],$sxwfile,$this->toc_current[$st_rec["arr_key"]],$st_uid);
						}
						$this->updateInsertTOCph($extRepEntry["extension_uid"],$sxwfile,$extRepEntry["is_manual_included"]);

						$this->buildCacheOfCurrentParts($extRepEntry["extension_uid"],$sxwfile);

// (robert 29.10.04) DISABLED for performance reasons until we find a better solution:
//						$this->clearPageCacheForExtensionDoc($extRepEntry["extension_uid"]);
						$this->clearPageCacheForExtensionDoc(0);
						return 'OOdoc TOC matches database TOC. Writing new content hash to TOC table.<br />
							<strong style="color:red">For performance reasons, it might take some hours until your changes are visible on typo3.org! However, we are working on an improved caching management.</strong>
						';
					}
				}
			} else return 'ERROR: '.$e;
		} else return 0;
	}

	/**
	 * Getting the TOC from the open office file
	 *
	 * @return	[type]		...
	 */
	function makeTOCfromLoadedOOdoc()	{
		$this->ooDocObj->prepareOfficeBodyArray();
			// $this->ooDocObj->officeBody is an array of all "root-level" paragraphs
#debug($this->ooDocObj->officeBody);
		$toc=array();
		$c=0;
		reset($this->ooDocObj->officeBody);
		while(list($index,$tagArray)=each($this->ooDocObj->officeBody))	{
			if ($tagArray["tag"]=="TEXT:H" && t3lib_div::inList("1,2,3",$tagArray["attributes"]["TEXT:LEVEL"]))	{
				$vv = trim(strip_tags(str_replace("&nbsp;","",implode("",$this->ooDocObj->renderOOBody(array($tagArray))))));
				if (strcmp($vv,""))	{
					$c++;
					$toc[$c]=array();
					$toc[$c]["stripped_value"]=$vv;
					$toc[$c]["index"]=$index;
					$toc[$c]["level"]=$tagArray["attributes"]["TEXT:LEVEL"];
					$toc[$c]["keynum"]=$c;
				}
#debug($tagArray);
			} elseif ($c && !isset($toc[$c]["stripped_next"]))	{
				$toc[$c]["stripped_next"]=t3lib_div::fixed_lgd(trim(strip_tags(str_replace("&nbsp;","",implode("",$this->ooDocObj->renderOOBody(array($tagArray)))))),100);
				if (!strcmp($toc[$c]["stripped_next"],""))	unset($toc[$c]["stripped_next"]);
			}
		}
#debug($toc);
		return $toc;
	}

	/**
	 * Update TOC field
	 *
	 * @param	[type]		$extUid: ...
	 * @param	[type]		$sxwfile: ...
	 * @param	[type]		$st_uid: ...
	 * @param	[type]		$type: ...
	 * @param	[type]		$field: ...
	 * @return	[type]		...
	 */
	function updateTOCField($extUid,$sxwfile,$st_uid,$type,$field)	{
		$document_unique_ref = hexdec(substr(md5($extUid."|".$sxwfile),0,7));

		$query="UPDATE tx_extrepmgm_oodoctocel SET
				".$field."='".$type."'
				WHERE uid=".intval($st_uid)."
					AND document_unique_ref=".intval($document_unique_ref);
		$res = mysql(TYPO3_db,$query);
		echo mysql_error();
	}

	/**
	 * Updating or inserting a TOC placeholder
	 *
	 * @param	[type]		$extUid: ...
	 * @param	[type]		$sxwfile: ...
	 * @param	[type]		$isIncHash: ...
	 * @return	[type]		...
	 */
	function updateInsertTOCph($extUid,$sxwfile,$isIncHash)	{
		$updateArr=array();
		$updateArr["document_unique_ref"]=hexdec(substr(md5($extUid."|".$sxwfile),0,7));
		$updateArr["is_included_hash"]=$isIncHash;
		$updateArr["extension_uid"]=$extUid;
		$updateArr["sxwfile"]=$sxwfile;
		$updateArr["cur_tmp_file"]=substr($this->ooDocObj->file,strlen(PATH_site));
		$updateArr["cur_oodoc_ref"]=$this->ooDocObj->ext_ID;
		$updateArr["doc_size"]=0;


		$meta_fileInfo = $this->ooDocObj->getFileFromXML("meta.xml");
		$XML_content = $meta_fileInfo["content"];
		if ($XML_content)	{
			$p = xml_parser_create();
			xml_parse_into_struct($p,$XML_content,$vals,$index);
			xml_parser_free($p);

			$metaSection = array_slice($vals,$index["OFFICE:META"][0]+1,$index["OFFICE:META"][1]-$index["OFFICE:META"][0]-1);
			$metaRaw =$this->ooDocObj->indentSubTagsRec($metaSection,999);
			if (is_array($metaRaw))	{
				$udc=0;
				reset($metaRaw);
				while(list(,$tagC)=each($metaRaw))	{
					switch($tagC["tag"])	{
						case "DC:TITLE":
							$updateArr["doc_title"]=utf8_decode($tagC["value"]);
						break;
						case "DC:CREATOR":
#							$updateArr["doc_author"]=utf8_decode($tagC["value"]);
						break;
						case "DC:DATE":
							$dateTimeParts=explode("T",$tagC["value"]);
							$dateP=explode("-",$dateTimeParts[0]);
							$timeP=explode(":",$dateTimeParts[1]);
							$updateArr["doc_mtime"]=mktime($timeP[0],$timeP[1],$timeP[2],$dateP[1],$dateP[2],$dateP[0]);
						break;
						case "META:USER-DEFINED":
							if ($udc==0)	$updateArr["doc_author_email"]=utf8_decode($tagC["value"]);
							if ($udc==1)	$updateArr["doc_author"]=utf8_decode($tagC["value"]);
							/*
							if (strtolower($tagC["attributes"]["META:NAME"])=="email")	{
								$updateArr["doc_author_email"]=utf8_decode($tagC["value"]);
							}
							if (strtolower(ereg_replace("[^a-zA-Z]*","",$tagC["attributes"]["META:NAME"]))=="author")	{
								$updateArr["doc_author"]=utf8_decode($tagC["value"]);
							}
							*/
							$udc++;
						break;
						case "META:DOCUMENT-STATISTIC":
							$updateArr["doc_images"]=$tagC["attributes"]["META:IMAGE-COUNT"];
							$updateArr["doc_tables"]=$tagC["attributes"]["META:TABLE-COUNT"];
							$updateArr["doc_objects"]=$tagC["attributes"]["META:OBJECT-COUNT"];
							$updateArr["doc_pages"]=$tagC["attributes"]["META:PAGE-COUNT"];
							$updateArr["doc_words"]=$tagC["attributes"]["META:WORD-COUNT"];
							$updateArr["doc_chars"]=$tagC["attributes"]["META:CHARACTER-COUNT"];
#debug($tagC);
						break;
					}
				}
			}
			$updateArr["doc_size"]=filesize($this->ooDocObj->file);
		}
		$updateArr["tstamp"]=time();

#debug($updateArr);

			// TOC cache:
		$updateArr["toc_cache"]="";
		$this->saved_toc = $this->getTOCfromSavedOOdoc($extUid,$sxwfile,1);
		if (count($this->saved_toc))	{
				// Prepare the TOC of this document:
			$toc_hierarch = $this->makeHierarchyTOC($this->saved_toc,1,0);
			$this->linearToc=array();
			$this->linearTocOrder=array();
			$this->prepareTOCdata($toc_hierarch);

			$updateArr["toc_cache"]=serialize(array(
				"linearToc" => $this->linearToc,
				"linearTocOrder" => $this->linearTocOrder
			));
		}

			// Test if it is here already:
		$query="SELECT document_unique_ref FROM tx_extrepmgm_oodoctoc WHERE document_unique_ref=".intval($updateArr["document_unique_ref"]);
		$res = mysql(TYPO3_db,$query);
		if (mysql_num_rows($res))	{
			// Update:
			$queryA=array();
			reset($updateArr);
			while(list($f,$v)=each($updateArr))	{
				$queryA[]=$f."='".addslashes($v)."'";
			}
			$query="UPDATE tx_extrepmgm_oodoctoc SET ".implode($queryA,",")." WHERE document_unique_ref=".intval($updateArr["document_unique_ref"]);
		} else {
				// Insert:
			$query = $GLOBALS['TYPO3_DB']->INSERTquery('tx_extrepmgm_oodoctoc', $updateArr);
		}
		$res = mysql(TYPO3_db,$query);
		echo mysql_error();
#debug($updateArr);
#debug(date("d-m-Y H:i:s",$updateArr["doc_mtime"]));
//		cat tinyint(3) DEFAULT '0' NOT NULL,
//		lang int(11) DEFAULT '0' NOT NULL,
	}

	/**
	 * Update the cached Table Of Contents
	 *
	 * @param	[type]		$extUid: ...
	 * @param	[type]		$sxwfile: ...
	 * @return	[type]		...
	 */
	function updateCachedToc($extUid,$sxwfile)	{
		$doc_uid = hexdec(substr(md5($extUid."|".$sxwfile),0,7));

			// TOC cache:
		$updateArr["toc_cache"]="";
		$this->saved_toc = $this->getTOCfromSavedOOdoc($extUid,$sxwfile,1);
		if (count($this->saved_toc))	{
				// Prepare the TOC of this document:
			$toc_hierarch = $this->makeHierarchyTOC($this->saved_toc,1,0);
			$this->linearToc=array();
			$this->linearTocOrder=array();
			$this->prepareTOCdata($toc_hierarch);

			$updateArr["toc_cache"]=serialize(array(
				"linearToc" => $this->linearToc,
				"linearTocOrder" => $this->linearTocOrder
			));
		}

		$query="UPDATE tx_extrepmgm_oodoctoc SET toc_cache='".addslashes($updateArr["toc_cache"])."'
					WHERE document_unique_ref=".intval($doc_uid);
#debug($query);
		$res = mysql(TYPO3_db,$query);
		echo mysql_error();
#debug($updateArr);
	}

	/**
	 * Update trivial fields in the PlaceHolder TOC entry
	 *
	 * @param	[type]		$extUid: ...
	 * @param	[type]		$sxwfile: ...
	 * @param	[type]		$dat: ...
	 * @return	[type]		...
	 */
	function updateTocPH($extUid,$sxwfile,$dat)	{
		$doc_uid = hexdec(substr(md5($extUid."|".$sxwfile),0,7));

		$query="UPDATE tx_extrepmgm_oodoctoc SET
				cat=".intval($dat["cat"]).",
				lang=".intval($dat["lang"])."
				WHERE document_unique_ref=".intval($doc_uid);
#debug($query);
		$res = mysql(TYPO3_db,$query);
		echo mysql_error();
	}

	/**
	 * Select TOC PH element
	 *
	 * @param	[type]		$extUid: ...
	 * @param	[type]		$sxwfile: ...
	 * @return	[type]		...
	 */
	function getTocPHElement($extUid,$sxwfile)	{
		$document_unique_ref = hexdec(substr(md5($extUid."|".$sxwfile),0,7));
		$query="SELECT * FROM tx_extrepmgm_oodoctoc WHERE document_unique_ref=".intval($document_unique_ref);
		$res = mysql(TYPO3_db,$query);
		return mysql_fetch_assoc($res);
	}

	/**
	 * Delete TOC PH element
	 *
	 * @param	[type]		$extUid: ...
	 * @param	[type]		$sxwfile: ...
	 * @return	[type]		...
	 */
	function deleteTocPHElement($extUid,$sxwfile)	{
		$document_unique_ref = hexdec(substr(md5($extUid."|".$sxwfile),0,7));
		$query="DELETE FROM tx_extrepmgm_oodoctoc WHERE document_unique_ref=".intval($document_unique_ref);
		$res = mysql(TYPO3_db,$query);
	}

	/**
	 * Saving/Updating TOC elements
	 *
	 * @param	[type]		$extUid: ...
	 * @param	[type]		$sxwfile: ...
	 * @param	[type]		$TOCarray: ...
	 * @param	[type]		$st_uid: ...
	 * @return	[type]		...
	 */
	function updateInsertTOCEntry($extUid,$sxwfile,$TOCarray,$st_uid=0)	{
		$updateArr=array();
		$updateArr["document_unique_ref"]=hexdec(substr(md5($extUid."|".$sxwfile),0,7));
#		$updateArr["is_included_hash"]=$isIncHash;
		$updateArr["extension_uid"]=$extUid;
#		$updateArr["sxwfile"]=$sxwfile;
#		$updateArr["cur_tmp_file"]=substr($this->ooDocObj->file,strlen(PATH_site));
#		$updateArr["cur_oodoc_ref"]=$this->ooDocObj->ext_ID;

#debug($TOCarray);

		// TOC array:
		if (is_array($TOCarray))	{
			$updateArr["arr_key"]=$TOCarray["keynum"];
			$updateArr["stripped_value"]=$TOCarray["stripped_value"];
			$updateArr["xmlarr_index"]=$TOCarray["index"];
			$updateArr["hlevel"]=$TOCarray["level"];
			$updateArr["stripped_next"]="".$TOCarray["stripped_next"];
		} else {	// disabling:
			$updateArr["hlevel"]=0;
			$updateArr["xmlarr_index"]=0;
			$updateArr["arr_key"]=0;
		}
		$updateArr["tstamp"]=time();

		if ($st_uid>0)	{	// UPDATE:
			$queryA=array();
			reset($updateArr);
			while(list($f,$v)=each($updateArr))	{
				$queryA[]=$f."='".addslashes($v)."'";
			}
			$query="UPDATE tx_extrepmgm_oodoctocel SET ".implode($queryA,",")." WHERE uid=".intval($st_uid);
		} else {	 // Insert:
			$query = $GLOBALS['TYPO3_DB']->INSERTquery('tx_extrepmgm_oodoctocel',$updateArr);
		}
#debug(array($query));
		$res = mysql(TYPO3_db,$query);
		echo mysql_error();
	}

	/**
	 * Cleaning up old OOdoc file storage entry and temp-file.
	 *
	 * @param	[type]		$oodoc_fileStorage_id: ...
	 * @param	[type]		$tempFile: ...
	 * @return	[type]		...
	 */
	function cleanUpOldOOdoc($oodoc_fileStorage_id, $tempFile)	{
#		debug(array($oodoc_fileStorage_id, $tempFile));

			// Remove storage item from storage table.
		$query = "DELETE FROM tx_oodocs_filestorage WHERE rel_id=".intval($oodoc_fileStorage_id);
		$res = mysql(TYPO3_db,$query);
		echo mysql_error();
#debug(array($query));

			// Remove temporary file:
		$path = PATH_site.$tempFile;
		if (!strstr($path,"..") && t3lib_div::isFirstPartOfStr($path,PATH_site."typo3temp/manual-"))	{
			if (@is_file($path))	{
				unlink($path);
			} else debug("No file to delete: ".$path);
		} else debug("Invalid path to delete: ".$path);
	}

	/**
	 * Render all current parts of a oodoc - so cached...
	 *
	 * @param	[type]		$extUid: ...
	 * @param	[type]		$sxwfile: ...
	 * @return	[type]		...
	 */
	function buildCacheOfCurrentParts($extUid,$sxwfile)	{
		// Clear current cache:
		$this->clearOOsliceCache($extUid);
#debug("CLEAR CACHE!");
		// Traverse whole document to render it.
		$parts = $this->getTOCfromSavedOOdoc($extUid,$sxwfile,1);
		reset($parts);
		while(list($uid)=each($parts))	{
#debug(array($extUid,$uid));
			$this->renderOOdocSlice($extUid,$uid,0,1);
#debug($e);
		}

			// Make information for the tsconfig-online help.
		$this->findOOdocTables($extUid);
	}

	/**
	 * Get the current TOC from the database
	 *
	 * @param	[type]		$extUid: ...
	 * @param	[type]		$sxwfile: ...
	 * @param	[type]		$noZeros: ...
	 * @return	[type]		...
	 */
	function getTOCfromSavedOOdoc($extUid,$sxwfile,$noZeros=0)	{
		$document_unique_ref = hexdec(substr(md5($extUid."|".$sxwfile),0,7));
		$query="SELECT * FROM tx_extrepmgm_oodoctocel WHERE
			document_unique_ref=".$document_unique_ref.
			($noZeros?" AND arr_key>0":"").
			" ORDER BY arr_key";
		$res = mysql(TYPO3_db,$query);

		$toc=array();
		$lastRecUid=0;
		while($row=mysql_fetch_assoc($res))	{
			$row["level"]=$row["hlevel"];
			$toc[$row["uid"]]=$row;

			if (isset($toc[$lastRecUid]))	{
				$toc[$lastRecUid]["_next_xmlarr_index"]=$row["xmlarr_index"];
			}
			$lastRecUid=$row["uid"];
		}
		return $toc;
	}

	/**
	 * Make Hierarchy TOC
	 *
	 * @param	[type]		$toc: ...
	 * @param	[type]		$level: ...
	 * @param	[type]		$cmp: ...
	 * @return	[type]		...
	 */
	function makeHierarchyTOC($toc,$level=0,$cmp=0)	{
		$hToc=array();
		$dat=array();
		$lC=0;
		reset($toc);
		while(list(,$cT)=each($toc))	{
			if ($cmp)	$cT=array("stripped_value"=>$cT["stripped_value"],"level"=>$cT["level"]);
			if ($cT["level"]>$level)	{
				$hToc[$lC]["sub"][]=$cT;
			} else {
				$lC++;
				$hToc[$lC]["dat"]=$cT;
				$dat[]=$cT;
			}
		}

			// Subs...
		reset($hToc);
		while(list($k,$v)=each($hToc)){
			if (is_array($hToc[$k]["sub"]))	{
				$hToc[$k]["sub"]=$this->makeHierarchyTOC($hToc[$k]["sub"],$level+1,$cmp);
				$hToc[$k]["sub_md5"]=md5(serialize($hToc[$k]["sub"]));
			}
		}

		return array("hToc" => $hToc,"dat_md5"=>md5(serialize($dat)));
	}

	/**
	 * Compare Arrays
	 *
	 * @param	[type]		$hToc_curDoc: ...
	 * @param	[type]		$hToc_saved: ...
	 * @param	[type]		$hToc_curDoc_full: ...
	 * @param	[type]		$hToc_saved_full: ...
	 * @param	[type]		$level: ...
	 * @return	[type]		...
	 */
	function cmpArrays($hToc_curDoc,$hToc_saved,$hToc_curDoc_full,$hToc_saved_full,$level=0)	{
#debug($hToc_curDoc_full);

		$cmp_content="";


			// OK, now searching for the right connection:
		$pairs=array();




			// First, look for submissions
		reset($hToc_curDoc["hToc"]);
		while(list($k,$dat)=each($hToc_curDoc["hToc"]))	{
			$realK=$hToc_curDoc_full["hToc"][$k]["dat"]["keynum"];
			if (isset($this->piVars["DATA"][$this->oodoc_inKey][$realK]))	{	//  && $this->piVars["DATA"][$this->oodoc_inKey][$realK]>0
				$pairs[$k]=$this->piVars["DATA"][$this->oodoc_inKey][$realK];
			}
		}




			// First, looking for an obvious match, same position, same title.
		reset($hToc_curDoc["hToc"]);
		while(list($k,$dat)=each($hToc_curDoc["hToc"]))	{
			if ($hToc_curDoc_full["hToc"][$k]["dat"]["stripped_value"] && !strcmp($hToc_saved_full["hToc"][$k]["dat"]["stripped_value"],$hToc_curDoc_full["hToc"][$k]["dat"]["stripped_value"]))	{
				$pairs[$k]=$hToc_saved_full["hToc"][$k]["dat"]["uid"];
			}
		}

#debug($pairs);

			// Then browsing through titles, if they have been switched around
		reset($hToc_curDoc["hToc"]);
		while(list($k,$dat)=each($hToc_curDoc["hToc"]))	{
			if (!isset($pairs[$k]))	{	// Cannot be registered already.
				if (is_array($hToc_saved_full["hToc"]))	{
						// Going through the elements:
					reset($hToc_saved_full["hToc"]);
					while(list(,$sDat)=each($hToc_saved_full["hToc"]))	{
							// The element cannot be registered already.
						if (!in_array($sDat["dat"]["uid"], $pairs))	{
							if ($hToc_curDoc_full["hToc"][$k]["dat"]["stripped_value"] && !strcmp($sDat["dat"]["stripped_value"],$hToc_curDoc_full["hToc"][$k]["dat"]["stripped_value"]))	{
								$pairs[$k]=$sDat["dat"]["uid"];
								break;
							}
						}
					}
				}
			}
		}

#debug($pairs);

			// Then looking for matching subsection
		reset($hToc_curDoc["hToc"]);
		while(list($k,$dat)=each($hToc_curDoc["hToc"]))	{
			if (!isset($pairs[$k]))	{	// Cannot be registered already.
				if (is_array($hToc_saved_full["hToc"]))	{
						// Going through the elements:
					reset($hToc_saved_full["hToc"]);
					while(list(,$sDat)=each($hToc_saved_full["hToc"]))	{
							// The element cannot be registered already.
						if (!in_array($sDat["dat"]["uid"], $pairs))	{
							if (!strcmp($dat["sub_md5"], $hToc_saved["hToc"][$k]["sub_md5"]))	{
								$pairs[$k]=$sDat["dat"]["uid"];
								break;
							}
						}
					}
				}
			}
		}


#$pairs=array();
#debug($pairs);

			// Finally render form:
		reset($hToc_curDoc["hToc"]);
		while(list($k,$dat)=each($hToc_curDoc["hToc"]))	{
			if (isset($dat["dat"]))	{
				$sel="";
					// Fieldname:
				$fN = $this->prefixId.'[DATA]['.$this->oodoc_inKey.']['.$hToc_curDoc_full["hToc"][$k]["dat"]["keynum"]."]";

					// To show selector boxes either cmp_hiddenFields should be disabled OR there should not be a good-guess value available.
#$this->cmp_hiddenFields=0;
				if (!$this->cmp_hiddenFields || !isset($pairs[$k]))	{
					$opt=array();
					$opt[]='<option value="0">[NEW]</option>';
					if (is_array($hToc_saved_full["hToc"]))	{
						reset($hToc_saved_full["hToc"]);
						while(list(,$sDat)=each($hToc_saved_full["hToc"]))	{
							if (!in_array($sDat["dat"]["uid"],$pairs) || $pairs[$k]==$sDat["dat"]["uid"])	{	// The uid should not be in the pairs array OR it should be exactly the value.
								$opt[]='<option value="'.$sDat["dat"]["uid"].'"'.($pairs[$k]==$sDat["dat"]["uid"]?' SELECTED':'').'>'.htmlspecialchars($sDat["dat"]["stripped_value"].' (#'.$sDat["dat"]["uid"].')').'</option>';
							}
						}
					}
					if (is_array($this->saved_toc))	{
						reset($this->saved_toc);
						while(list(,$sDat)=each($this->saved_toc))	{
							$opt[]='<option value="'.$sDat["uid"].'"'.($sDat["uid"]==$pairs[$k]?" SELECTED":"").'>'.htmlspecialchars(str_pad("", 2*($sDat["level"]-1), "--").$sDat["stripped_value"]).'</option>';
						}
					}


					$sel= '<select name="'.$fN.'">'.implode("",$opt).'</select>';
				} else {
					$sel= '<input type="hidden" value="'.$pairs[$k].'" name="'.$fN.'">';
					if ($pairs[$k])	{
						$sel.= str_pad("", 2*($this->saved_toc[$pairs[$k]]["level"]-1), "--").$this->saved_toc[$pairs[$k]]["stripped_value"].(' ('.$this->saved_toc[$pairs[$k]]["uid"].')');
					} else $sel.= "[NEW]";
					$this->pairs[$hToc_curDoc_full["hToc"][$k]["dat"]["keynum"]]=$pairs[$k];
				}
				$out.= '<tr>
						<td>'.$hToc_curDoc_full["hToc"][$k]["dat"]["keynum"].'</td>
						<td nowrap'.(isset($pairs[$k])?'':' style="background-color:#ccccff"').'>'.str_pad("", 2*$level, "--").$dat["dat"]["stripped_value"].'</td>
						<td>'.$sel.'</td>
					</tr>';
				$noDat=0;
			} else $noDat=1;

			if (($noDat || isset($pairs[$k])) && is_array($dat["sub"]))	{
				$out.= $this->cmpArrays(
					$dat["sub"],
					$hToc_saved["hToc"][$k]["sub"],
					$hToc_curDoc_full["hToc"][$k]["sub"],
					$hToc_saved_full["hToc"][$k]["sub"],
					$level+1
				);
			}
		}

		return $out;
	}

	/**
	 * Render oodoc slice
	 *
	 * @param	[type]		$tocEl: ...
	 * @return	[type]		...
	 */
	function _temp_get_oodoc_element($tocEl)	{
		$query='SELECT tx_extrepmgm_oodoctocel.*,tx_extrepmgm_oodoctoc.cur_tmp_file
				FROM tx_extrepmgm_oodoctocel,tx_extrepmgm_oodoctoc
				WHERE tx_extrepmgm_oodoctocel.document_unique_ref = tx_extrepmgm_oodoctoc.document_unique_ref
				AND tx_extrepmgm_oodoctocel.uid='.intval($tocEl);
		$res = mysql(TYPO3_db,$query);
		echo mysql_error();
		if ($tocElRec=mysql_fetch_assoc($res))	{
				// Make OOdoc instance
			$idKey="PI:extrep.".$extUid.".".$tocElRec["cur_tmp_file"];
			$thisOODocObj = t3lib_div::makeInstance("tx_oodocs");
			$thisOODocObj->compressedStorage=1;

			$tempFile=PATH_site.$tocElRec["cur_tmp_file"];
debug($tempFile);
			if (@is_file($tempFile))	{
				$e = $thisOODocObj->init($tempFile,$idKey);
				if (!$e)	{
					$thisOODocObj->prepareOfficeBodyArray();

						// Slicing the content to display the part we want:
						// Finding arr_key of next part:
					$query='SELECT xmlarr_index FROM tx_extrepmgm_oodoctocel WHERE
						extension_uid='.intval($tocElRec["extension_uid"]).'
						AND arr_key>'.intval($tocElRec["arr_key"]).
						' ORDER BY arr_key LIMIT 1';
					$res = mysql(TYPO3_db,$query);
					if ($tocElRec_next = mysql_fetch_assoc($res))	{
debug(array($tocElRec_next["xmlarr_index"],$tocElRec["xmlarr_index"]));
						$oBody = array_slice(
								$thisOODocObj->officeBody,
								$tocElRec["xmlarr_index"],
								$tocElRec_next["xmlarr_index"]-$tocElRec["xmlarr_index"]);
					} else {
						$oBody = array_slice(
								$thisOODocObj->officeBody,
								$tocElRec["xmlarr_index"]+$offsetFirst);
					}
					return array($tocElRec,$oBody,$thisOODocObj);
				} else return "ERROR: The OOdoc parser returned an error: ".$e; // $e
			} else return "ERROR: No temporary sxw file found in '".$tempFile."'"; // is_file
		} else return "ERROR: No toc-record by that UID for this extension uid"; // tocElRec
	}








	/*********************************************************
	 *
	 * 	Translator listing
	 *
	 **********************************************************/

	/**
	 * Output the list of translators
	 *
	 * @return	[type]		...
	 */
	function listTranslations()	{

		$emails_chiefs=array();
		$emails_assist=array();

			// EDITING/MANAGEMENT of the languages
		if ($this->piVars["langUid"])	{
			$langRec = $this->pi_getRecord("tx_extrepmgm_langadmin",$this->piVars["langUid"]);
			if ($GLOBALS["TSFE"]->loginUser && is_array($langRec) && !strcmp($GLOBALS["TSFE"]->fe_user->user["uid"],$langRec["auth_translator"]))	{

					// If a change is sent:
				if ($this->piVars["DATA"]["submit"])	{
						// Set members:
					$query="DELETE FROM tx_extrepmgm_langadmin_sub_translators_mm WHERE uid_local=".intval($langRec["uid"]);
					$res = mysql(TYPO3_db,$query);
#	debug($query,1);
					if (is_array($this->piVars["DATA"]["assist"]))	{
						reset($this->piVars["DATA"]["assist"]);
						while(list($k,$fe_user_uid)=each($this->piVars["DATA"]["assist"]))	{
							$query="INSERT INTO tx_extrepmgm_langadmin_sub_translators_mm
								(uid_local,uid_foreign,tablenames,sorting)
								VALUES
								(".intval($langRec["uid"]).",".intval($fe_user_uid).",'fe_users',".intval($k).")";
							$res = mysql(TYPO3_db,$query);
#	debug($query,1);
						}
					}
				}
			}


			// *************************
			// DISPLAY assistant form:
			// **************************

				// Finding assisting translators.
			$assistTranslators=array();
			$query = "SELECT * FROM tx_extrepmgm_langadmin_sub_translators_mm,fe_users WHERE
						fe_users.pid=".intval($this->dbPageId).
						" AND tx_extrepmgm_langadmin_sub_translators_mm.uid_foreign = fe_users.uid
						 AND tx_extrepmgm_langadmin_sub_translators_mm.uid_local = ".intval($langRec["uid"]).
						$this->cObj->enableFields("fe_users").
						" ORDER BY tx_extrepmgm_langadmin_sub_translators_mm.sorting";
			$res = mysql(TYPO3_db,$query);
			echo mysql_error();
			while($row=mysql_fetch_assoc($res))	{
				$assistTranslators[$row["uid"]]=$row;
				$assistTranslators[$row["uid"]]["_ISSET"]=1;
			}

				// Looking up potential users for membership:
			if ($this->piVars["DATA"]["lookup"])	{
				$LU_query = $this->cObj->searchWhere($this->piVars["DATA"]["lookup"],"uid,username,name,email,company,city,country","fe_users");
				$notIn = array_keys($assistTranslators);
				$query = "SELECT * FROM fe_users WHERE ".
					" pid=".intval($this->dbPageId).
					(count($notIn) ? " AND uid NOT IN (".implode(",",$notIn).")" : "").
					" AND uid!=".intval($GLOBALS["TSFE"]->fe_user->user["uid"]).
					$LU_query.
					$this->cObj->enableFields("fe_users").
					" ORDER BY name,username".
					" LIMIT 30";

				$res = mysql(TYPO3_db,$query);
				echo mysql_error();
				while($row=mysql_fetch_assoc($res))	{
					$assistTranslators[$row["uid"]]=$row;
				}
			}

#debug($memberArray);
			$fN = $this->prefixId."[DATA]";
			$formLines=array();
			reset($assistTranslators);
			while(list($mUid,$mDat)=each($assistTranslators))	{
				$formLines[]='<input type="checkbox" name="'.$fN.'[assist][]" value="'.$mUid.'"'.($mDat["_ISSET"]?" CHECKED":"").'>'.$mDat["username"]." (".htmlentities(trim($mDat["name"])).", ".htmlentities(trim($mDat["email"])).")";
			}



			$out.='<form action="'.t3lib_div::getIndpEnv("REQUEST_URI").'" method="post" style="margin: 0px 0px 0px 0px;" name="editForm">
				<h3>Assistant translators:</h3>
				<p>'.(count($formLines)?implode("<BR>",$formLines):"<em>None</em>").'</p>
					<br>
					<p><strong>Lookup users:</strong></p>
					<input type="text" name="'.$fN.'[lookup]"><BR>
					<BR>
					<input type="submit" name="'.$fN.'[submit]" value="Find/Set">
			</form><br>';

				// Back button:
			$out.='<p>'.$this->pi_linkTP_keepPIvars("Back to language list",array("langUid"=>"")).'</p>';

		} else {	// LISTING:
			$query = "SELECT * FROM tx_extrepmgm_langadmin WHERE ".
						"pid=".intval($this->dbPageId).
						$this->cObj->enableFields("tx_extrepmgm_langadmin").
						" ORDER BY title";
			$res = mysql(TYPO3_db,$query);

			$lines=array();
				$lines[]='<tr>
					<td'.$this->pi_classParam("HCell").' nowrap>Language:</td>
					<td'.$this->pi_classParam("HCell").' nowrap>Chief translator:</td>
					<td'.$this->pi_classParam("HCell").' nowrap>Assisting translators:</td>
					<td'.$this->pi_classParam("HCell").' nowrap>Sponsored by:</td>
					<td'.$this->pi_classParam("HCell").' nowrap>Lang. key:</td>
					<td'.$this->pi_classParam("HCell").' nowrap>Charset used:</td>
					<td'.$this->pi_classParam("HCell").' nowrap>Notes:</td>
				</tr>';

				// For each language:
			$c=0;
			while($row=mysql_fetch_assoc($res))	{
					// Finding chief translator
				$chiefTranslator = $this->pi_getRecord("fe_users",$row["auth_translator"]);
				$emails_chiefs[]=$chiefTranslator["email"];
				$chiefTranslator = (is_array($chiefTranslator)?$this->cObj->getTypoLink($chiefTranslator["name"].' ('.$chiefTranslator["username"].')',$chiefTranslator["email"]):'<em>N/A!</em>');

					// Finding assisting translators.
				$assistTranslators=array();
				$query = "SELECT * FROM tx_extrepmgm_langadmin_sub_translators_mm,fe_users WHERE
							fe_users.pid=".intval($this->dbPageId).
							" AND tx_extrepmgm_langadmin_sub_translators_mm.uid_foreign = fe_users.uid
							 AND tx_extrepmgm_langadmin_sub_translators_mm.uid_local = ".intval($row["uid"]).
							$this->cObj->enableFields("fe_users").
							" ORDER BY tx_extrepmgm_langadmin_sub_translators_mm.sorting";
				$res2 = mysql(TYPO3_db,$query);
				echo mysql_error();
				while($row2=mysql_fetch_assoc($res2))	{
					$assistTranslators[]=$this->cObj->getTypoLink($row2["name"].' ('.$row2["username"].')',$row2["email"]);
					$emails_assist[]=$row2["email"];
				}

					// Edit?
				if ($GLOBALS["TSFE"]->loginUser
						&& $chiefTranslator
						&& !strcmp($GLOBALS["TSFE"]->fe_user->user["uid"],$row["auth_translator"]))	{
					$assistTranslators[]='<span'.$this->pi_classParam("admLink").'>'.$this->pi_linkTP_keepPIvars("ADMIN",array("langUid"=>$row["uid"])).'</span>';
				}

					// Making row:
				$pp=($c%2 ? $this->pi_classParam("odd"):'');
				$lines[]='<tr>
					<td'.$pp.'>'.$row["title"].'</td>
					<td'.$pp.' nowrap>'.$chiefTranslator.'</td>
					<td'.$pp.' nowrap>'.implode('<BR>',$assistTranslators).'</td>
					<td'.$pp.' nowrap>'.$this->cObj->getTypoLink($row["sponsor_company"],$row["sponsor_url"]).'</td>
					<td'.$pp.'>'.$row["langkey"].'</td>
					<td'.$pp.' nowrap>'.$row["charset"].'</td>
					<td'.$pp.'>'.$row["credits"].'</td>
				</tr>';
				$c++;
			}
			$out='<table '.$this->conf["listTranslations."]["tableParams"].'>'.implode(chr(10),$lines).'</table>';

				// Show emails in comment:
			if ($GLOBALS["TSFE"]->loginUser)	{
				$out.='


<!-- CHIEF TRANSLATORS:
	'.htmlspecialchars(implode(', ', array_unique($emails_chiefs))).'
-->

<!-- ASSISTING TRANSLATORS:
	'.htmlspecialchars(implode(', ', array_unique($emails_assist))).'
-->

<!-- ALL TRANSLATORS:
	'.htmlspecialchars(implode(', ', array_unique(array_merge($emails_chiefs,$emails_assist)))).'
-->



				';
			}

		}
		return '<DIV'.$this->pi_classParam("tlist").'>'.$out.'</DIV>';
	}





	/*********************************************************
	 *
	 * 	Teams/Projects listing
	 *
	 **********************************************************/

	/**
	 * Output the list of translators
	 *
	 * @return	[type]		...
	 */
	function listTeamsProjects()	{
			// EDITING/MANAGEMENT of the projects
		if ($this->piVars["teamEditUid"])	{
			$teamRec = $this->pi_getRecord("tx_extrepmgm_team",$this->piVars["teamEditUid"]);
			$editOK = $GLOBALS["TSFE"]->loginUser && is_array($teamRec) && !strcmp($GLOBALS["TSFE"]->fe_user->user["uid"],$teamRec["leader"]);
			if ($editOK)	{

					// If a change is sent:
				if ($this->piVars["DATA"]["submit"])	{
						// Set members:
					$query="DELETE FROM tx_extrepmgm_team_members_mm WHERE uid_local=".intval($teamRec["uid"]);
					$res = mysql(TYPO3_db,$query);

					if (is_array($this->piVars["DATA"]["member"]))	{
						reset($this->piVars["DATA"]["member"]);
						while(list($k,$fe_user_uid)=each($this->piVars["DATA"]["member"]))	{
							$query="INSERT INTO tx_extrepmgm_team_members_mm
								(uid_local,uid_foreign,sorting)
								VALUES
								(".intval($teamRec["uid"]).",".intval($fe_user_uid).",".intval($k).")";
							$res = mysql(TYPO3_db,$query);
						}
					}
				}
			}


			// *************************
			// DISPLAY member form:
			// **************************

				// Finding members of team
			$teamMembers=array();

				// Finding members of project
			$query = "SELECT * FROM tx_extrepmgm_team_members_mm,fe_users WHERE
						fe_users.pid=".intval($this->dbPageId).
						" AND tx_extrepmgm_team_members_mm.uid_foreign = fe_users.uid
						 AND tx_extrepmgm_team_members_mm.uid_local = ".intval($teamRec["uid"]).
						$this->cObj->enableFields("fe_users").
						" ORDER BY tx_extrepmgm_team_members_mm.sorting";
			$res = mysql(TYPO3_db,$query);
			echo mysql_error();
			while($row=mysql_fetch_assoc($res))	{
				$teamMembers[$row["uid"]]=$row;
				$teamMembers[$row["uid"]]["_ISSET"]=1;
			}

				// Looking up potential users for membership:
			if ($this->piVars["DATA"]["lookup"])	{
				$LU_query = $this->cObj->searchWhere($this->piVars["DATA"]["lookup"],"uid,username,name,email,company,city,country","fe_users");
				$notIn = array_keys($teamMembers);
				$query = "SELECT * FROM fe_users WHERE ".
					" pid=".intval($this->dbPageId).
					(count($notIn) ? " AND uid NOT IN (".implode(",",$notIn).")" : "").
					" AND uid!=".intval($GLOBALS["TSFE"]->fe_user->user["uid"]).
					$LU_query.
					$this->cObj->enableFields("fe_users").
					" ORDER BY name,username".
					" LIMIT 30";

				$res = mysql(TYPO3_db,$query);
				echo mysql_error();
				while($row=mysql_fetch_assoc($res))	{
					$teamMembers[$row["uid"]]=$row;
				}
			}

#debug($memberArray);
			$fN = $this->prefixId."[DATA]";
			$formLines=array();
			reset($teamMembers);
			while(list($mUid,$mDat)=each($teamMembers))	{
				$formLines[]='<input type="checkbox" name="'.$fN.'[member][]" value="'.$mUid.'"'.($mDat["_ISSET"]?" CHECKED":"").'>'.$mDat["username"]." (".htmlentities(trim($mDat["name"])).", ".htmlentities(trim($mDat["email"])).")";
			}

			$out.='<form action="'.t3lib_div::getIndpEnv("REQUEST_URI").'" method="post" style="margin: 0px 0px 0px 0px;" name="editForm">
				<h3>Team members:</h3>
				<p>'.(count($formLines)?implode("<BR>",$formLines):"<em>None</em>").'</p>
					<br>
					<p><strong>Lookup users:</strong></p>
					<input type="text" name="'.$fN.'[lookup]"><BR>
					<BR>
					<input type="submit" name="'.$fN.'[submit]" value="Find/Set">
			</form><br>';

				// Back button:
			$out.='<p>'.$this->pi_linkTP_keepPIvars("Back to team list",array("teamEditUid"=>"")).'</p>';

		} elseif ($this->piVars["projEditUid"])	{
			$projRec = $this->pi_getRecord("tx_extrepmgm_project",$this->piVars["projEditUid"]);
			$teamRec = $this->pi_getRecord("tx_extrepmgm_team",$projRec['team_id']);
			$editOK = $GLOBALS["TSFE"]->loginUser && is_array($projRec) && (!strcmp($GLOBALS["TSFE"]->fe_user->user["uid"],$projRec["leader"]) || !strcmp($GLOBALS["TSFE"]->fe_user->user["uid"],$teamRec["leader"]));
			if ($editOK)	{

					// If a change is sent:
				if ($this->piVars["DATA"]["submit"])	{
						// Set members:
					$query="DELETE FROM tx_extrepmgm_project_members_mm WHERE uid_local=".intval($projRec["uid"]);
					$res = mysql(TYPO3_db,$query);

					if (is_array($this->piVars["DATA"]["member"]))	{
						reset($this->piVars["DATA"]["member"]);
						while(list($k,$fe_user_uid)=each($this->piVars["DATA"]["member"]))	{
							$query="INSERT INTO tx_extrepmgm_project_members_mm
								(uid_local,uid_foreign,sorting)
								VALUES
								(".intval($projRec["uid"]).",".intval($fe_user_uid).",".intval($k).")";
							$res = mysql(TYPO3_db,$query);
						}
					}

					$query = 'UPDATE tx_extrepmgm_project SET
								notepad="'.addslashes($this->piVars["DATA"]["projDat"]['notepad']).'",
								skills="'.addslashes($this->piVars["DATA"]["projDat"]['skills']).'",
								status="'.addslashes($this->piVars["DATA"]["projDat"]['status']).'"
								WHERE uid='.intval($projRec["uid"]).';';
					$res = mysql(TYPO3_db,$query);
					echo mysql_error();
					$projRec = $this->pi_getRecord("tx_extrepmgm_project",$this->piVars["projEditUid"]);
				}



				// *************************
				// DISPLAY member form:
				// **************************

				// Finding members of team
				$projMembers=array();
				$query = "SELECT * FROM tx_extrepmgm_team_members_mm,fe_users WHERE
						fe_users.pid=".intval($this->dbPageId).
				" AND tx_extrepmgm_team_members_mm.uid_foreign = fe_users.uid
						 AND tx_extrepmgm_team_members_mm.uid_local = ".intval($projRec["team_id"]).
				$this->cObj->enableFields("fe_users").
				" ORDER BY tx_extrepmgm_team_members_mm.sorting";
				$res = mysql(TYPO3_db,$query);
				echo mysql_error();
				while($row=mysql_fetch_assoc($res))	{
					$projMembers[$row["uid"]]=$row;
					$projMembers[$row["uid"]]["_ISSET"]=0;
				}


				// Finding members of project
				$query = "SELECT * FROM tx_extrepmgm_project_members_mm,fe_users WHERE
						fe_users.pid=".intval($this->dbPageId).
						" AND tx_extrepmgm_project_members_mm.uid_foreign = fe_users.uid
						 AND tx_extrepmgm_project_members_mm.uid_local = ".intval($projRec["uid"]).
						$this->cObj->enableFields("fe_users").
						" ORDER BY tx_extrepmgm_project_members_mm.sorting";
				$res = mysql(TYPO3_db,$query);
				echo mysql_error();
				while($row=mysql_fetch_assoc($res))	{
					$projMembers[$row["uid"]]=$row;
					$projMembers[$row["uid"]]["_ISSET"]=1;
				}

				// Looking up potential users for membership:
				if ($this->piVars["DATA"]["lookup"])	{
					$LU_query = $this->cObj->searchWhere($this->piVars["DATA"]["lookup"],"uid,username,name,email,company,city,country","fe_users");
					$notIn = array_keys($projMembers);
					$query = "SELECT * FROM fe_users WHERE ".
					" pid=".intval($this->dbPageId).
					(count($notIn) ? " AND uid NOT IN (".implode(",",$notIn).")" : "").
					" AND uid!=".intval($GLOBALS["TSFE"]->fe_user->user["uid"]).
					$LU_query.
					$this->cObj->enableFields("fe_users").
					" ORDER BY name,username".
					" LIMIT 30";

					$res = mysql(TYPO3_db,$query);
					echo mysql_error();
					while($row=mysql_fetch_assoc($res))	{
						$projMembers[$row["uid"]]=$row;
					}
				}

				#debug($memberArray);
				$fN = $this->prefixId."[DATA]";
				$formLines=array();
				reset($projMembers);
				while(list($mUid,$mDat)=each($projMembers))	{
					$formLines[]='<input type="checkbox" name="'.$fN.'[member][]" value="'.$mUid.'"'.($mDat["_ISSET"]?" CHECKED":"").'>'.$mDat["username"]." (".htmlentities(trim($mDat["name"])).", ".htmlentities(trim($mDat["email"])).")";
				}



				$out.='<form action="'.t3lib_div::getIndpEnv("REQUEST_URI").'" method="post" style="margin: 0px 0px 0px 0px;" name="editForm">
				<h3>Project members:</h3>
				<p>'.(count($formLines)?implode("<BR>",$formLines):"<em>None</em>").'</p>
					<br>
					<p><strong>Lookup users:</strong></p>
					<input type="text" name="'.$fN.'[lookup]"><BR>
					<BR>

					<b>Status:</b><br>
					<textarea name="'.$fN.'[projDat][status]" style="width: 200px;" rows="3">'.t3lib_div::formatForTextarea($projRec['status']).'</textarea><br>

					<b>Skills:</b><br>
					<textarea name="'.$fN.'[projDat][skills]" style="width: 200px;" rows="3">'.t3lib_div::formatForTextarea($projRec['skills']).'</textarea><br>

					<b>Notepad:</b><br>
					<textarea name="'.$fN.'[projDat][notepad]" style="width: 500px;" rows="10">'.t3lib_div::formatForTextarea($projRec['notepad']).'</textarea><br>


					<input type="submit" name="'.$fN.'[submit]" value="Find/Set">
			</form><br>';

				// Back button:
				$out.='<p>'.$this->pi_linkTP_keepPIvars("Back to team list",array("projEditUid"=>"")).'</p>';
			}
		} elseif ($this->piVars["projUid"])	{
			$projRec = $this->pi_getRecord("tx_extrepmgm_project",$this->piVars["projUid"]);

			$emailArr=array();

				// Finding supervisor, leader, team members.
			$leader = $this->pi_getRecord("fe_users",$projRec["leader"]);
			$emailArr[]=$leader['email'];
			$leader = (is_array($leader)?$this->cObj->getTypoLink($leader["name"].' ('.$leader["username"].')',$leader["email"]):'');

				// Finding team members.
			$projMembers=array();
			$query = "SELECT * FROM tx_extrepmgm_project_members_mm,fe_users WHERE
						fe_users.pid=".intval($this->dbPageId).
						" AND tx_extrepmgm_project_members_mm.uid_foreign = fe_users.uid
						 AND tx_extrepmgm_project_members_mm.uid_local = ".intval($projRec["uid"]).
						$this->cObj->enableFields("fe_users").
						" ORDER BY tx_extrepmgm_project_members_mm.sorting";
			$res2 = mysql(TYPO3_db,$query);
			echo mysql_error();
			while($row2=mysql_fetch_assoc($res2))	{
				$projMembers[]=$this->cObj->getTypoLink($row2["name"].' ('.$row2["username"].')',$row2["email"]);
				$emailArr[]=$row2['email'];
			}
			$emailArr = array_unique(t3lib_div::trimExplode(',',implode(',',$emailArr),1));


			$teamStuff='';
			$teamStuff.='<h3>Project: '.htmlspecialchars(trim($projRec['title'])).'</h3>';
			$teamStuff.='<table '.$this->conf["listTeam."]["tableParams"].'>';
			$teamStuff.='<tr><td'.$this->pi_classParam("HCell").' nowrap><strong>Description:</strong></td><td><em>'.nl2br(htmlspecialchars(trim($projRec['description']))).'</em></td></tr>';
			$teamStuff.='<tr><td'.$this->pi_classParam("HCell").' nowrap><strong>Status:</strong></td><td>'.nl2br(htmlspecialchars(trim($projRec['status']))).'</td></tr>';
			$teamStuff.='<tr><td'.$this->pi_classParam("HCell").' nowrap><strong>Skills:</strong></td><td>'.nl2br(htmlspecialchars(trim($projRec['skills']))).'</td></tr>';
			if ($leader)		$teamStuff.='<tr><td'.$this->pi_classParam("HCell").' nowrap><strong>Leader:</strong></td><td>'.$leader.'</td></tr>';
			if (count($projMembers))	$teamStuff.='<tr><td'.$this->pi_classParam("HCell").' nowrap><strong>Project members:</strong></td><td>'.implode('<br />',$projMembers).'</td></tr>';
			if ($GLOBALS["TSFE"]->loginUser)		$teamStuff.='<tr><td'.$this->pi_classParam("HCell").' nowrap><strong>Email list:</strong></td><td><em>'.htmlspecialchars(implode(', ',$emailArr)).'</em></td></tr>';
			$teamStuff.='<tr><td'.$this->pi_classParam("HCell").' nowrap><strong>Notepad:</strong></td><td>'.nl2br(strip_tags(trim($projRec['notepad']),'<a><h4>')).'</td></tr>';
			$teamStuff.='</table>';

			$out.=$teamStuff;
				// Back button:
				$out.='<p>'.$this->pi_linkTP_keepPIvars("Back to team list",array("projUid"=>"")).'</p>';

		} else {	// LISTING:
			$query = "SELECT * FROM tx_extrepmgm_team WHERE ".
						"pid=".intval($this->dbPageId).
						$this->cObj->enableFields("tx_extrepmgm_team").
						" ORDER BY sorting";
			$res = mysql(TYPO3_db,$query);
			$out='';

			$indexList=array();
			$globalLeaderEmails=array();
			$globalTeamLeaderEmails=array();

			while($row=mysql_fetch_assoc($res))	{
				$emailArr=array();

					// Finding supervisor, leader, team members.
				$leader = $this->pi_getRecord("fe_users",$row["leader"]);
				$emailArr[]=$globalLeaderEmails[]=$globalTeamLeaderEmails[]=$leader['email'];
				$leader = (is_array($leader)?$this->cObj->getTypoLink($leader["name"].' ('.$leader["username"].')',$leader["email"]):'');

				$supervisor = $this->pi_getRecord("fe_users",$row["supervisor"]);
				$emailArr[]=$globalLeaderEmails[]=$globalTeamLeaderEmails[]=$supervisor['email'];
				$supervisor = (is_array($supervisor)?$this->cObj->getTypoLink($supervisor["name"].' ('.$supervisor["username"].')',$supervisor["email"]):'');

					// Finding team members.
				$teamMembers=array();
				$query = "SELECT * FROM tx_extrepmgm_team_members_mm,fe_users WHERE
							fe_users.pid=".intval($this->dbPageId).
							" AND tx_extrepmgm_team_members_mm.uid_foreign = fe_users.uid
							 AND tx_extrepmgm_team_members_mm.uid_local = ".intval($row["uid"]).
							$this->cObj->enableFields("fe_users").
							" ORDER BY tx_extrepmgm_team_members_mm.sorting";
				$res2 = mysql(TYPO3_db,$query);
				echo mysql_error();
				while($row2=mysql_fetch_assoc($res2))	{
					$teamMembers[]=$this->cObj->getTypoLink($row2["name"].' ('.$row2["username"].')',$row2["email"]);
					$emailArr[]=$row2['email'];
				}
				$emailArr = array_unique(t3lib_div::trimExplode(',',implode(',',$emailArr),1));

					// Edit?
				if ($GLOBALS["TSFE"]->loginUser
						&& !strcmp($GLOBALS["TSFE"]->fe_user->user["uid"],$row["leader"]))	{
					$teamMembers[]='<span'.$this->pi_classParam("admLink").'>'.$this->pi_linkTP_keepPIvars("ADMIN",array("teamEditUid"=>$row["uid"])).'</span>';
				}

				$indexList[]='<li>Team: <B><a href="#team_'.$row['uid'].'">'.htmlspecialchars(trim($row['title'])).'</a></B> - <i>Leader: '.$leader.'</i></li>';

				$teamStuff='';
				$teamStuff.='<a name="team_'.$row['uid'].'"></a><h3>Team: '.htmlspecialchars(trim($row['title'])).'</h3>';
				$teamStuff.='<table '.$this->conf["listTeam."]["tableParams"].'>';
				$teamStuff.='<tr><td'.$this->pi_classParam("HCell").' nowrap><strong>Purpose:</strong></td><td><em>'.htmlspecialchars(trim($row['purpose'])).'</em></td></tr>';
				if ($supervisor)	$teamStuff.='<tr><td'.$this->pi_classParam("HCell").' nowrap><strong>Supervisor:</strong></td><td>'.$supervisor.'</td></tr>';
				if ($leader)		$teamStuff.='<tr><td'.$this->pi_classParam("HCell").' nowrap><strong>Leader:</strong></td><td>'.$leader.'</td></tr>';
				if (count($teamMembers))	$teamStuff.='<tr><td'.$this->pi_classParam("HCell").' nowrap><strong>Team members:</strong></td><td>'.implode('<br />',$teamMembers).'</td></tr>';
				if ($GLOBALS["TSFE"]->loginUser)		$teamStuff.='<tr><td'.$this->pi_classParam("HCell").' nowrap><strong>Email list:</strong></td><td><em>'.htmlspecialchars(implode(', ',$emailArr)).'</em></td></tr>';
				$teamStuff.='</table>';

				$out.=$teamStuff;


					// Selecting the projects for a team:
				$query = "SELECT * FROM tx_extrepmgm_project WHERE ".
							"pid=".intval($this->dbPageId).
							' AND team_id='.intval($row['uid']).
							$this->cObj->enableFields("tx_extrepmgm_project").
							" ORDER BY priority DESC, title";
				$res_project = mysql(TYPO3_db,$query);

				if (mysql_numrows($res_project))	{
					$indexPList=array();

					$lines=array();
					$lines[]='<tr>
						<td'.$this->pi_classParam("HCell").' nowrap>Title:</td>
						<td'.$this->pi_classParam("HCell").' nowrap>Description:</td>
						<td'.$this->pi_classParam("HCell").' nowrap>Status:</td>
						<td'.$this->pi_classParam("HCell").' nowrap>Project members:</td>
					</tr>';

						// For each language:
					$c=0;
					$localLeaderEmails=array();
					while($prow=mysql_fetch_assoc($res_project))	{

						$pleader = $this->pi_getRecord("fe_users",$prow["leader"]);
						$globalLeaderEmails[]=$localLeaderEmails[]=$pleader['email'];
						$pleader = (is_array($pleader)?$this->cObj->getTypoLink($pleader["name"].' ('.$pleader["username"].')',$pleader["email"]):'');

						$indexPList[]='<li>Project: <B><a href="#proj_'.$prow['uid'].'">'.htmlspecialchars(trim($prow['title'])).'</a></B> - <i>Leader: '.$pleader.'</i></li>';

							// Finding team members:
						$projMembers=array();
						$query = "SELECT * FROM tx_extrepmgm_project_members_mm,fe_users WHERE
									fe_users.pid=".intval($this->dbPageId).
									" AND tx_extrepmgm_project_members_mm.uid_foreign = fe_users.uid
									 AND tx_extrepmgm_project_members_mm.uid_local = ".intval($prow["uid"]).
									$this->cObj->enableFields("fe_users").
									" ORDER BY tx_extrepmgm_project_members_mm.sorting";
						$res2 = mysql(TYPO3_db,$query);
						echo mysql_error();
						while($row2=mysql_fetch_assoc($res2))	{
							$projMembers[]=$this->cObj->getTypoLink($row2["name"].' ('.$row2["username"].')',$row2["email"]);
						}

							// Edit?
						$editLink='';
						if ($GLOBALS["TSFE"]->loginUser
								&& (!strcmp($GLOBALS["TSFE"]->fe_user->user["uid"],$prow["leader"]) || !strcmp($GLOBALS["TSFE"]->fe_user->user["uid"],$row["leader"])))	{
							$editLink='<span'.$this->pi_classParam("admLink").'>'.$this->pi_linkTP_keepPIvars("ADMIN",array("projEditUid"=>$prow["uid"])).'</span>';
						}

							// Making row:
						$pp=($c%2 ? $this->pi_classParam("odd"):'');
						$lines[]='<tr>
							<td'.$pp.'><a name="proj_'.$prow['uid'].'"></a>'.$this->pi_linkTP_keepPIvars(htmlspecialchars($prow["title"]),array("projUid"=>$prow["uid"])).
											($prow["priority"]?'<br /><em>('.$this->priorityLabels[$prow["priority"]].')</em>':'').
											($editLink?'<br />'.$editLink:'').'</td>
							<td'.$pp.'>'.$this->pi_linkTP_keepPIvars(trim(nl2br(htmlspecialchars($prow["description"]))),array("projUid"=>$prow["uid"])).'</td>
							<td'.$pp.'>'.trim(nl2br(htmlspecialchars($prow["status"]))).'</td>
							<td'.$pp.' nowrap><b>'.$pleader.'</b><br />'.implode('<br/>',$projMembers).'</td>
						</tr>';
						$c++;
					}
					$out.='<h4>Team Projects:</h4><table '.$this->conf["listTeam."]["tableParams"].'>'.implode(chr(10),$lines).'</table><br /><br />';

					$localLeaderEmailArr = array_unique(t3lib_div::trimExplode(',',implode(',',$localLeaderEmails),1));
					if ($GLOBALS["TSFE"]->loginUser)  $out.='<p><b>Project leader emails:</b> <em>'.implode(', ',$localLeaderEmailArr).'</em></p>';

					$indexList[]='<ul>'.implode('',$indexPList).'</ul>';
				}
				$out.='<hr />';
			}

			$out = '<h3>Team/Project Index:</h3><ul>'.implode('',$indexList).'</ul><hr>'.$out;

			if ($GLOBALS["TSFE"]->loginUser)  {
				$globalTeamLeaderEmailArr = array_unique(t3lib_div::trimExplode(',',implode(',',$globalTeamLeaderEmails),1));
				$out.='<p><b>ALL Team leader emails:</b> <em>'.implode(', ',$globalTeamLeaderEmailArr).'</em></p>';

				$globalLeaderEmailArr = array_unique(t3lib_div::trimExplode(',',implode(',',$globalLeaderEmails),1));
				$out.='<p><b>ALL Team+Project leader emails:</b> <em>'.implode(', ',$globalLeaderEmailArr).'</em></p>';
			}
		}
		return '<DIV'.$this->pi_classParam("tplist").'>'.$out.'</DIV>';
	}
}

if (defined("TYPO3_MODE") && $TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/extrep_mgm/pi1/class.tx_extrepmgm_pi1.php"])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/extrep_mgm/pi1/class.tx_extrepmgm_pi1.php"]);
}
?>

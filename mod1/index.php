<?php
/***************************************************************
*  Copyright notice
*  
*  (c) 2002-2004 Kasper Skårhøj (kasper@typo3.com)
*  All rights reserved
*
*  This script is part of the Typo3 project. The Typo3 project is 
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
 * Module 'Extension Repository Analysis' for the 'extrep_mgm' extension.
 *
 * @author    Kasper Skårhøj <kasper@typo3.com>
 */


    // DEFAULT initialization of a module [BEGIN]
unset($MCONF);    
require ("conf.php");
require ($BACK_PATH."init.php");
require ($BACK_PATH."template.php");
include ("locallang.php");
require_once (PATH_t3lib."class.t3lib_scbase.php");
$BE_USER->modAccess($MCONF,1);    // This checks permissions and exits if the users has no permission for entry.
    // DEFAULT initialization of a module [END]

class tx_extrepmgm_module1 extends t3lib_SCbase {
    var $pageinfo;

    /**
     * 
     */
    function init()    {
        global $AB,$BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$HTTP_GET_VARS,$HTTP_POST_VARS,$CLIENT,$TYPO3_CONF_VARS;

        parent::init();
    }

    /**
     * Adds items to the ->MOD_MENU array. Used for the function menu selector.
     */
    function menuConfig()    {
        global $LANG;
        $this->MOD_MENU = Array (
            "function" => Array (
                "1" => $LANG->getLL("function1"),
                "2" => $LANG->getLL("function2"),
                "3" => $LANG->getLL("function3"),
				"4" => "Download stats",
            )
        );
        parent::menuConfig();
    }

        // If you chose "web" as main module, you will need to consider the $this->id parameter which will contain the uid-number of the page clicked in the page tree
    /**
     * Main function of the module. Write the content to $this->content
     */
    function main()    {
        global $AB,$BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$HTTP_GET_VARS,$HTTP_POST_VARS,$CLIENT,$TYPO3_CONF_VARS;
        
        // Access check!
        // The page will show only if there is a valid page and if this page may be viewed by the user
        $this->pageinfo = t3lib_BEfunc::readPageAccess($this->id,$this->perms_clause);
        $access = is_array($this->pageinfo) ? 1 : 0;
        
        if (($this->id && $access) || ($BE_USER->user["admin"] && !$this->id))    {
    
                // Draw the header.
            $this->doc = t3lib_div::makeInstance("mediumDoc");
            $this->doc->backPath = $BACK_PATH;
            $this->doc->form='<form action="" method="POST">';

                // JavaScript
            $this->doc->JScode = '
                <script language="javascript">
                    script_ended = 0;
                    function jumpToUrl(URL)    {
                        document.location = URL;
                    }
                </script>
            ';
            $this->doc->postCode='
                <script language="javascript">
                    script_ended = 1;
                    if (top.theMenu) top.theMenu.recentuid = '.intval($this->id).';
                </script>
            ';

            $headerSection = $this->doc->getHeader("pages",$this->pageinfo,$this->pageinfo["_thePath"])."<br>".$LANG->php3Lang["labels"]["path"].": ".t3lib_div::fixed_lgd_pre($this->pageinfo["_thePath"],50);

            $this->content.=$this->doc->startPage($LANG->getLL("title"));
            $this->content.=$this->doc->header($LANG->getLL("title"));
            $this->content.=$this->doc->spacer(5);
            $this->content.=$this->doc->section("",$this->doc->funcMenu($headerSection,t3lib_BEfunc::getFuncMenu($this->id,"SET[function]",$this->MOD_SETTINGS["function"],$this->MOD_MENU["function"])));
            $this->content.=$this->doc->divider(5);

            
            // Render content:
            $this->moduleContent();

            
            // ShortCut
            if ($BE_USER->mayMakeShortcut())    {
                $this->content.=$this->doc->spacer(20).$this->doc->section("",$this->doc->makeShortcutIcon("id",implode(",",array_keys($this->MOD_MENU)),$this->MCONF["name"]));
            }
        
            $this->content.=$this->doc->spacer(10);
        } else {
                // If no access or if ID == zero
        
            $this->doc = t3lib_div::makeInstance("mediumDoc");
            $this->doc->backPath = $BACK_PATH;
        
            $this->content.=$this->doc->startPage($LANG->getLL("title"));
            $this->content.=$this->doc->header($LANG->getLL("title"));
            $this->content.=$this->doc->spacer(5);
            $this->content.=$this->doc->spacer(10);
        }
    }

    /**
     * Prints out the module HTML
     */
    function printContent()    {
        global $SOBE;

        $this->content.=$this->doc->middle();
        $this->content.=$this->doc->endPage();
        echo $this->content;
    }
    
    /**
     * Generates the module content
     */
    function moduleContent()    {
        switch((string)$this->MOD_SETTINGS["function"])    {
            case 1:
					// Selecting all extensions that HAS repository records associated.
				$query="SELECT 
							tx_extrep_keytable.extension_key, 
							tx_extrep_keytable.owner_fe_user, 
							tx_extrep_keytable.title, 
							tx_extrep_keytable.members_only,
							tx_extrep_repository.version, 
							tx_extrep_repository.datasize_gz
							
						FROM tx_extrep_keytable,tx_extrep_repository
						WHERE tx_extrep_keytable.uid=tx_extrep_repository.extension_uid
						AND tx_extrep_keytable.pid=".intval($this->id)." 
						AND tx_extrep_repository.pid=".intval($this->id).
						t3lib_BEfunc::deleteClause("tx_extrep_keytable").
						t3lib_BEfunc::deleteClause("tx_extrep_repository").
						" ORDER BY tx_extrep_keytable.owner_fe_user";

				$res = mysql(TYPO3_db, $query);
				echo mysql_error();
				$grouping=array();
				while($row=mysql_fetch_assoc($res))	{
					$grouping[$row["extension_key"]][]=$row;
				}
				
					// Displaying them:
				$content="";
				$prevUser=0;
				$prevCount=0;
				$prevCount_mem=0;
				$allCount=0;
				$allCount_mem=0;
				$allCount_users=0;

				reset($grouping);
				while(list($extKey,$subrecords)=each($grouping))	{
						
					$subcontent="";
					$size=0;
					$c=0;
					$title="";
					reset($subrecords);
					while(list(,$rec)=each($subrecords))	{
						$subcontent.='<tr>
							<td>'.$rec["version"].'</td>
							<td nowrap>'.t3lib_div::formatSize($rec["datasize_gz"]).'</td>
							<td>&nbsp;</td>
						</tr>';
						$size+=$rec["datasize_gz"];
						$title=$rec["title"];
						$owner_fe_user=$rec["owner_fe_user"];
						$members_only=$rec["members_only"];
						$c++;
					}
					
					if ($prevUser!=$owner_fe_user)	{
						$allCount_users++;
						if ($prevUser)	{
							$content.='<tr>
									<td colspan=3>Count: '.($prevCount-$prevCount_mem).'+'.$prevCount_mem.'='.$prevCount.'<BR><BR><BR></td>
								</tr>';
							$prevCount=0;
							$prevCount_mem=0;
						}
						$content.='<tr bgcolor="'.$this->doc->bgColor2.'">
								<td colspan=3><BR><strong>User: '.$this->getUserName($owner_fe_user).'</strong><BR></td>
							</tr>';
					}

					$prevCount++;
					$allCount++;
					if ($members_only)	{
						$prevCount_mem++;
						$allCount_mem++;
					}
					$prevUser=$owner_fe_user;
					
					
					$content.='<tr bgcolor="'.(!$members_only?$this->doc->bgColor5:$this->doc->bgColor6).'">
							<td nowrap colspan=3><strong>'.$title.'</strong> - <em>'.$extKey.'</em></td>
						</tr>';
					$content.='<tr bgcolor="'.$this->doc->bgColor4.'">
							<td nowrap'.($c>10?' bgcolor="red"':'').'>'.$c.'</td>
							<td nowrap'.($size>500000?' bgcolor="red"':'').'>'.t3lib_div::formatSize($size).'</td>
							<td>'.$this->getUserName($owner_fe_user).'</td>
						</tr>';
					$content.=$subcontent;
				}

					// For the last user, set count:
				$content.='<tr>
						<td colspan=3>Count: '.($prevCount-$prevCount_mem).'+'.$prevCount_mem.'='.$prevCount.'<BR><BR><BR></td>
					</tr>';
					
				$listcontent='<table border=0 cellpadding=0 cellspacing=1>'.$content.'</table>';
				
					// Begin stats:
				$content='<strong>All users:</strong> '.$allCount_users.'<BR>';
				$content.='<strong>Public+Member=All:</strong> '.($allCount-$allCount_mem).'+'.$allCount_mem.'='.$allCount.'<BR>';
				
                $this->content.=$this->doc->section("All stats:",$content,0,1);
                $this->content.=$this->doc->section("Extension keys with uploads:",$listcontent,0,1);
            break;
            case 2:
					// Selecting all extensions that HAS repository records associated.
				$uids = $this->getAllKeyUidsWithUploads();

				if (count($uids))	{
					$query="SELECT 
							tx_extrep_keytable.*
						FROM tx_extrep_keytable
						WHERE tx_extrep_keytable.uid NOT IN (".implode(",",$uids).")
						AND tx_extrep_keytable.pid=".intval($this->id).
						t3lib_BEfunc::deleteClause("tx_extrep_keytable")."
						ORDER BY ".(t3lib_div::GPvar("sortUsername")?" owner_fe_user":" crdate");

					$trows=array();
					$trows[]='<tr bgcolor="'.$this->doc->bgColor5.'">
						<td>Ext. Key:</td>
						<td><a href="'.t3lib_div::linkThisScript(array("sortUsername"=>1)).'">Username:</a></td>
						<td>Creation date:</td>
					</tr>';

					$res = mysql(TYPO3_db, $query);
					while($row=mysql_fetch_assoc($res))	{
						$trows[]='<tr>
							<td>'.$row["extension_key"].'</td>
							<td>'.$this->getUserName($row["owner_fe_user"]).'</td>
							<td>'.t3lib_BEfunc::calcAge(time()-$row["crdate"],$GLOBALS["LANG"]->php3Lang["labels"]["minutesHoursDaysYears"]).'</td>
						</tr>';
					}
					
					$content='<table border=1 cellpadding=0 cellspacing=0>'.implode("",$trows).'</table>';
    	            $this->content.=$this->doc->section("Extension keys without uploaded versions:",$content,0,1);
				}
            break;
            case 3:
					// Selecting all extensions that HAS repository records associated.
				$uids = $this->getAllKeyUidsWithUploads();

				if (count($uids))	{
					$query="SELECT 
							uid,extension_key, version, last_upload_date, extension_uid
						FROM tx_extrep_repository
						WHERE tx_extrep_repository.extension_uid NOT IN (".implode(",",$uids).")
						AND tx_extrep_repository.pid=".intval($this->id).
						t3lib_BEfunc::deleteClause("tx_extrep_repository")."
						ORDER BY extension_uid,version_int
						";

					if (t3lib_div::GPvar("_DELETE_ALL"))	{
						$versions=array();
						$res = mysql(TYPO3_db, $query);
						while($row=mysql_fetch_assoc($res))	{
							$versions[]=$row["extension_key"]."-".$row["extension_uid"]."-".$row["version"];

								// Delete:
							mysql(TYPO3_db,"DELETE FROM tx_extrep_repository WHERE uid=".intval($row["uid"]));
							echo mysql_error();
						}					


						$content='Deleted:<BR><BR> <em>'.implode("<BR>",$versions).'</em>';
					} else {
						$trows=array();
						$trows[]='<tr bgcolor="'.$this->doc->bgColor5.'">
							<td>Ext. key</td>
							<td>Ext. uid</td>
							<td>Version:</td>
							<td>Last upload:</td>
						</tr>';
	
						$res = mysql(TYPO3_db, $query);
						while($row=mysql_fetch_assoc($res))	{
							$trows[]='<tr>
								<td>'.$row["extension_key"].'</td>
								<td>'.$row["extension_uid"].'</td>
								<td>'.$row["version"].'</td>
								<td>'.t3lib_BEfunc::calcAge(time()-$row["last_upload_date"],$GLOBALS["LANG"]->php3Lang["labels"]["minutesHoursDaysYears"]).'</td>
							</tr>';
						}
						
						$content='<table border=1 cellpadding=0 cellspacing=2>'.implode("",$trows).'</table>';
						
						$content.='<input type="submit" name="_DELETE_ALL" value="Delete All Lost">';
					}
					
    	            $this->content.=$this->doc->section("Uploads with no extension key:",$content,0,1);
				}
            break;
			case "4":
				$query = "SELECT uid FROM tx_extrep_downloadstat 
						GROUP BY download_path_hash";
				$res = mysql(TYPO3_db,$query);
				$totalCount=mysql_num_rows($res);

				$query = "SELECT count(*) FROM tx_extrep_downloadstat";
				$res = mysql(TYPO3_db,$query);
				$totalCountAll=mysql_num_rows($res);

				$query = "SELECT count(*) AS number_connect, download_referer FROM tx_extrep_downloadstat 
						GROUP BY download_path_hash 
						ORDER BY number_connect DESC
						LIMIT 300";
				$res = mysql(TYPO3_db,$query);

				$lines=array();
				while($row=mysql_fetch_assoc($res))	{
					list($ref) = explode("mod/tools/em/index.php",$row["download_referer"]);
					$lines[]='<tr><td>'.$row["number_connect"].'</td><td><a href="'.$ref.'" target="NEWWINDOW">'.$ref.'</a> - <a href="'.substr($ref,0,-6).'" target="NEWWINDOW">SITE</a></td></tr>';
				}

				$content="";
				$content.="Total connection: ".$totalCountAll."<BR>";
				$content.="Total unique installations connected: ".$totalCount."<BR>";
				$content.='<table border=1 cellpadding=0 cellspacing=0>'.implode("",$lines).'</table>';
				
   	            $this->content.=$this->doc->section("Statistics:",$content,0,1);
			break;
        } 
    }

	/**
	 * Returns the user name of a fe_users.uid
	 */
	function getUserName($fe_users_uid)	{
		if (!isset($this->cache_fe_user_names[$fe_users_uid]))	{
			$fe_user_rec = t3lib_BEfunc::getRecord("fe_users", $fe_users_uid);
			$this->cache_fe_user_names[$fe_users_uid] = "".$fe_user_rec["username"];
		}
		return $this->cache_fe_user_names[$fe_users_uid];
	}
	
	/**
	 * REturns an array with uids of key-table records which HAS uploads in the repository table of this page id.
	 */
	function getAllKeyUidsWithUploads()	{
		$query="SELECT 
					tx_extrep_keytable.uid
				FROM tx_extrep_keytable,tx_extrep_repository
				WHERE tx_extrep_keytable.uid=tx_extrep_repository.extension_uid
				AND tx_extrep_keytable.pid=".intval($this->id)." 
				AND tx_extrep_repository.pid=".intval($this->id).
				t3lib_BEfunc::deleteClause("tx_extrep_keytable").
				t3lib_BEfunc::deleteClause("tx_extrep_repository");

		$uids=array();
		$res = mysql(TYPO3_db, $query);
		echo mysql_error();
		$grouping=array();
		while($row=mysql_fetch_assoc($res))	{
			$uids[]=$row["uid"];
		}
		return $uids;
	}
	
}



if (defined("TYPO3_MODE") && $TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/extrep_mgm/mod1/index.php"])    {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/extrep_mgm/mod1/index.php"]);
}




// Make instance:
$SOBE = t3lib_div::makeInstance("tx_extrepmgm_module1");
$SOBE->init();

// Include files?
reset($SOBE->include_once);    
while(list(,$INC_FILE)=each($SOBE->include_once))    {include_once($INC_FILE);}

$SOBE->main();
$SOBE->printContent();

?>
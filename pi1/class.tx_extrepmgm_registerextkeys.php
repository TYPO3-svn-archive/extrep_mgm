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
 * Plugin 'Extension Manager Frontend' for the 'extrep_mgm' extension.
 *
 * @author		Kasper Skårhøj <kasperYYYY@typo3.com>
 * @co-author	Robert Lemke <robert@typo3.org>
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   53: class tx_extrepmgm_registerextkeys extends tx_extrepmgm_pi1
 *   60:     function main()
 *  194:     function registerKeyFromPIData()
 *  213:     function randomWordPrefix()
 *  233:     function validateSubmittedKey()
 *  256:     function validateExtensionKey($extKey)
 *  292:     function checkUniquenessOfKey($extKey)
 *  307:     function linkThisCmd($uPA=array())
 *  319:     function piFieldName($key)
 *  328:     function cmdHiddenField()
 *
 * TOTAL FUNCTIONS: 9
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */

require_once(t3lib_extMgm::extPath('extrep_mgm').'pi1/class.tx_extrepmgm_pi1.php');

class tx_extrepmgm_registerextkeys extends tx_extrepmgm_pi1 {

	/**
	 * Output the registration HTML form etc.
	 *
	 * @return	[type]		...
	 */
	function main()	{

		if ($this->checkLogin()) {
			return "<p>You are not logged in. You have to do so before you can register extension keys.</p>";
		}

		list($OK,$errors) = $this->validateSubmittedKey();
		$extKey = trim($this->piData['regKey']);

		if ($OK)	{
			if ($this->piData['registerNow'] &&
				trim($this->piData['field_title']) )	{

				$this->registerKeyFromPIData();

				$content.='<p>You have now registered the key "'.$extKey.'"</p>

				';
			} else {
				if ($this->piData['registerNow'])	{
					$content.='<p><span style="color:red;font-weight:bold;">You didn\'t fill in all fields! Please do so!</span></p>';
				}
				$content.='
					<p>Key "'.$extKey.'" was not registered, so you can have it...</p>
					<p>Registering a key means that you will posses the right to use this key for an extension in TYPO3. This makes it possible to share your code later on and being sure that no one else will use the same table names, module names etc. as you do (provided you follow the naming conventions). Basically you\'ve got you own namespace!</p>
					<p>You are not required to share an extension registered here. By default your uploaded extensions are marked "members only" which means that they do NOT show up in the public list of extensions. When you are ready to publish your extension later, you simply unset this flag.</p>
					<p>If you wish to go on, please enter your information here:</p>

					<form action="'.$this->linkThisCmd().'" method="post">
					<input type="hidden" name="'.$this->piFieldName('regKey').'" value="'.htmlspecialchars($extKey).'" />


					<p>Your name: '.$GLOBALS['TSFE']->fe_user->user['name'].'<br />
					Your email: '.$GLOBALS['TSFE']->fe_user->user['email'].'<br />
					Your Company: '.$GLOBALS['TSFE']->fe_user->user['company'].'</p>

					<p><em>(Info from your user profile. If this is wrong, go edit your user profile!)</em></p>

					<p>Extension Title <strong>(required)</strong>:</p>
					<p><input type="text" name="'.$this->piFieldName('field_title').'" value="'.htmlspecialchars($this->piData['field_title']).'"><br>
						<em>(Name your extension with a title)</em></p>

					<p>Description:</p>
					<p><textarea cols="30" rows="5" name="'.$this->piFieldName('field_description').'">'.htmlspecialchars($this->piData['field_description']).'</textarea><br>
						<em>(Please make a short and clear statement about what this extension is about)</em></p>

					<p>Upload password (not required for now - if no password, no upload possible):</p>
					<p><input type="text" name="'.$this->piFieldName('field_up').'" value="'.htmlspecialchars($this->piData['field_up']).'"><br>
						<em>(The upload password is used when you want to update the repository with new versions of your extension)</em></p>

					<p>
					<input type="submit" value="Register key">
					<input type="hidden" value="1" name="'.$this->piFieldName('registerNow').'">
					<input type="hidden" value="'.$GLOBALS['TSFE']->fe_user->user['uid'].'" name="'.$this->piFieldName('owner_fe_user').'">
					'.$this->cmdHiddenField().'</p>
					</form>
				';
			}
		} else {
			if ($errors)	{
				$content.='
					<p><strong>These were some errors with your keyname:</strong></p>
				'.$errors;
			} else {
				$content.='
					<p>An extension key is a string which uniquely identifies your extension worldwide.<BR>
						Having a unique extension key ensures that you can name modules, plugins, PHP-classes, database tables and fields with a prefix that others do not use. It garantees global portability and compatibility.<br>
						Registration is free and encouraged by the TYPO3 community for all extensions you make.</p>
					<p>Enter a keyname you want to register. It will be validated and checked. If the extension key is not already registered, you\'ll have the chance to do it immediately hereafter.</p>
					<p><strong>Good keys</strong> are some, which reflect what the extension is about. Examples:</p>

					<ul>
						<li>A message board named "Michaels Super Board". Example key: "<em>mc_superboard</em>"</li>
						<li>A booking system called "Hotel Manager" for hotels. Example key: "<em>hotelmgr</em>"</li>
						<li>A plugin (poll system) in a series of plugins made by you or your company which is named "Direct People Technology". Example key: "<em>dpt_pollsystem</em>"</li>
						<li>A skin for TYPO3 with aliens in the background image, named "Black is Back". Example key: "<em>skinb2b</em>"</li>
					</ul>
					<p><strong>Notice:</strong> <em>Using "_" (underscores) in your keys is discouraged since it will make the namespace more complex for you to manage. If possible, please avoid underscores!</em></p>
					<br>
					<p><strong>Bad keys</strong> are strings which convey no information. Examples:</p>

					<ul>
						<li>"<em>asdf</em>" - the typical default "whatever"-string. If you want to test the Extension Repository, please use at least a key like "test_asdf"...</li>
						<li>"<em>d_d_o</em>" - is too much of an abbreviation to tells us anything.</li>
						<li>"<em>my_super_module_for_typo3</em>" - this begs the question how it can be anything near "super" when you couldn\'t come up with a good extension key...</li>
						<li>"i_always_use_underscores" - Is bad because it uses underscores (see notice above). For your own sake.</li>
						<li>"ilove_very_long_extensionskeys" - You will love long extension keys only until you see all your classnames, tables, fields etc. prefixed with it. Keep them SHORT!</li>
						<li>"iLoveUpperCASE" - Uppercase is NOT allowed.</li>
					</ul>
					<br>
					<p>Some of these "bad examples" <em>are</em> allowed but they doesn\'t communicate anything usefull for the extension.</p>

					<h3>Guidelines for good keys:</h3>
					<ol>
						<li>It should make sense.</li>
						<li>It should <em>not</em> have to be changed. When the extension key has been picked, it\'s not so easy to change it.</li>
						<li>Avoid underscores if you can (stick to a-z0-9) - that will provide you with the least confusing naming of your modules, tables, classes.</li>
						<li>Keep it short, less than 10 characters.</li>
						<li>All in lowercase.</li>
						<li>Although the primary purpose of the an extension key is to be unique rather than convey information, you might look up which keys others has registered for which kind of extensions - that might help you settle for a good key!</li>
						<li>Want to test this? Just enter any string prefixed "test_"...</li>
					</ol>
					<br>
					<p>Anyways, all technical limitations is validated when you submit a string, so just go ahead now...</p>

					<h3>Terms of use:</h3>
					<p><strong>By registering an extension key you accept that <em>all content</em> uploaded to TER (TYPO3.org Extension Repository) agrees to these terms:</strong>
					<ul>
						<li>Published under the <strong>GPL license</strong></li>
						<li><strong>You hold the copyright</strong> of the code or <strong>does not infringe the rights of others</strong> (meaning that work from others must be under GPL already!)</li>
					</ul>
					Any extensions found to break these terms will be removed <em>without further notice</em> by the webmaster of TYPO3.org.<br />
					The webmaster of TYPO3.org <strong>refuses to accept any responsibility</strong> for the content of extensions found in the repository since that responsibility is on the owner of the associated extension key who is in control of the uploaded content.
					</p>
				';
			}
			$content.='
				<form action="'.$this->linkThisCmd().'" method="post" name="'.$this->varPrefix.'_register">
				<p><input type="text" name="'.$this->piFieldName('regKey').'" value="'.htmlspecialchars($this->piData['regKey']).'" maxlength="20" /></p>
				<p><input type="checkbox" name="_" value="0" onClick="if (this.checked) {document.'.$this->varPrefix.'_register[\''.$this->piFieldName('regKey').'\'].value=\'test_'.$this->randomWordPrefix().'\';}">I just want to test...</p>
<!--				<p><input type="checkbox" name="_" value="0" onClick="if (this.checked) {document.'.$this->varPrefix.'_register[\''.$this->piFieldName('regKey').'\'].value=\'rnd'.$this->randomWordPrefix().'\';}">Pass me a random key...</p> -->
				<p><input type="submit" value="Evaluate key validity">'.$this->cmdHiddenField().'</p>
				</form>
			';
		}

		return '<DIV'.$this->pi_classParam('regkey').'>'.$content.'</DIV>';
	}

	/**
	 * Based on input in the piVar array, this input is stored in the keytable. So this function is used to register a new extension key.
	 *
	 * @return	[type]		...
	 */
	function registerKeyFromPIData()	{
		$dataArr=array(
			'title' => trim($this->piData['field_title']),
			'description' => trim($this->piData['field_description']),
			'extension_key' => trim($this->piData['regKey']),
			'extension_key_modules' => trim(str_replace('_','',$this->piData['regKey'])),
			'owner_fe_user' => $this->piData['owner_fe_user'],
			'members_only' => 1,
			'upload_password' => trim($this->piData['field_up'])
		);
		$q = $this->cObj->DBgetInsert('tx_extrep_keytable', $this->dbPageId, $dataArr, implode(',',array_keys($dataArr)));
		$res = mysql(TYPO3_db,$q);
	}

	/**
	 * Does its best to return a random word to us...
	 *
	 * @return	[type]		...
	 */
	function randomWordPrefix()	{
		$uId = uniqid(rand(),1);

		for($a=0;$a<40;$a++)	{
			$uString.=strtolower(chr(rand(65,65+25)));
		}

		$K_letters=ereg_replace('[eyuioa]','',$uString);
		$A_letters=ereg_replace('[^eyuioa]','',$uString);
		$letters=$uString;

		$str=$K_letters[0].$A_letters[1].$letters[2].$A_letters[3].$letters[4];
		return $str;
	}

	/**
	 * Validates the piData[regKey] content and returns an array with the status
	 *
	 * @return	[type]		...
	 */
	function validateSubmittedKey()	{
		if ($this->piData['regKey'])	{
			$extKey = trim($this->piData['regKey']);
			$res = $this->validateExtensionKey($extKey);
			if (is_array($res))	{
				$content.='<ul><li>'.implode('</li><li>',$res).'</li></ul>';
			} else {
				if (is_array($this->checkUniquenessOfKey($extKey)))	{
					$content.='<p>Error: Key was registered already!</p>';
				} else {
					$OK=1;
				}
			}
		}
		return array($OK,$content);
	}

	/**
	 * Input is a string and output is also a string if the input complies with the rules for extension keys. Otherwise it's an array with the error messages.
	 *
	 * @param	[type]		$extKey: ...
	 * @return	[type]		...
	 */
	function validateExtensionKey($extKey)	{
		$errors=array();
		$extKey_module = str_replace('_','',$extKey);
			// Check characters used:
		if (ereg('[^a-z0-9_]',$extKey,$reg))	{
			$errors[]="Extension keys cannot contain characters apart from a-z (lowercase), 0-9 and '_' (underscore)";
		}

			// Check characters used:
		if (ereg('^[0-9_]',$extKey,$reg) || ereg("[_]$",$extKey,$reg))	{
			$errors[]="Extension keys cannot start or end with 0-9 and '_' (underscore)";
		}

			// Length
		if (strlen($extKey)>30 || strlen($extKey)<3 || strlen($extKey_module)<3)	{
			$errors[]='Extension keys cannot be shorter than 3 and longer than 30 characters (and should be kepts as short as possible, although still meaningful)';
		}

			// Bad prefixes:
		$pList = 'tx,u,user_,pages,tt_,sys_,ts_language_,csh_';
		$badPre = explode(',',$pList);
		while(list(,$pref)=each($badPre))	{
			if ($pref && t3lib_div::isFirstPartOfStr($extKey,$pref))	{
				$errors[]="Prefixed with '".$pref."'. Extension keys cannot be prefixed with any prefixes from this list: <em>".$pList.'</em>';
			}
		}

		return count($errors) ? $errors : $extKey;
	}

	/**
	 * Checks if the extension key is unique.
	 *
	 * @param	[type]		$extKey: ...
	 * @return	[type]		...
	 */
	function checkUniquenessOfKey($extKey)	{
		$extKey_module = str_replace('_','',$extKey);
		$query = 'SELECT uid FROM tx_extrep_keytable WHERE pid='.intval($this->dbPageId)." AND (extension_key='".addslashes($extKey)."' OR extension_key_modules='".addslashes($extKey_module)."')".$GLOBALS['TSFE']->sys_page->deleteClause('tx_extrep_keytable');
		$res = mysql(TYPO3_db,$query);
		if ($row=mysql_fetch_assoc($res))	{
			return $row;
		}
	}

	/**
	 * Getting link to this page + extra parameters, we have specified
	 *
	 * @param	[type]		$uPA: ...
	 * @return	[type]		...
	 */
	function linkThisCmd($uPA=array())	{
		$uP = t3lib_div::implodeArrayForUrl($this->varPrefix,array_merge(array('cmd'=>$this->currentCMD),$uPA));
		$url = $this->cObj->currentPageUrl($uP);
		return $url;
	}

	/**
	 * Returns name for form fields
	 *
	 * @param	[type]		$key: ...
	 * @return	[type]		...
	 */
	function piFieldName($key)	{
		return $this->varPrefix.'['.$key.']';
	}

	/**
	 * Get hidden field for "cmd"
	 *
	 * @return	[type]		...
	 */
	function cmdHiddenField()	{
		return '<input type="hidden"  name="'.$this->piFieldName('cmd').'" value="'.htmlspecialchars($this->currentCMD).'">';
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/extrep_mgm/pi1/class.tx_extrepmgm_registerextkeys.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/extrep_mgm/pi1/class.tx_extrepmgm_registerextkeys.php']);
}

?>
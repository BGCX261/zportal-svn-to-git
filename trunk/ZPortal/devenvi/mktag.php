<?php
/**
 * by Yuval kuck, Zend Technologies, 12/2007
 */
	$arOpt = getopt('hrt:cm:');
	if( !isset($arOpt['t']) || isset($arOpt['h']) ) {
		die( "usege: {$argv[0]} ". <<<EOF
<-t newTag prefix> [-r][-c][-m 'comment text']
-t	tag prefix (example: YUVAL_DEV_0.0.1_RC00 or ZEND_PORTAL_0.0.1_RC00)
-r	create revision. 
-c	create the tag.
-m 'text' add comments.

EOF
);
	}	
	$baseTag = $arOpt['t'];
	$baseTagName = $baseTag;
	// featch RC num, Version and base tag name (prefix)  
	if( (preg_match('/_(\d+\.\d+\.\d+)RC(\d+)/', $baseTag, $arMatch) == 1) ||
		(preg_match('/_(\d+_\d+_\d+)_RC(\d+)/', $baseTag, $arMatch) == 1))	{
		$ver = $arMatch[1];
		$rc = $arMatch[2];
		if( (preg_match('/^(.*)_\d+\.\d+\.\d+RC\d+/', $baseTag, $arMatch) == 1) || 
			(preg_match('/^(.*)_(\d+_\d+_\d+)_RC(\d+)/', $baseTag, $arMatch) == 1)) {
			$baseTagName = $arMatch[1];
		}		
	} else {
		$ver = '';
		$rc = '';	
	}
	$svnBasePath = getProjectRoot(); 
	if( $svnBasePath === false) {
		die( "can not find svn base path");
	}	
	echo "validate exitance of tag(s) ... \n";
	if( isset($arOpt['r'])) {
		$arTags = getTagsList($svnBasePath);		
		for( $rev = 1; $rev < 999; $rev++) {
			$tag = "{$baseTag}_".sprintf("%03d", $rev);
			foreach ($arTags as $exist) {
				if( preg_match("/^{$tag}/", $exist) == 1) {
					continue 2;
				}
			}
			break;
		}	
		$comment = "{$baseTagName} ver:{$ver} RC:{$rc} rev:{$rev}";
	} else {
		$tag = $baseTag;
		if( isTagExist($svnBasePath, $tag)) {
			die( "tag {$tag} alreay exist\n");
		}
		$comment = "{$baseTagName} ver:{$ver} RC:{$rc}";
	}
	$tag .= '_'.date("Ymd",time());
	if( isset($arOpt['m'])) {
		$comment .= "- {$arOpt['m']}";
	}
	$doCmd = "svn copy -m '{$comment}' {$svnBasePath}/trunk {$svnBasePath}/tags/{$tag}";
	
	if( isset($arOpt['c']) ) {
		echo "create tag: {$tag} comented:{$comment} ... ";
		exec($doCmd);
		echo "done\n";
	} else {
		echo "{$doCmd}\n";
	}

/**
 * Enter description here...
 *
 * @param string $urlRoot
 * @return array or FALSE
 */
function getTagsList($urlRoot) {
	unset($arOutput);	
	exec("svn ls {$urlRoot}/tags 2>/dev/null", $arOutput);	
	return (empty($arOutput) ? false : $arOutput);
}
	/**
	 * Enter description here...
	 *
	 * @param string $urlRoot
	 * @param string $name
	 * @return boolean
	 */
function isTagExist($urlRoot, $name) {
	$arOutput = getTagsList($urlRoot);
	while (list($idx, $row) = each($arOutput)) {
		if( preg_match("/^{$name}/", $row) == 1) {
			return true;
		}
	}
	return false;
}

/**
 * @return string or FALSE
 */
function getProjectRoot() {	
	exec("svn info .", $arOutput);
	$svnBasePath = '';  
	foreach ($arOutput as $row) {
		if( preg_match('/URL:[\s\t ]*([^\n \s\t]*)\/trunk/', $row, $arMatch) == 1) {
			$svnBasePath = $arMatch[1];
		}
	}	
	if( empty($svnBasePath)) {
		return false;
	}	
	return $svnBasePath;	
}


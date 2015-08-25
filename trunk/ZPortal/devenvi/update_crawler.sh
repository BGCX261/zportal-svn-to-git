#!/bin/bash
php=/usr/local/zend/bin/php
if [ ! -f ${php} ] ; then
	echo "can not find PHP at: ${php}, abort"
	exit
fi
if [ -d lucene ] ; then 
	cd lucene
fi
if [ -f crawler.php ] ; then
	echo -n "update ..."
	rm -fr index
	${php} crawler.php
	chmod -R 777 index
else 
	echo "can not find crawler.php, abort"
	exit
fi	
echo "done"

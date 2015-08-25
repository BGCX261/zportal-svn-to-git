#!/bin/bash
#
# by Yuval Kuck 04/2008, Core Team
#
root=https://il-cms1.zend.net/svn/Zend/InternalTools/ZendPortal
link=ZendPortal
rc=$1
user=svnread
pass=3303
svncmd="svn --username ${user} --password ${pass}"

if [ "$rc" == "" ] ; then
        echo "$0 <rc Number> (Example: 01)"
        exit
fi
if ! [ -h ./${link} ] ; then
        echo "${link} is not a link, script does not know to handle this situation, abort"
        exit
fi
echo "Featch tags ... "
tag=`${svncmd} ls ${root}/tags | grep RC${rc}`
if [ "${tag}" == "" ] ; then
        echo "Tag of RC${rc} not exist, abort"
        exit
fi
valid=`${svncmd} ls ${root}/tags | grep RC${rc} | wc -l`
if [ "${valid}" != "1" ] ; then
        echo "find more then one tag match, abort"
        exit
fi
if [ -d ./${tag} ] ; then
        echo "tag ${tag} already exist, abort"
        exit
fi
${svncmd} co ${root}/tags/${tag} ${tag}
if ! [ -d ./${tag} ] ; then
        echo "failed to featch tag ${tag}, abort"
        exit
fi
chmod 777 ${tag}cache
rm -f ${link}
if [ -h ./${link} ] ; then
        echo "failed to remove softlink ${link}, abort"
        exit
fi
ln -s ./${tag} ${link}
if ! [ -h ./${link} ] ; then
        echo "failed to create softlink ${link} to ./${tag}, abort"
        exit
fi
echo "upgrade to tag ${tag},done"


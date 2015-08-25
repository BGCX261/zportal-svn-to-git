ItemHandler = Class.create({
    initialize: function(baseUrl) {
        this.baseUrl = baseUrl;
    },
    onLoad: function() {
        var feeds = $$('div[id ^= "FEEDentries"]');
        for(var i = 0; i < feeds.length; ++i) {
            var feed = feeds[i];
            this.read(feed.id.replace('FEEDentries',''));
        }
    },
    expand: function(feedId, index) {
    	elementPlus = $('ft_' + feedId + '_' + index);
    	elementBox = $('ftl_' + feedId + '_' + index);
    	elementShow = $('fb_' + feedId + '_' + index);
    	
        if (elementShow.style.display=='block') {
        	elementBox.removeClassName('sftl');
        	elementBox.addClassName('uftl');
        	elementPlus.removeClassName('fminbox');
        	elementPlus.addClassName('fmaxbox');
        	elementShow.style.display='none';
        } else {
        	elementBox.removeClassName('uftl');
        	elementBox.addClassName('sftl');
        	elementPlus.removeClassName('fmaxbox');
        	elementPlus.addClassName('fminbox');
        	elementShow.style.display='block';        	
        }
    },
    read: function(feedId) {
        var url = this.baseUrl + "portal/read";
        new Ajax.Request(url, {
            onSuccess: function(transport) {
                $('FEEDentries' + feedId).innerHTML = transport.responseText;
                $('FEEDmsg' + feedId).hide();
                $('FEEDdisplay' + feedId).show();
            },
            parameters: { 'feedId' : feedId } }); 
    },
    
    del: function(feedId) {
        $('m_' + feedId).hide();    	
    	var url = this.baseUrl + "portal/unsubscribe";
        new Ajax.Request(url, {
            onSuccess: function(transport) {
            },
            parameters: { 'feedId' : feedId } }); 
    }
});

var baseUrl = '';
if (parseQuery) { // included from query.js
    var params = parseQuery();
    if (params['baseUrl']) {
        baseUrl = params['baseUrl'];
    }
}

var itemHandler = new ItemHandler(baseUrl);

window.onload = function() {
    itemHandler.onLoad();
}
function proposeMenu(element) {
	var element = $(element);
	element.style.display = element.style.display == 'none' ? 'block' : 'none';
}

var Subscribe = Class.create({
	Version :'2007-11-29',

    initialize: function(baseUrl) {
        this.baseUrl = baseUrl;
    },
    
    subscribe: function(feedId) {
    	var url = this.baseUrl + "portal/subscribe";
        new Ajax.Request(url, {
            onSuccess: function(transport) {
        		var stats = transport.responseText;
	    		if (stats == 'Added') {
	    			$('addbtn' + feedId).style.display = "none";
	    			$('added' + feedId).style.display = "block";
	    		} 
            },
            parameters: {'feedId' : feedId, 'subscribe' : 'true'}}); 
    },
    
	onload: function() {
    	
	}
});

var baseUrl = '';
if (parseQuery) { // included from query.js
    var params = parseQuery();
    if (params['baseUrl']) {
        baseUrl = params['baseUrl'];
    }
}

var subscribe = new Subscribe(baseUrl);

window.onload = function() {
	subscribe.onload();
}

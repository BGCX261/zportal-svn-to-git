/**
 * Zend Portal
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @author 
 */

var AjaxForm = {
	convert: function(form, options) {
		if (!form._originalAction) {
		    form._options = {
		    	onFailure: function(transport) {
		    		window.alert('AJAX submit failed. Using standard one.');
		    		transport.form.action = form._originalAction; 
		    		transport.form._originalAction = null;
		    		transport.form.submit();
		    	},
		    	onSuccess: function(transport) {
		    		alert(transport.responseText);
		    		if (response.redirect) {
		    			document.location(response.redirect);
		    		} else if (response.errors) {
		    			for (element in transport.form.elements) {
		    				if(responce.errors[element.name]) {
		    					element.style._backgroundColor = element._backgroundColor;
		    					element.style.backgroundColor = 'red';
		    				} else if(element.style._backgroundColor) {
	    						element.style.backgroundColor = element.style._backgroundColor;
	    						element.style._backgroundColor = null;
	    					}
		    			}
		    		}
		    	},
		    	parameters: {}
		    }
			Object.extend(form._options, options);
			form._originalAction = form.action || document.location.href;
			form.action = 'javascript:$("'+form.id+'").submitAjax()';
			form.submitAjax = function() {
				this._options.method = this.method ? this.method : 'get';
				this._options.parameters = {};
				for (i=0; i < this.elements.length; i++) {
					this._options.parameters[this.elements[i].name] = this.elements[i].value;
				}
				var request = new Ajax.Request(this._originalAction, this._options);
				request.form = this;
			}
		}
		return form;
	}
}
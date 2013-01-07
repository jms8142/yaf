/*
compile and minify
*/


function getUrlVars(val)
{
    var vars = [], hash,
    _urlString = (val !== undefined) ? val :  window.location.href;
    var hashes = _urlString.slice(_urlString.indexOf('?') + 1).split('&');
    for(var i = 0; i < hashes.length; i++)
    {
        hash = hashes[i].split('=');
        vars.push(hash[0]);
        vars[hash[0]] = hash[1];
    }
    return vars;
}


/**
* Similiar to above function, but returns an object and also decodes html entities
*/

function getTemplateURLVars(val)
{

    var vars = {}, hash, items = [],
    _urlString = (val !== undefined) ? val :  window.location.href,
    hashes = _urlString.slice(_urlString.indexOf('?') + 1).split('&'),
    pattern = /^items\[[0-9]\]\[\w+\]\=.+$/,
    reg = new RegExp(pattern);
    for(var i = 0; i < hashes.length; i++)
    {
        hashes[i] = decodeURIComponent(hashes[i]);
    	//un parse the items (from $.params())
    	
    	if(reg.test(hashes[i])){//temporary hardcoded deserialization
            //  console.info(hashes[i]);
      		var _index = hashes[i].charAt(6),
      		_key = hashes[i].substr(9,3);
      		_value = hashes[i].substr(14,hashes[i].length-14);
      		
            obj = new Object();
            obj.key = _key;
            obj.val = _value;
            if(items[_index] === undefined) {
                items[_index] = Array(obj);
            } else {
                items[_index].push(obj);
            }
    		
    	} else {    		
    		hash = hashes[i].split('=');
    	}
        
       	vars[hash[0]] = decodeURIComponent(hash[1]).replace(/\+/g,' ');
        
    }

    if(items.length>0){  //clean up nested values
        for(x=0;x<items.length;x++){
            var collect = {};
            for(y=0;y<items[x].length;y++){
                collect[items[x][y].key] = items[x][y].val;
            }
            items[x] = collect;
        }
        vars['items'] = items;
    }

    return vars;
}


/**
Generic Wrapper Function on top of jquery $.ajax
**/

function ajaxGateway(opts){
	if(typeof(jQuery === "function")){  //make sure jquery is here
			var requestType,
			url = "/api/?m=" + opts.method,
			data = opts.data
			datatype = (opts.datatype !== undefined) ? opts.datatype : 'json';
			requestType = (opts.requestType !== undefined) ? opts.requestType : 'POST';
			//console.info(data);
			$.ajax({
				url : url,
				dataType : datatype,
				data : data,
				type : requestType,
				success : function(resp){	

					if(opts.eventFire !== undefined){ //event to trigger
						if(opts.eventScope !== undefined ){ //if scope
							if(opts.args !== undefined){
								opts.eventScope.trigger(opts.eventFire,opts.args);
							} else {
								opts.eventScope.trigger(opts.eventFire);
							}
							
						} else {
							$(window).trigger(opts.eventFire);
						}
						
					}

					if(typeof(opts.onSuccess) === "object" || typeof(opts.onSuccess) === "function"){ //callback function exists
						opts.onSuccess(resp);
					}
				}
			});
	}
}
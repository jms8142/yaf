
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
						if(opts.args !=== undefined){
							opts.eventScope.trigger(opts.eventFire,opts.args);
						} else {
							opts.eventScope.trigger(opts.eventFire);
						}
						
					} else {
						$(window).trigger(opts.eventFire);
					}
					
				}

				if(typeof(opts.onSuccess) === "object"){ //callback function exists
					opts.onSuccess(resp);
				}
			}
		});

	}
	
}
}

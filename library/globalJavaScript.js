




//가이드 앱 푸쉬알람
function sandPushMassage(mode,target,title,content,link){
	var apiUserId = "wepix.design@gmail.com"; 
	var apiKey = "fc547c63-efe2-11e9-aad3-0a86e61dd930"; 
	var appId = "f08cec4b-03c2-4cdb-b7b2-b40dd4d3d0af"; 

	var messageJson = '{ "messageTitle" : "'+title+'" , "messageContent" : "'+content+'" , "messageLinkUrl" : "'+link+'" }';

	if(mode == 'ALL'){
		var sendTargetList = '-1'; 
		var sendTargetTypeList = "ALL_TARGET"; 
	}else if(mode == 'MEMBER'){
		var sendTargetList = target; 
		var sendTargetTypeList = "MEMBER"; 
	}


		$.ajax({
			url: "http://www.swing2app.co.kr/swapi/push_send",
			type: "post",
			dataType: "json",
			data : {
				app_id : appId,
				send_target_list : sendTargetList,
				send_target_type_list : sendTargetTypeList,
				send_type : 'push' ,
				message_json : messageJson,
				api_user : apiUserId,
				api_key : apiKey
				},success: function (model) {
					console.log("send push message"); 
				}
		 }); 
}
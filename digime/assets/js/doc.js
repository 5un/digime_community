$(document).ready(function(e){
	var $outputBox = $('#api-output');
	var $outputTimeElapsed = $('#output-time-elapsed');
	var $ulModule = $('#ul-modules');
	var $selModule = $('#select-module');
	var $selAction = $('#select-action');
	var $apiName = $('#api-data-name');
	var $apiMethod = $('#api-data-method');
	var $apiUrl = $('#api-data-url');
	var $apiModuleName = $('#api-data-module');
	var $apiModuleDescription = $('#api-module-description');
	var $apiActionDescription = $('#api-action-description');
	var $tabIntro = $('#tab0-page-content');
	var $tabAPIReference = $('#tab1-page-content');
	var $tableParamsDescription = $('#table-param-description');
	var $tableParams = $('#table-param-container');
	var $sendingStatus = $('#sending-status');
	var $txtSiteRoot = $('#txt-site-root');
	var $logBox = $('#log-box');
	
	var selectedModuleIndex = 0 ;
	var selectedActionIndex = 0;
	var actionMethod = 'GET';
	var actionURL = '';
	
	// jquery events
	
	$selModule.click(function(){
		selectedModuleIndex = parseInt($(this).find(":selected").val());
		showModuleData(selectedModuleIndex);
	});
	
	$selAction.click(function(){
		selectedActionIndex = parseInt($(this).find(":selected").val());
		showAPIInfo(selectedModuleIndex, selectedActionIndex);
	});
	
	$('#btn-intro').click(function(e){
		navigateToTab0();
	});
	
	$('#btn-apiref').click(function(e){
		navigateToTab1();
	});
	
	$('#btn-test-api').click(function(e){
		var callURL = $txtSiteRoot.val() + actionURL;
		var params = apiData[selectedModuleIndex].actions[selectedActionIndex].params;
		var formData = {};
		
		for(var i=0;i<params.length;i++){
			formData[params[i].name] = $('#txt-param-'+i).val();
		}
		
		// get the optional params
		$('.optional-param').each(function(index){
			$tdOptionalParamName = $(this).children('td.td-optional-param-name');
			$tdOptionalParamValue = $(this).children('td.td-optional-param-value');
			
			$txtOptionalParamName = $tdOptionalParamName.children('input');
			$txtOptionalParamValue = $tdOptionalParamValue.children('input');
			
			optionalParamName = $txtOptionalParamName.val();
			optionalParamValue = $txtOptionalParamValue.val();
			if(optionalParamName!=''){
				formData[optionalParamName] = optionalParamValue;
			}
		});
		
		$outputBox.html("loading");
		var startTime = Date.now();
		if(actionMethod=='GET'){
			log('GET request to ' + callURL + ' with Data:' + JSON.stringify(formData));
			$.get(callURL, formData,function(data){
				var stopTime = Date.now();
				var timeElapsed = stopTime- startTime;
				log('GET has responsed after ' + timeElapsed + ' ms');
				$outputTimeElapsed.html('Time Elapsed: ' + timeElapsed + ' ms');
				setOutput(data);
			});
		}else if(actionMethod=='POST'){
			log('POST request to ' + callURL + ' with Data:' + JSON.stringify(formData));
			$.post(callURL, formData, function(data){
				var stopTime = Date.now();
				var timeElapsed = stopTime- startTime;
				log('POST has responsed after ' + timeElapsed  + ' ms');
				$outputTimeElapsed.html('Time Elapsed: ' + timeElapsed + ' ms');
				setOutput(data);
			});
		}
	});
	
	$('#btn-add-more-param').click(function(e){
		var $rowData = $('<tr class="optional-param"></tr>');
		var $cellParamName = $('<td class="td-optional-param-name"></td>');
		var $textParamName = $('<input type="text" class="optional-param-name" value=""/>');
		$cellParamName.html($textParamName);
		var $textInput = $('<input type="text" class="optional-param-value" value=""/>');
		var $cellParamInput = $('<td class="td-optional-param-value"></td>');
		$cellParamInput.html($textInput);
		$rowData.append($cellParamName);
		$rowData.append($cellParamInput);
		$tableParams.append($rowData);
	});
	
	// view functions
	
	function showModuleData(moduleIndex){
		var actions = apiData[moduleIndex].actions;
		$selAction.html('');
		for(var i=0;i<actions.length;i++){
			$option = $('<option value="'+i+'"></option>');
			$option.html(actions[i].name);
			$selAction.append($option);
		}
		$apiModuleName.html('Module: '+apiData[moduleIndex].module);
		$apiModuleDescription.html(apiData[moduleIndex].description);
		selectedActionIndex = 0;
		showAPIInfo(moduleIndex,0);
	}
	
	function showAPIInfo(moduleIndex,actionIndex){
		var action = apiData[moduleIndex].actions[actionIndex];
		$apiName.html('Action: ' + action.name);
		$apiMethod.html(action.method);
		actionMethod = action.method;
		$apiUrl.html(action.url);
		actionURL = action.url;
		$apiActionDescription.html(action.desc);
		
		$tableParams.html('');
		var params = apiData[moduleIndex].actions[actionIndex].params;
		for(var i=0;i<params.length;i++){
			var $rowData = $('<tr></tr>');
			var $cellParamName = $('<td></td>');
			$cellParamName.html(params[i].name);
			var $textInput = $('<input type="text" id="txt-param-'+i+'" value=""/>');
			var $cellParamInput = $('<td></td>');
			$cellParamInput.html($textInput);
			$rowData.append($cellParamName);
			$rowData.append($cellParamInput);
			$tableParams.append($rowData);
		}
		
		$tableParamsDescription.html('');
		for(var j=0;j<params.length;j++){
			var $rowDataDesc = $('<tr></tr>');
			var $cellParamNameDesc = $('<td></td>');
			$cellParamNameDesc.html(params[j].name);
			var $cellParamDesc = $('<td></td>');
			$cellParamDesc.html(params[j].desc);
			$rowDataDesc.append($cellParamNameDesc);
			$rowDataDesc.append($cellParamDesc);
			$tableParamsDescription.append($rowDataDesc);
		}
	}
	
	function setOutput(msg){ $outputBox.html(msg); }
	function appendOutput(msg){ $outputBox.append(msg); }
	function appendLnOutput(msg){ $outputBox.append(msg + '<br>'); }
	function log(msg){ $logBox.append(msg + '<br>');}
	function initData(){
		//$option = $('<option value=""></option>');
		$selModule.html('');
		for(var i=0;i<apiData.length;i++){
			$option = $('<option value="'+i+'"></option>');
			$option.html(apiData[i].module);
			$selModule.append($option);
			$link = $('<button id="link-modules-'+i+'" class="link-modules"></button>');
			$link.data('moduleId',i);
			$link.html(apiData[i].module);
			$listItem = $('<li></li>');
			$listItem.html($link);
			$ulModule.append($listItem);
			$('#link-modules-'+i).bind('click',function(e){
				navigateToTab1();
				selectedModuleIndex =  $(this).data('moduleId');
				showModuleData($(this).data('moduleId'));
			});
		}
	}
	
	function navigateToTab0(){
		$tabIntro.show('slow');
		$tabAPIReference.hide('slow');
	}
	
	function navigateToTab1(){
		$tabIntro.hide('slow');
		$tabAPIReference.show('slow');
	}
	
	//init
	$tabAPIReference.hide();
	
	initData();
	setOutput('ready');
});

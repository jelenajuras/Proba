<!DOCTYPE html>
<head>
    <meta http-equiv="Content-type" content="text/html; charset=utf-8">
 
    <script src="https://cdn.dhtmlx.com/gantt/edge/dhtmlxgantt.js"></script>
    <link href="https://cdn.dhtmlx.com/gantt/edge/dhtmlxgantt.css" rel="stylesheet">
 
    <style type="text/css">
        html {
            height:100%;
            padding:0px;
            margin:0px;
        }
		body { 
			padding:20px;
			overflow: scroll;
		}
		.weekend{
			background: #F0DFE5 !important;
		}
		.updColor{
			background-color:#ffeb8a!important;
		}
	</style>
</head>
<body>
	<h1>Kalendar</h1>
	
	<div id="gantt_here" style='width:100%; height:100%;'></div>
	<script type="text/javascript">
		gantt.config.columns=[
			{name:"text",	label:"Naziv zadatka", align: "center" },
			{name:"start_date", label:"Početak", align: "center" },
			{name:"duration",   label:"Trajanje",   align: "center" },
			{name:"add",        label:"" }
		];
				
		gantt.config.lightbox.sections = [
			{name:"description", height:38, map_to:"text", type:"textarea", focus:true},
			{name:"priority", height:22, map_to:"priority", type:"select", options: [ 
				{key:1, label: "High"},                                               
				{key:2, label: "Normal"},                                             
				{key:3, label: "Low"}                                                 
			 ]},                                                                      
			{name:"time", height:72, type:"duration", map_to:"auto"}
		];
	 
		gantt.locale.labels.section_priority = "Priority";
		
		gantt.config.time_picker = "%H:%s";
		gantt.config.time_step = 15;

		gantt.config.xml_date = "%Y-%m-%d %H:%i:%s";
		gantt.attachEvent("onBeforeGanttRender", function(){
		var range = gantt.getSubtaskDates();
		var scaleUnit = gantt.getState().scale_unit;
		
		var d = new Date();
		gantt.config.start_date = new Date(d);
		if(range.start_date && range.end_date){
		 gantt.config.start_date = gantt.calculateEndDate(gantt.config.start_date, 0, scaleUnit);
		 gantt.config.end_date = gantt.calculateEndDate(range.end_date, 2, scaleUnit);
		}
		});
		var daysStyle = function(date){
			var dateToStr = gantt.date.date_to_str("%D");
			if (dateToStr(date) == "Sun"||dateToStr(date) == "Sat")  return "weekend";
		 
			return "";
		};
		gantt.config.scale_unit = "month";
		gantt.config.date_scale = "%m - %Y";
	 
		gantt.config.subscales = [
			{unit:"week", step:1, date:"%W week"},
			{unit:"day", step:1, date:"%d (%D)",css:daysStyle }
		];
		gantt.config.scale_height = 70;

		gantt.init("gantt_here");

		gantt.load("/api/data");
		 
		var dp = new gantt.dataProcessor("/api");
		dp.init(gantt);
		dp.setTransactionMode("REST");
		
		gantt.config.order_branch = true;
		gantt.config.order_branch_free = true;
		gantt.config.autosize = true;
		
		gantt.init("gantt_here");
		
	</script>
</body>
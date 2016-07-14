<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>dialog demo</title>
  <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
  <script src="//code.jquery.com/jquery-1.10.2.js"></script>
  <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
</head>
<body>
 
<div id="dialog" title="Newswire Search">Please enter stock ticker</div>
 
<script type="text/javascript">
  
 
  $(document).ready(function() 
{
   	
    //init dialog
    $("#dialog" ).dialog({ height: 110,autoOpen: false });
    
    $("#nw_btnsearch").click(function() {
      console.log("clicked: %o", this);
      var str = $("#nw_txtsearch").val();
	if (str==''){ 
		$("#dialog" ).dialog( "open" );
	} 
	else {
	console.log('Search str: ' + str);

	//Check current page is correct. ** Needs to be changed for production **
	var url = "http://localhost/wordpress/?page_id=5197&nw_name=" + str;

   	if(window.location.href.indexOf("5197") > -1) {
       console.log("You are on the correct page!");
    	}
	else {console.log("Page refresh required.");
		 setTimeout(function(){
		window.location = url;
         	}, 1000);
	}
 

	//call dbinfo.php and load results
	$("#nw_tabledata" ).load("/dbinfo.php?name="+str, function() {
  	console.log( "Load was performed." );
	});

	}

	
    }); // button click
  
}); // document.ready

</script>
 
</body>
</html>

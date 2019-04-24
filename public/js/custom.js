(function(){
  var rootURL = "http://122.160.36.7/symfony_api/"
  $(document).ready(function(){
/*
* Submitting data of new content form
*/
    $("#form_newcontent").submit(function(){
      event.preventDefault();
      var thisform = $(this);
      var contenttitle = thisform.find("#contenttitle").val();
      var contentdescription = thisform.find("#contentdescription").val();
      var contentemail = thisform.find("#contentemail").val();
      var contentdata = thisform.find("#contentdata").val();
/*
* calling API to add content
*/

		$.post(rootURL+"addcontent.php", { 
			contenttitle: contenttitle,
			contentdescription : contentdescription,
			contentemail : contentemail,
			contentdata : contentdata
		}, function(data) {
            if(JSON.stringify(data) != null) { 
               if(data.status==1){
                //added successfully
	               	document.getElementById("form_newcontent").reset();
               		displayMessage("success", data.message);
                } else if(data.message != "" || data.message != undefined){
                //failed to add content
               		displayMessage("error", data.message);
                } else {
                //invalid response from API
               		displayMessage("error", "Failed");
                 }
            } 
        });      
    });
  });

  const allcontents = document.getElementById('allcontents');
  if (allcontents) {
/*
* calling API for all contents
*/
		$.post(rootURL+"getcontent.php", { 
			function: "getcontent"
		}, function(data) {
            if(JSON.stringify(data) != null) { 
               if(data.status==1){
                //data fetched successfully
           		// displayMessage("success", data.message);
               	$.each(data.api_result, function(i, cdata) {
               		var row = "<tr>";
               		row += "<td>"+cdata.email+"</td>";
               		row += "<td>"+cdata.title+"</td>";
               		row += "<td>"+cdata.description+"</td>";
               		row += "<td>"+cdata.time+"</td>";
               		if(cdata.state == 1){
	               		row += "<td>Approved</td>";
	               		row += "<td></td>";
               		} else if(cdata.state == 2){
	               		row += "<td>Rejected</td>";
	               		row += "<td></td>";
               		} else {
	               		row += "<td>Pending</td>";
	               		row += "<td><div class='btn-group' role='group'><button type='button' class='btn btn-danger btn-contentstate' data-id='"+cdata.id+"' data-val='1'>Approve</button><button type='button' class='btn btn-warning btn-contentstate'  data-id='"+cdata.id+"' data-val='2'>Reject</button></div></td>";
               		}
	            	// $('#country_group_id').append($('<option></option>').val(p.countrycode).html(p.countryname));
                	$("#allcontents").find("tbody").append(row);
	            });
                } else if(data.message != "" || data.message != undefined){
                //No content
                	$("#allcontents").find("tbody").append('<tr><td colspan="4" class="text-center">Record not available</td></tr>');
               		displayMessage("error", data.message);
                } else {
                //API failed
                	$("#allcontents").find("tbody").append('<tr><td colspan="4" class="text-center">Record not available</td></tr>');
               		displayMessage("error", "Failed");
                 }
            }  

        });
  allcontents.addEventListener('click', e => {
/*
* calling API for approve/reject content
*/
    if (e.target.className === 'btn btn-danger btn-contentstate' || e.target.className === 'btn btn-warning btn-contentstate') {
      if (confirm('Are you sure?')) {
        const contentid = e.target.getAttribute('data-id');
        const newstate = e.target.getAttribute('data-val');
        // alert(id+"-"+newstate);
        $.post(rootURL+"updatecontentstate.php", { 
          contentid: contentid,
          state: newstate
        }, function(data) {
          if(JSON.stringify(data) != null) { 
            if(data.status==1){
              // status updated
              displayMessage("success", data.message);
              window.location.reload();
            } else {
              // failed to update status
              displayMessage("error", data.message);
            }
          } else {
              // API failed
            displayMessage("error", "Failed");
          }

        });
      }
    }
  });
}


})();
/*
* Displayes error/success messages
*/
function displayMessage(status, message){

	var finalmessage = message;
	if(status == 'error'){
    // error message display formate
		finalmessage = '<div class="alert alert-dismissible alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>'+message+'</div>';
	} else if(status == 'success'){
    // success message display formate
		finalmessage = '<div class="alert alert-dismissible alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>'+message+'</div>';
	} else {

	}
  // display message and removde after 3 second
   	$(".error-container").html(finalmessage).fadeIn().delay(3000).fadeOut();

}
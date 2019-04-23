
$(document).ready(function(){

    function display(){

        var action = "display";

        $.ajax({
            url : "RandomData.php",
            method : "POST",
            data : {action:action },
            success:function(data){
                
                $("#result").html(data);
            }

        });
    }

    $(document).on('click','#play',function(){
             
        display();
    });

    $("#export_all").on("click", function(){   

        $.ajax({
            url : 'ExportAll.php',
            method  : 'POST', 
            success : function(data){
                window.location = 'ExportAll.php';
            }

        });

    });
      
});

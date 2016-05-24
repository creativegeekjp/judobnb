/*
check_departure();

//oneday in millisecond 86400000
function check_departure(){
    
}
*/

jQuery(function($) {
    
    check_departure();
    
    function check_departure(){
        setInterval(function(){ 
            var today=new Date();
            
            
            
            $.ajax({
                type:'GET',
                data:{
                    action:'get_departures'
                },
                url:ajax_object.ajaxurl,
                success:function(response){
                    console.log(response);
                },
                complete:function(){
                    
                }
            });
            
        }, 3000);
    }
    
});
 document.observe("dom:loaded", function() {
        // initially hide all containers for payment details
        $('errormsg').hide();
        if (document.getElementById('bank_pay').checked) {
            $$('#paypal_id').invoke('hide');
            
        } else {
            $$('#bank_payment').invoke('hide'); 
        }

    });
 
 function validateFileExtension(fld) {
     if (!/(\.bmp|\.png|\.gif|\.jpg|\.jpeg)$/i.test(fld.value)) {
         fld.form.reset();
         fld.focus();
         $('errormsg').show();
         document.getElementById("errormsg").innerHTML = "Invalid file format";
         return false;
     }
     return true;
 }
 
 function updateShowProfileLink(){
	    if(document.getElementById("show_profile_link").checked){
	    document.getElementById("show_profile_link").value = 1;
	    }else{
	    document.getElementById("show_profile_link").value = 0;
	    }	
	    }
 
 function payment() {
     if (document.getElementById('bank_pay').checked) {
         document.getElementById('bank_payment').show();            
         document.getElementById('paypal_id').hide();
        if(document.getElementById('advice-required-entry-paypal_id')){
         document.getElementById('advice-required-entry-paypal_id').hide();
         }
     } else if (document.getElementById('paypal_pay').checked) {
    	 
         document.getElementById('paypal_id').show();
         document.getElementById('bank_payment').hide();
         
         if(document.getElementById('advice-required-entry-bank_payment')){
             document.getElementById('advice-required-entry-bank_payment').hide();
         }
     } 
 }
 
 var $jq=jQuery.noConflict();

 $jq( document ).ready(function() {
 	 var selected = $('shipping_cost').select("option[selected]")[0].innerHTML;
 	 if (selected == 'Shipping Cost') {
          $('shipping_price').setStyle('display:block');
      } else
          $('shipping_price').setStyle('display:none');
 });
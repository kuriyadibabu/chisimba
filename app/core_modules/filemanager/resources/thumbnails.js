//This is to help withthe visual effects on Filemanager's Thumbnail view and also the layout
jQuery(document).ready(function(){
   var fileLink = jQuery('.fileLink');
   var fileDetails = jQuery('.filedetails');
   var index = 0;
   jQuery(fileLink).hover(function(event){
       event.preventDefault()
       jQuery(fileDetails).css('width',jQuery(this).css('width'))
       jQuery(fileDetails).css('height',jQuery(this).css('height'))
   })
});
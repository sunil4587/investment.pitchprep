jQuery(document).ready(function () {
    var text = jQuery("#idsCustomFlashMsg").find('p').html();
   if(text === 'Your submission was successfully saved'){
        jQuery("#idsCustomFlashMsg").delay(6000).fadeOut("slow");
        window.history.replaceState('', 'Account','https://investment.pitchprep.co.uk/account');
    }
})
$(document).ready(function(){

    $("#role").on("change", function() {

        let roleVal = $(this).val();
        $('.role-assets').hide();
        $("#"+roleVal+"-asset").show();


    })

    $("#role").change();


})
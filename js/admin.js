//Admin JS

$("#addDomain").click(function(){
    $("#addDomain").before('<input class="inner-fields" type="text" name="domain[]" placeholder="Enter Domain">');
});

$("#addForbidden").click(function(){
    $("#addForbidden").before('<input class="inner-fields" type="text" name="forbidemail[]" placeholder="Enter Forbiden TMail">');
});

$("#addLinks").click(function(){
    $("#addLinks").before('<input class="small-inner-fields" type="text" name="linksIcon[]" placeholder="Enter Icon"><input class="small-inner-fields" type="text" name="linksTitle[]" placeholder="Enter Title"><input class="big-inner-fields" type="text" name="linksValue[]" placeholder="Enter Link">');
});
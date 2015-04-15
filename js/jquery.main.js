jQuery(document).ready(function(){
    jQuery("#calculate").click(function(e){
        var total = 0;
        jQuery(".pure-u-1-8").each(function(){
            var num = parseFloat(this.value).toFixed(2);
            var numValue = parseInt(jQuery(this).attr("data-value"));
            if(!isNaN(num)) {
                total += num * numValue;
            }
        });

        jQuery("#total").text(total);
        jQuery("#total_value").val(total);
        e.preventDefault();
    });
});

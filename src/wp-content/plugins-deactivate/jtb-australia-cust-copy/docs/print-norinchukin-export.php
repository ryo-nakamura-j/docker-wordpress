<?php

$array="ID,Time,Email Address,RSVP\\n";

if ( ! post_password_required() ) {
    global $wpdb;
    $results = $wpdb->get_results( 'SELECT * FROM wp_jtbau_mice_rsvp', OBJECT );
    foreach ($results as $key => $value) {
    	$array=$array . $value->id . "," . date("Y-m-d H:i",$value->time) . "," . $value->email . "," . $value->rsvp . "\\n" ;
    }
}



?>

<script type="text/javascript">

function norinchukin_export(){
	var data2 = '<?php echo $array;?>';
	var blob = new Blob([data2], { type: 'text/csv;charset=utf-8;' });

    var link = document.createElement("a");
    if (link.download !== undefined) { // feature detection
        // Browsers that support HTML5 download attribute
        var url = URL.createObjectURL(blob);
        link.setAttribute("href", url);
        link.setAttribute("download", "norinchukin_rsvp.csv");
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

}

</script>

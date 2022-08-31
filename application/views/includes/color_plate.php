<div class="color-palet">
    <?php
	if($model_info->color){
		$selected_color = $model_info->color;
	}
	else{
		if($_SESSION['team_type']=="Reception User Role"){
			$selected_color = "#4080CB";
		}
		else{
			$selected_color = "#4CAF6E";
		}
	}    
	if($_SESSION['team_type']=="Reception User Role"){
		$colors = array("#4080CB","#AAB7B7");		
	}
	else if($_SESSION['team_type']=="Marketing User Role"){
		$colors = array("#4CAF6E","#f1c40f");
	}
	else{
		$colors = array("#4CAF6E", "#29c2c2", "#4080CB", "#aab7b7", "#f1c40f", "#e18a00", "#e74c3c", "#d43480", "#ad159e", "#37b4e1", "#34495e", "#dbadff");
	}
    foreach ($colors as $color) {
        $active_class = "";
        if ($selected_color === $color) {
            $active_class = "active";
        }
        echo "<span style='background-color:" . $color . "' class='color-tag clickable mr15 " . $active_class . "' data-color='" . $color . "'></span>";
    }
    ?> 
    <input id="color" type="hidden" name="color" value="<?php echo $selected_color; ?>" />
</div>

<script type="text/javascript">
    $(document).ready(function () {

        $(".color-palet span").click(function () {
            $(".color-palet").find(".active").removeClass("active");
            $(this).addClass("active");
            $("#color").val($(this).attr("data-color"));
        });

    });
</script>
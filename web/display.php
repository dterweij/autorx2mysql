<?php
// Display stuff
?>
<script type="text/javascript">
	var timeleft = 15;
	var downloadTimer = setInterval(function(){
  	document.getElementById("countdown").innerHTML = timeleft;
  	timeleft -= 1;
  	if(timeleft <= 0){
    	clearInterval(downloadTimer);
    	document.getElementById("countdown").innerHTML = "0"
  	}
	}, 1000);
</script>

	<div id="display">
    <span>Update in <span id="countdown">15 </span> Seconds</span>
    </div>

    <div id="display">
    <h2><span class="title">Last Sonde</span></h2>
    <span><?php TableLastSonde(1); ?></span>
    </div>
    
    <div id="display">
    <h2><span class="title">Latest 15 Sondes</span></h2>
    <span><?php TableLatestSondes(15); ?></span>
    </div>
        
    <!-- Alt -->
    <div id="display">
    <h2><span class="title">Max Alt - Last Seen</span></h2>
    <span><?php MaxAlt(); ?></span>
    </div>
    
    <div id="display">
    <h2><span class="title">Min Alt - Last Seen</span></h2>
    <span><?php MinAlt(); ?></span>
    </div>
    
    <div id="display">
    <h2><span class="title">Max Alt - First Seen</span></h2>
    <span><?php FirstMaxAlt(); ?></span>
    </div>
    
    <div id="display">
    <h2><span class="title">Min Alt - First Seen</span></h2>
    <span><?php FirstMinAlt(); ?></span>
    </div>

    <!-- Distance -->
    <div id="display">
    <h2><span class="title">Max Distance - Last Seen</span></h2>
    <span><?php MaxDistance(); ?></span>
    </div>
    
    <div id="display">
    <h2><span class="title">Min Distance - Last Seen</span></h2>
    <span><?php MinDistance(); ?></span>
    </div>
    
    <div id="display">
    <h2><span class="title">Max Distance - First Seen</span></h2>
    <span><?php FirstMaxDistance(); ?></span>
    </div>
    
    <div id="display">
    <h2><span class="title">Min Distance - First Seen</span></h2>
    <span><?php FirstMinDistance(); ?></span>
    </div>    
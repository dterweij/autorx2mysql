<?php
// Display stuff
?>
<script type="text/javascript">
	var timeleft = 15;
	var downloadTimer = setInterval(function(){
  	document.getElementById("countdown").innerHTML = timeleft + " Seconds";
  	timeleft -= 1;
  	if(timeleft <= 0){
    	clearInterval(downloadTimer);
    	document.getElementById("countdown").innerHTML = "** UPDATING NOW **"
  	}
	}, 1000);
</script>

<div class="textbox">
  <div class="titel-box">Data Refresh</div>
  <div class="divTable myTable">
    <div class="divTableBody">
      <div class="divTableRow">
        <div class="divTableCell"><span>Update in <span id="countdown">15 </span></span></div>
      </div>
    </div>
  </div>
</div>
<div class="split">
  <?php CurrentSondes(); ?>
</div>
<div class="textbox">
  <div class="titel-box">Latest 15 Sondes</div>
  <?php TableLatestSondes(15); ?>
</div>
<div class="split">
  <div class="column">
    <div class="textbox">
      <div class="titel-box">Max Altitude</div>
      <?php MaxAlt(); ?>
      <?php FirstMaxAlt(); ?>
    </div>
    <div class="textbox">
      <div class="titel-box">Min Altitude</div>
      <?php MinAlt(); ?>
      <?php FirstMinAlt(); ?>
    </div>
    <div class="textbox">
      <div class="titel-box">Average Altitude</div>
      <?php AVGAltTable(); ?>
    </div>
  </div>
  <div class="column">
    <div class="textbox">
      <div class="titel-box">Max Distance</div>
      <?php MaxDistance(); ?>
      <?php FirstMaxDistance(); ?>
    </div>
    <div class="textbox">
      <div class="titel-box">Min Distance</div>
      <?php MinDistance(); ?>
      <?php FirstMinDistance(); ?>
    </div>
    <div class="textbox">
      <div class="titel-box">Average Distance from QTH</div>
      <?php AVGDistanceTable(); ?>
    </div>
  </div>
  <div class="column">
    <div class="textbox">
      <div class="titel-box">Nearby your QTH</div>
      <?php NearestTable(); ?>
    </div>
    <div class="textbox">
      <div class="titel-box">Frequency list</div>
      <?php Freq(); ?>
    </div>
    <div class="textbox">
      <div class="titel-box">Sonde Model</div>
      <?php Model(); ?>
    </div>
  </div>
  <div class="column">
    <div class="textbox">
      <div class="titel-box">Seen Sondes</div>
      <?php SeenSondes(); ?>
    </div>
    <div class="textbox">
      <div class="titel-box">Monthly Received (All stations)</div>
      <?php MonthlyAllRX(); ?>
    </div>
    <?php include('info-box.php'); ?>
    <div class="textbox">
      <div class="titel-box">Page Info</div>
      <div class="divTable myTable">
        <div class="divTableBody">
          <div class="divTableRow">
            <div class="divTableCell"><?php echo 'MySQL Data loaded in ' . timer() . ' seconds.'; ?></div>
          </div>
          <div class="divTableRow">
            <div class="divTableCell"><?php echo 'Peak memory usage: ',round(memory_get_peak_usage()/1048576, 2), 'MB'; ?></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

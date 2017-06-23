<?php
if(isset($_POST['submit'])){
  $date = $_POST['date'];
  $month = substr($date,4,3);
  switch ($month) {
    case 'Jan':
      $month=1;
      break;
    case 'Feb':
      $month=2;
      break;
    case 'Mar':
      $month=3;
      break;
    case 'Apr':
      $month=4;
      break;
    case 'May':
      $month=5;
      break;
    case 'Jun':
      $month=6;
      break;
    case 'Jul':
      $month=7;
      break;
    case 'Aug':
      $month=8;
      break;
    case 'Sep':
      $month=9;
      break;
    case 'Oct':
      $month=10;
      break;
    case 'Nov':
      $month=11;
      break;
    case 'Dec':
      $month=12;
      break;
    default:
      $month=0;
      break;
  }
  $day = substr($date,8,2);
  $year = substr($date,11,4);
}
?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link rel="stylesheet" href="/resources/demos/style.css">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
$( function() {
  $( "#datepicker" ).datepicker({
    showWeek: true,
    firstDay: 1,
		dayNames: [ "Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi" ],
		dayNamesMin: [ "Di", "Lu", "Ma", "Me", "Je", "Ve", "Sa" ],
		monthNames: [ "Janvier", "Fevrier", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Decembre" ],
    monthNamesShort: [ "Janvier", "Fevrier", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Decembre" ],
    weekHeader: "Sem",
    changeMonth: true,
    changeYear: true,
    yearRange: "2016:2020",
    showOtherMonths :true,
    selectOtherMonths: true,
    onSelect: function (dateText, inst) {
        var date = $( "#datepicker" ).datepicker( "getDate" );
        $("#div1").val(date);
    },
  });
});
</script>

<div id="datepicker"></div>
<form method='POST' action='<?php if(isset($_POST['submit'])){echo "./day.php?year=$year&month=$month&day=$day&area=$area";} ?>'>
  <input type='date' name='date' id="div1"/>
  <input type='submit' name='submit' />

</form>

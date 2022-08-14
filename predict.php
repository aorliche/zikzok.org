<style>
<?php include('css/predict.css'); ?>
</style>
<div id='pred'>
    <div style='text-align: center; margin-bottom: 20px; font-size: 16px; font-weight: bold;'>Predict this video's worthiness!</div>
    <div style='margin-left: 10px;'>How many views/likes will it receive?</div>
    <div id='pred-views'>
        <div style='width: 50px; display: inline-block;'>Views:</div>
<?php
    $preds_arr = array(10,100,1000,10000,100000);
    foreach ($preds_arr as $pred) {
        echo "<div class='pred-button' data-value='$pred'>$pred</div>";    
    }
?>
    </div>
    <div id='pred-likes'>
        <div style='width: 50px; display: inline-block;'>Likes:</div>
<?php
    $preds_arr = array(1,10,100,1000);
    foreach ($preds_arr as $pred) {
        echo "<div class='pred-button' data-value='$pred'>$pred</div>";    
    }
?>
    </div>
    <div style='text-align: center; margin-top: 20px;'>
        <button id='pred-submit'>Submit</button>
    </div>
</div>

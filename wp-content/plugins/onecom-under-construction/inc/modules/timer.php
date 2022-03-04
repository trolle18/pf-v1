<?php
ob_start();
?>
<div class="timer">
    <div>
        <span class="count-number" id="counter-day"></span>
        <div class="smalltext"><?php _e('Days', ONECOM_UC_TEXT_DOMAIN); ?></div>
    </div>
    <div>
        <span class="count-number" id="counter-hour"></span>
        <div class="smalltext"><?php _e('Hours', ONECOM_UC_TEXT_DOMAIN); ?></div>
    </div>
    <div>
        <span class="count-number" id="counter-minute"></span>
        <div class="smalltext"><?php _e('Minutes', ONECOM_UC_TEXT_DOMAIN); ?></div>
    </div>
    <div>
        <span class="count-number" id="counter-second"></span>
        <div class="smalltext"><?php _e('Seconds', ONECOM_UC_TEXT_DOMAIN); ?></div>
    </div>
    <p id="time-up"></p>
</div>
<?php

$html = ob_get_clean();

<?php
function validate_input($str) {
    if (empty($str)) {
        return false;
    }
    return trim($str);
}
function validateMobile($mobile) {
    // Regular expression for 10-digit Indian mobile number starting with 7, 8, or 9
    return preg_match('/^[6789]\d{9}$/', $mobile);
}
?>
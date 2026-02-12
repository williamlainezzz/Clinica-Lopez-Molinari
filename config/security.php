<?php

return [
    'password_expiry_days' => (int) env('PASSWORD_EXPIRY_DAYS', 60),
    'password_reminder_days' => (int) env('PASSWORD_REMINDER_DAYS', 15),
    'password_expiry_months_label' => (int) env('PASSWORD_EXPIRY_MONTHS_LABEL', 2),
];

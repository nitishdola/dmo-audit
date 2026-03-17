<?php

/**
 * config/audit_summary.php
 *
 * All WhatsApp recipient phone numbers are read from .env —
 * never hardcode numbers in this file or in source control.
 *
 * Phone number format: international format WITHOUT the + sign.
 * Examples:
 *   India (Assam) : 919876543210   (91 = country code, 9876543210 = number)
 *   Twilio sandbox: 14155238886    (US test number)
 *
 * .env entries needed (add these to your .env file):
 * ─────────────────────────────────────────────────────────────────────────
 *   WA_CEO_NAME="Dr. Rajiv Kumar"
 *   WA_CEO_PHONE=919XXXXXXXXX
 *
 *   WA_JT_CEO_NAME="Ms. Priya Sharma"
 *   WA_JT_CEO_PHONE=919XXXXXXXXX
 *
 *   WA_OPS_MGR_1_NAME="Mr. Arjun Das"
 *   WA_OPS_MGR_1_PHONE=919XXXXXXXXX
 *
 *   WA_OPS_MGR_2_NAME="Ms. Rekha Borah"
 *   WA_OPS_MGR_2_PHONE=919XXXXXXXXX
 *
 *   WA_OPS_MGR_3_NAME="Mr. Ranjit Kalita"
 *   WA_OPS_MGR_3_PHONE=919XXXXXXXXX
 * ─────────────────────────────────────────────────────────────────────────
 *
 * To add more recipients: add another block in .env + another entry in
 * the 'recipients' array below. No code changes needed.
 */

return [

    'recipients' => array_filter([

        // ── Chief Executive Officer ──────────────────────────────────────
        env('WA_CEO_PHONE') ? [
            'name'  => env('WA_CEO_NAME',  'CEO'),
            'phone' => env('WA_CEO_PHONE'),
            'role'  => 'ceo',
        ] : null,

        // ── Joint CEO ────────────────────────────────────────────────────
        env('WA_JT_CEO_PHONE') ? [
            'name'  => env('WA_JT_CEO_NAME',  'Joint CEO'),
            'phone' => env('WA_JT_CEO_PHONE'),
            'role'  => 'jt_ceo',
        ] : null,

        // ── Operations Managers (up to 5 slots, add more as needed) ──────
        env('WA_OPS_MGR_1_PHONE') ? [
            'name'  => env('WA_OPS_MGR_1_NAME',  'Operations Manager 1'),
            'phone' => env('WA_OPS_MGR_1_PHONE'),
            'role'  => 'operations_mgr',
        ] : null,

        env('WA_OPS_MGR_2_PHONE') ? [
            'name'  => env('WA_OPS_MGR_2_NAME',  'Operations Manager 2'),
            'phone' => env('WA_OPS_MGR_2_PHONE'),
            'role'  => 'operations_mgr',
        ] : null,

        env('WA_OPS_MGR_3_PHONE') ? [
            'name'  => env('WA_OPS_MGR_3_NAME',  'Operations Manager 3'),
            'phone' => env('WA_OPS_MGR_3_PHONE'),
            'role'  => 'operations_mgr',
        ] : null,

        env('WA_OPS_MGR_4_PHONE') ? [
            'name'  => env('WA_OPS_MGR_4_NAME',  'Operations Manager 4'),
            'phone' => env('WA_OPS_MGR_4_PHONE'),
            'role'  => 'operations_mgr',
        ] : null,

        env('WA_OPS_MGR_5_PHONE') ? [
            'name'  => env('WA_OPS_MGR_5_NAME',  'Operations Manager 5'),
            'phone' => env('WA_OPS_MGR_5_PHONE'),
            'role'  => 'operations_mgr',
        ] : null,

    ]),

];

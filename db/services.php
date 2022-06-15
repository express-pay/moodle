<?php
/**
 * External functions and service definitions for the ExpressPay payment gateway plugin.
 *
 * @package    paygw_expresspay
 * @copyright  LLC TriIncom <info@express-pay.by>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = [
    'paygw_expresspay_create_payment' => [
        'classname'   => 'paygw_expresspay\external\create_payment',
        'methodname'  => 'create_payment',
        'classpath'   => '',
        'description' => 'Create a payment',
        'type'        => 'write',
        'ajax'        => true,
    ],
];

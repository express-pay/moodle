<?php
/**
 * Upgrade script for paygw_expresspay.
 *
 * @package    paygw_expresspay
 * @copyright  LLC TriIncom <info@express-pay.by>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade the plugin.
 *
 * @param int $oldversion the version we are upgrading from
 * @return bool always true
 */
function xmldb_paygw_expresspay_upgrade(int $oldversion): bool {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2021052501) {
        // Define key paymentid (foreign-unique) to be added to paygw_expresspay.
        $table = new xmldb_table('paygw_expresspay');
        $key = new xmldb_key('paymentid', XMLDB_KEY_FOREIGN_UNIQUE, ['paymentid'], 'payments', ['id']);

        // Launch add key paymentid.
        $dbman->add_key($table, $key);

        // Expresspay savepoint reached.
        upgrade_plugin_savepoint(true, 2021052501, 'paygw', 'expresspay');
    }

    // Automatically generated Moodle v4.0.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}

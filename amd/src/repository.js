/**
 * ExpressPay repository module to encapsulate all of the AJAX requests that can be sent for ExpressPay.
 *
 * @module     paygw_expresspay/repository
 * @copyright  LLC TriIncom <info@express-pay.by>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Ajax from 'core/ajax';

/**
 * Create a payment at Mollie.
 *
 * @param {string} component
 * @param {string} paymentArea
 * @param {number} itemId
 * @param {string} description
 * @returns {Promise<{shortname: string, name: string, description: String}[]>}
 */
export const createPayment = (component, paymentArea, itemId, description) => {
    let args = {
        component,
        paymentarea: paymentArea,
        itemid: itemId,
        description
    };
    const request = {
        methodname: 'paygw_expresspay_create_payment',
        args: args
    };

    return Ajax.call([request])[0];
};
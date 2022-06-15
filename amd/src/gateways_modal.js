/**
 * This module is responsible for ExpressPay content in the gateways modal.
 *
 * @module     paygw_expresspay/gateway_modal
 * @copyright  LLC TriIncom <info@express-pay.by>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import * as Repository from './repository';
import Templates from 'core/templates';
import ModalFactory from 'core/modal_factory';

/**
 * Creates and shows a modal that contains a placeholder.
 *
 * @returns {Promise<Modal>}
 */
const showModalWithPlaceholder = async () => {
    const modal = await ModalFactory.create({
        body: await Templates.render('paygw_expresspay/expresspay_button_placeholder', {})
    });
    modal.show();
    return modal;
};

/**
 * Process the payment.
 *
 * @param {string} component Name of the component that the itemId belongs to
 * @param {string} paymentArea The area of the component that the itemId belongs to
 * @param {number} itemId An internal identifier that is used by the component
 * @param {string} description Description of the payment
 * @returns {Promise<string>}
 */
 export const process = (component, paymentArea, itemId, description) => {
    return showModalWithPlaceholder()
        .then(modal => {
            return Repository.createPayment(component, paymentArea, itemId, description)
                .then(result => {
                    if (result.success) {
                        location.href = result.redirecturl;
                        // Return a promise that is never going to be resolved.
                        return new Promise(() => null);
                    }else {
                        return Promise.reject(result.message);
                    }
                }).catch(e => {
                    modal.hide();
                    return Promise.reject(e);
                });
        });
};

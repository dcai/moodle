// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Contain the logic for modal backdrops.
 *
 * @module     core/modal_backdrop
 * @copyright  2016 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import $ from 'jquery';
import * as Notification from './notification';
import * as Fullscreen from './fullscreen';

const SELECTORS = {
    ROOT: '[data-region="modal-backdrop"]',
};

export default class ModalBackdrop {
    root = null;
    isAttached = false;
    attachmentPoint = null;

    /**
     * Constructor for ModalBackdrop.
     *
     * @class core/modal_backdrop
     * @param {HTMLElement|jQuery} root The root element for the modal backdrop
     */
    constructor(root) {
        this.root = $(root);
        this.isAttached = false;
        this.attachmentPoint = document.createElement('div');
        document.body.append(this.attachmentPoint);

        if (!this.root.is(SELECTORS.ROOT)) {
            Notification.exception({message: 'Element is not a modal backdrop'});
        }
    }

    /**
     * Get the root element of this modal backdrop.
     *
     * @method getRoot
     * @return {object} jQuery object
     */
    getRoot() {
        return this.root;
    }

    /**
     * Gets the jQuery wrapped node that the Modal should be attached to.
     *
     * @returns {jQuery}
     */
    getAttachmentPoint() {
        const fullscreenElement = Fullscreen.getElement();

        if (fullscreenElement && fullscreenElement.tagName.toLowerCase() === 'html') {
            return $(this.attachmentPoint);
        }

        return $(fullscreenElement || this.attachmentPoint);
    }

    /**
     * Add the modal backdrop to the page, if it hasn't already been added.
     *
     * @method attachToDOM
     */
    attachToDOM() {
        this.getAttachmentPoint().append(this.root);

        if (this.isAttached) {
            return;
        }

        this.isAttached = true;
    }

    /**
     * Set the z-index value for this backdrop.
     *
     * @method setZIndex
     * @param {int} value The z-index value
     */
    setZIndex(value) {
        this.root.css('z-index', value);
    }

    /**
     * Check if this backdrop is visible.
     *
     * @method isVisible
     * @return {bool}
     */
    isVisible() {
        return this.root.hasClass('show');
    }

    /**
     * Check if this backdrop has CSS transitions applied.
     *
     * @method hasTransitions
     * @return {bool}
     */
    hasTransitions() {
        return this.getRoot().hasClass('fade');
    }

    /**
     * Display this backdrop. The backdrop will be attached to the DOM if it hasn't
     * already been.
     *
     * @method show
     */
    show() {
        if (this.isVisible()) {
            return;
        }

        this.attachToDOM();
        this.root.removeClass('hide').addClass('show');
    }

    /**
     * Hide this backdrop.
     *
     * @method hide
     */
    hide() {
        if (!this.isVisible()) {
            return;
        }

        if (this.hasTransitions()) {
            // Wait for CSS transitions to complete before hiding the element.
            this.getRoot().one('transitionend webkitTransitionEnd oTransitionEnd', () => {
                this.getRoot().removeClass('show').addClass('hide');
            });
        } else {
            this.getRoot().removeClass('show').addClass('hide');
        }

        // Ensure the modal is moved onto the body node if it is still attached to the DOM.
        if ($(document.body).find(this.getRoot()).length) {
            $(document.body).append(this.getRoot());
        }
    }

    /**
     * Remove this backdrop from the DOM.
     *
     * @method destroy
     */
    destroy() {
        this.root.remove();
        this.attachmentPoint.remove();
    }
}

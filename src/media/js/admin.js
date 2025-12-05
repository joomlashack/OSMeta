/**
 * @package   OSMeta
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2023-2026 Joomlashack.com. All rights reserved
 * @license   https://www.gnu.org/licenses/gpl.html GNU/GPL
 *
 * This file is part of OSMeta.
 *
 * OSMeta is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * OSMeta is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OSMeta.  If not, see <https://www.gnu.org/licenses/>.
 */
(function($) {
    let hashChecks = [];

    $.fn.osmetaCharCount = function(args) {
        let options = $.extend({}, $.osmeta.options, args);

        return this.each(function() {
            $.osmeta.addCountContainer(this, options);
        });
    }

    $.osmeta = {
        options: {
            limit     : 100,
            message   : 'Text is too long',
            characters: {
                0: Joomla.JText._('COM_OSMETA_CHARS') || '%s',
                1: Joomla.JText._('COM_OSMETA_CHARS_1') || '%s'
            }
        },

        /**
         * @param {HTMLFormElement} element
         * @param {Object} options
         *
         * @return void
         */
        addCountContainer: function(element, options) {
            let that       = this,
                $element   = $(element),
                $container = $('<div class="char-count-container">');

            let $warning = $('<div class="char-count-warning">').text(options.message),
                $text    = $('<div class="char-count-counter-text">');

            $container
                .append($warning)
                .append($text);

            $element.data({
                'countDisplay': $container,
                'hash'        : that.getHash($element.val())
            });

            $element
                .after($container)
                .on('keyup', function() {
                    that.updateCount(element, options);
                })
                .trigger('keyup');

            hashChecks.push($element);
        },

        /**
         *
         * @param {HTMLFormElement} element
         * @param {Object} options
         *
         * @return void
         */
        updateCount: function(element, options) {
            let $display = $(element).data('countDisplay'),
                $warning = $('.char-count-warning', $display),
                $text    = $('.char-count-counter-text', $display),
                length   = element.value.length;

            if (length > options.limit) {
                $warning.show();
                length = options.limit - length;
                $text.addClass('invalid');
            } else {
                $warning.hide();
                $text.removeClass('invalid');
            }

            let countText = options.characters[Math.abs(length)] || options.characters[0];
            $text.html(countText.replace('%s', length));
        },

        /**
         * @param {String} string
         *
         * @returns {number}
         */
        getHash: function(string) {
            let hash = 0;

            for (let i = 0; i < string.length; i++) {
                hash = (((hash << 5) - hash) + string.charCodeAt(i)) | 0;
            }

            return hash;
        }
    }

    if (Joomla.submitform) {
        let nativeSubmit = Joomla.submitform;

        Joomla.submitform = function(task, form, validate) {
            if (task !== 'save') {
                for (let i = 0; i < hashChecks.length; i++) {
                    let $element = hashChecks[i];

                    if ($element.data('hash') !== $.osmeta.getHash($element.val())) {
                        if (confirm(Joomla.JText._('COM_OSMETA_CONFIRM_CANCEL')) === false) {
                            return;
                        }
                    }
                }
            }

            nativeSubmit(task, form, validate);
        }
    }
})(jQuery);

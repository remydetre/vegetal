/**
 * 2014 - 2019 Watt Is It
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons BY-ND 4.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://creativecommons.org/licenses/by-nd/4.0/fr/
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@paygreen.fr so we can send you a copy immediately.
 *
 * @author    PayGreen <contact@paygreen.fr>
 * @copyright 2014 - 2019 Watt Is It
 * @license   https://creativecommons.org/licenses/by-nd/4.0/fr/ Creative Commons BY-ND 4.0
 * @version   2.7.6
 */

jQuery(document).ready(function() {
    let categoryFilter = jQuery('#category-filter');

    categoryFilter.on('keyup', function(event) {
        let text = jQuery(event.target).val();
        filterCategories(text);
    });

    let text = categoryFilter.val();
    if (text !== '') {
        filterCategories(text);
    }

    jQuery('#categories input[type="checkbox"]').change(function (event) {
        let checkbox = jQuery(event.target);
        let category = checkbox.parents('tr');

        let paymentType = checkbox.val();
        let check = checkbox.prop('checked');

        checkChildren(category, category.data('depth'), paymentType, check)
    });

    function filterCategories(text) {
        let categories = jQuery('#categories tr');
        let regex = new RegExp(text,'i');

        /** @var Element category */
        categories.each(function (index, element) {
            let category = jQuery(element);

            if ((text === '') || (category.data('name').match(regex) !== null)) {
                category.show();

                if (text !== '') {
                    displayParents(category, category.data('depth'));
                }
            } else {
                category.hide();
            }
        });
    }

    function displayParents(element, depth) {
        if (depth > 0) {
            element = element.prev();
            if (element.data('depth') < depth) {
                element.show();
                depth = element.data('depth');
            }

            displayParents(element, depth);
        }
    }

    function checkChildren(element, depth, paymentType, check) {
        element = element.next();

        if (element.data('depth') > depth) {
            let checkbox = element.find('input[value="' + paymentType + '"]');
            checkbox.prop('checked', check);

            checkChildren(element, depth, paymentType, check);
        }
    }
});

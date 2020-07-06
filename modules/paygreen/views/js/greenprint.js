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

$(document).ready(function () {

    let fingerprintComputing = function () {
        var client = new ClientJS();
        var fingerprint = client.getFingerprint();
        var clientBrowser = client.getBrowser();
        var clientDevice = 'null';

        if (client.isMobile())
            clientDevice = 'mobile';
        else
            clientDevice = 'desktop';

        var start;
        var count = 0;

        $('img').each(function () {
            ++count;
        });

        $('script').each(function () {
            ++count;
        });

        start = new Date().getTime();

        $(window).on("unload", function () {
            var end = new Date().getTime();

            $.ajax({
                url: paygreen_tree_computing_url,
                type: 'post',
                async: false,
                data: {
                    client: fingerprint,
                    startAt: start,
                    useTime: (end - start),
                    nbImage: count,
                    device: clientDevice,
                    browser: clientBrowser
                },
                dataType: 'json',
                error: function (result) {
                    console.log("Paygreen fingerprint computing error.");
                }
            });
        });
    };

    fingerprintComputing();

    // var pathArray = location.href.split('/');
    // var protocol = pathArray[0];
    // var host = pathArray[2];
    // var baseUrlClient = protocol + '//' + host;
    // $.getScript(baseUrlClient + '/modules/paygreen/views/js/client.min.js', fingerprintComputing);
});

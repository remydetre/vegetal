{*
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
 *}

    <div class="panel">
        <div class="panel-heading">
            <i class="icon-image"></i> {l s='Paygreen Action' mod='paygreen'}
        </div>
        <div class="row" style="margin-top:-10px">
            <div class="col-lg-2 col-sm-6 col-md-6 col-xs-12">
                <div class="form-group">
                    <form class="form-horizontal center-block" action="#" method="post" enctype="multipart/form-data">
                        <label class="col-md-4 control-label" for="height">{l s='Display position' mod='paygreen'}</label>
                        <div class="col-lg-2 col-lg-2 col-md-2 col-md-1">
                            <button type="submit" value="1" id="module_form_submit_btn_hook" name="submitPaygreenModuleHook" class="btn btn-default center-block button">
                            {l s='BRING TO THE TOP' mod='paygreen'}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <span class="help-block">{l s='Set PayGreen in first position' mod='paygreen'}</span>
    </div>
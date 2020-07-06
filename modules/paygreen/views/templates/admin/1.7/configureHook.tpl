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
        <i class="icon-arrow-up"></i> {l s='Paygreen Action' mod='paygreen'}
    </div>

    <form class="ps_paygreen_fields ps_paygreen_hook" action="#" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label class="control-label ps_paygreen_label" for="height">
                {l s='Display position' mod='paygreen'}
            </label>

            <div class="ps_paygreen_input">
                <button type="submit" value="1" id="module_form_submit_btn_hook" name="submitPaygreenModuleHook" class="btn btn-default">
                    {l s='Bring to the top' mod='paygreen'}
                </button>
            </div>
        </div>

        <span class="help-block">
            {l s='Set PayGreen in first position' mod='paygreen'}
        </span>
    </form>    
</div>
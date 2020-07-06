{*
 * 2014 - 2020 Watt Is It
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
 * @copyright 2014 - 2020 Watt Is It
 * @license   https://creativecommons.org/licenses/by-nd/4.0/fr/ Creative Commons BY-ND 4.0
 * @version   3.0.1
 *}
<div class="pglayout">
    {view name="menu" selected="buttons"}
    {view name="notifications"}

    {include file="button/breadcrumb.tpl" currentPage="button.pages.update.title"}

    <div class="pgcontainer pgcontainer__align-center pg__mtop-sm">
        <h1 class="pg__mleft-page pg__mtop-xs pg__mbottom-xs">
            {$button['label']|escape:'htmlall':'UTF-8'}
        </h1>

        <a
            href="{'backoffice.buttons.delete'|toback:['id' => $button['id']]}"
            onclick="return confirm('{'button.actions.delete.confirmation'|pgtrans|escape:javascript}')"
            class="pgbutton pg__danger pg__mlateral-page pg__mtop-xs pg__mbottom-xs"
        >
            {'button.actions.delete.button'|pgtrans}
        </a>

        {if isset($errors)}
            {include
            file="../../../PGForm/_resources/templates/fields/partials/errors.tpl"
            errors=$errors
            class="pgform__errors--right"
            }
        {/if}
    </div>

    {$form}

    {view name="blocks" page="button-update"}
</div>

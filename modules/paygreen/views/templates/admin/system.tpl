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
        <i class="icon-bug"></i> {'system.title'|pgtrans}
    </div>

    <div class="pg-index-content">
        <div class="pg-content-column-2">
            <div class="pg-informations">
                <h3>{'system.platform.title'|pgtrans}</h3>
                <table>
                    <tr>
                        <th>{'system.platform.fields.platform'|pgtrans}</th>
                        <td>{$platforme} - {$version_platforme}</td>
                    </tr>
                    <tr>
                        <th>{'system.platform.fields.php'|pgtrans}</th>
                        <td>{$version_php}</td>
                    </tr>
                    <tr>
                        <th>{'system.platform.fields.module'|pgtrans}</th>
                        <td>{$version_module}</td>
                    </tr>
                    <tr>
                        <th>{'system.platform.fields.framework'|pgtrans}</th>
                        <td>{$version_framework}</td>
                    </tr>
                    <tr>
                        <th>{'system.platform.fields.curl'|pgtrans}</th>
                        <td>{$version_curl}</td>
                    </tr>
                    <tr>
                        <th>{'system.platform.fields.ssl'|pgtrans}</th>
                        <td>{$version_ssl}</td>
                    </tr>
                </table>
            </div>
            <div class="pg-informations">
                <h3>{'system.logs.title'|pgtrans}</h3>
                <table>
                    <thead>
                    <tr>
                        <td>{'system.logs.columns.filename'|pgtrans}</td>
                        <td>{'system.logs.columns.size'|pgtrans}</td>
                        <td>{'system.logs.columns.last_update'|pgtrans}</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    </thead>
                    <tbody>
                        {foreach from=$log_data key=key item=log}
                            <tr>
                                <td>{$log['name']}</td>
                                <td>{$log['size']}</td>
                                <td>{$log['updatedAt']}</td>
                                <td>
                                    {if $log['action'] }
                                    <form action="#" method="post">
                                        <input type="hidden" name="filename" value="{$log['name']}" />
                                        <button type="submit" name="downloadLogFile" class="btn btn-success pull-right">
                                            {'system.logs.actions.download.button'|pgtrans}
                                        </button>
                                    </form>
                                    {/if}
                                </td>
                                <td>
                                    {if $log['action'] }
                                    <form action="#" method="post">
                                        <input type="hidden" name="filename" value="{$log['name']}" />
                                        <button type="submit" name="deleteLogFile" class="btn btn-danger pull-right">
                                            {'system.logs.actions.delete.button'|pgtrans}
                                        </button>
                                    </form>
                                    {/if}
                                </td>
                            </tr>
                        {/foreach}
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
<style>
    .pg-index-content {
        display: flex;
        flex-direction: column;
    }

    .pg-index-content a{
        color: #33ad73;
        text-decoration: underline;
        font-weight: bold;
    }

    .pg-informations {
        margin-right: 30px;
    }

    .pg-informations h1, .pg-informations h2, .pg-informations h3, .pg-informations h4, .pg-informations h5, .pg-informations h6 {
        color: #33ad73;
        border: none !important;
        margin: 0 !important;
    }

    .pg-informations__content section{
        padding: 4px;
    }

    .pg-informations table{
        border-top: 2px solid #7A7A7A4D;
        border-collapse: collapse;
        background: #ffffff;
    }

    .pg-informations table tr:nth-child(even) {
        background: #F6F6F6;
    }

    .pg-informations table tr:nth-child(odd)  {
        background: #fff;
    }

    .pg-alert{
        border-left: 2px solid #db7413;
        font-weight: bold;
    }

    .pg-informations table tbody th{
        padding: 4px;
        color: #808080;
    }

    .pg-informations  table tbody td{
        padding: 4px;
        color: #4f4f4f;
    }

    .pg-informations  table thead td{
        font-weight: bold;
        padding: 4px;
    }

    .pg-content-column-2{
        display:flex;
        flex-direction:row;
    }
</style>

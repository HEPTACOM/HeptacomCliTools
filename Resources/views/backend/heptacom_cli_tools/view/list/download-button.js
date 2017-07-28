
//{block name="backend/plugin_manager/view/list/local_plugin_listing_page"}
// {$smarty.block.parent}

Ext.define('Shopware.apps.HeptacomCliTools.view.list.DownloadButton', {
    override: 'Shopware.apps.PluginManager.view.list.LocalPluginListingPage',

    createActionColumnItems: function () {
        var me = this, items = me.callParent(arguments);

        items.push({
            iconCls: 'sprite-drive-download',
            tooltip: '{s name="download"}Download{/s}',
            handler: function(grid, rowIndex, colIndex, item, eOpts, record) {
                window.location.href = window.location.href + 'HeptacomCliTools/pluginBuild?plugin=' + record.data.technicalName;
            },
            getClass: function(value, metaData, record) {
                if (record.data.source.length !== 0) {
                    return Ext.baseCSSPrefix + 'hidden';
                }
            }
        });

        return items;
    }
});

//{/block}

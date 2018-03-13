Ext.onReady(function () {
    if (Ext.get('js-grid-placeholder')) {
        var urlParam = intelli.urlVal('status');

        intelli.video =
            {
                columns: [
                    'selection',
                    {name: 'title', title: _t('title'), width: 1, editor: 'text'},

                    {name: 'date_added', title: _t('date_added'), width: 170, editor: 'date'},
                    {name: 'date_modified', title: _t('date_modified'), width: 170, hidden: true},

                    'status',
                    'update',
                    'delete'
                ],
                storeParams: urlParam ? {status: urlParam} : null,
            };

        intelli.video = new IntelliGrid(intelli.video, false);
        intelli.video.toolbar = Ext.create('Ext.Toolbar', {
            items: [
                {
                    emptyText: _t('text'),
                    name: 'text',
                    listeners: intelli.gridHelper.listener.specialKey,
                    xtype: 'textfield'
                },
                {
                    displayField: 'title',
                    editable: false,
                    emptyText: _t('status'),
                    id: 'fltStatus',
                    name: 'status',
                    store: intelli.video.stores.statuses,
                    typeAhead: true,
                    valueField: 'value',
                    xtype: 'combo'
                }, {
                    handler: function () {
                        intelli.gridHelper.search(intelli.video);
                    },
                    id: 'fltBtn',
                    text: '<i class="i-search"></i> ' + _t('search')
                }, {
                    handler: function () {
                        intelli.gridHelper.search(intelli.video, true);
                    },
                    text: '<i class="i-close"></i> ' + _t('reset')
                }
            ]
        });

        if (urlParam) {
            Ext.getCmp('fltStatus').setValue(urlParam);
        }

        intelli.video.init();
    }
});
Ext.Loader.setConfig({
    enabled: true
});

Ext.require([
    'Ext.grid.*',
    'Ext.data.*',
]);

Ext.onReady(function(){
    Ext.define('lab1', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'xreal', type: 'float'},
            {name: 'xint', type: 'int'},
            {name: 'xbin', type: 'string'},
            {name: 'eval_func', type: 'float'}
         ]
    });

    // Array data for the grids
    {% autoescape false %}
    Ext.grid.dummyData = {{ resultsInJson }};
    {% endautoescape %}

    var getLocalStore = function() {
        return Ext.create('Ext.data.ArrayStore', {
            model: 'lab1',
            data: Ext.grid.dummyData
        });
    };

    var grid3 = Ext.create('Ext.grid.Panel', {
        store: getLocalStore(),
        columns: [
            Ext.create('Ext.grid.RowNumberer'),
            {text: "x-real", width: 120, sortable: true, dataIndex: 'xreal'},
            {text: "x-int", width: 120, sortable: true, dataIndex: 'xint'},
            {text: "x-bin", width: 120, sortable: true, dataIndex: 'xbin'},
            {text: "f(x-real)", width: 120, sortable: true, dataIndex: 'eval_func'}
        ],
        columnLines: true,
        width:600,
        height:300,
        title:'Wartość populacji',
        iconCls:'icon-grid',
        renderTo: Ext.getBody()
    });
});

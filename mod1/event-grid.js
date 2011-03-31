Ext.onReady(function(){
		
		var page_size = 20;

		function renderData(data) {

				if(data=='') {
						return '';
				}

				var result = '{';
				for(d in data) {
						result += d+':'+data[d]+', ';
				}
				result += '}';
				return result;
		}

		// create the data store
		var store = new Ext.data.JsonStore({
				totalProperty: 'total',
				root: 'data',
				url: 'ajax.php?ajaxID=tx_aware_module1_ajax::getEvents',
				remoteSort: true,
				fields: [
						{name: 'uid', type: 'int'},
						{name: 'channel', type: 'string'},
						//{name: 'data', type: 'string'},
						'data'
				]
		});
		
		// load data from the url ( data.php )
		store.load({ params:{
				start: 0, 
				limit: page_size
		}});
		
		// create the Grid
		var grid = new Ext.grid.GridPanel({
				store: store,
				columns: [
						{id:'uid', header: 'ID', width: 50, sortable: true, dataIndex: 'uid'},
						{header: 'Channel', width: 200, sortable: true, dataIndex: 'channel'},
						{header: 'Data', width: 445, renderer: renderData, sortable: true, dataIndex: 'data'}
				],
				stripeRows: true,
				height:550,
				width:700,
				title:TYPO3.jslang.getLL('function1'),
				bbar: new Ext.PagingToolbar({
						pageSize: page_size,
						store: store,
						displayInfo: true,
						displayMsg: TYPO3.jslang.getLL('displaying_events') +' {0} - {1} ' + TYPO3.jslang.getLL('of') + ' {2}',
						emptyMsg: TYPO3.jslang.getLL('no_events')
				})
		});
		
		// render grid
		grid.render('event-grid');
		
});
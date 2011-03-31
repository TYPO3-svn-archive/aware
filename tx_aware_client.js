/***************************************************************
*  Copyright notice
*
*  (c) 2009 Morten Tranberg Hansen (mth at cs dot au dot dk)
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*  A copy is found in the textfile GPL.txt and important notices to the license
*  from the author is found in LICENSE.txt distributed with these scripts.
*
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * This is the frontend client for aware.
 *
 * @author Morten Tranberg Hansen <mth at cs dot au dot dk>
 * @date   November 10 2009
 */

Ext.namespace('Ext.ux.TYPO3');  

Ext.ux.TYPO3.tx_aware_client = Ext.extend(Ext.util.Observable, {
		
    locked: 0,
    interval: 5 * 1000,
		addlist: [],
				
    constructor: function(config) {
				
				config = config || {};
				Ext.apply(this, config);
				
        this.timerTask = {
            run: function(){

								var params = {
										'ajaxID': 'tx_aware_ajax::clientRequest'
								};

								var c = 0;
								for(var channel in this.events) {
										if(this.hasListener(channel)) {
												params['channels['+c+']'] = channel;
												c = c+1;
										}
								}
								
								for(var i=0; i<this.addlist.length; i++) {
										params['adds['+i+'][channel]'] = this.addlist[i].channel;
										for (var key in this.addlist[i].data) {
												params['adds['+i+'][data]['+key+']'] = this.addlist[i].data[key];
										}
								}
								this.addlist = [];
	
								Ext.Ajax.request({
										url: "ajax.php",
										params: params,
										method: "GET",
										
										success: function(response, options) {
												var result = Ext.util.JSON.decode(response.responseText);
												for(var i=0; i<result.length; i++) {
														this.fireEvent(result[i].channel, result[i].channel, result[i].timestamp, result[i].data);
												}
												
										},
										failure: function() {
												//alert('ERROR: tx_aware_client could not connect to the server.  Please inform you adminstrator.');
										},
										scope: this
								});
								
								
            },
            interval: this.interval,
            scope: this
        };

				this.startTimer();

				Ext.ux.TYPO3.tx_aware_client.superclass.constructor.call(this, config);
				
		},
		
		
    startTimer: function() {
        Ext.TaskMgr.start(this.timerTask);
    },

		addEvent: function(channel, data) {
				this.addlist[this.addlist.length] = {'channel':channel, 'data':data};
		}
	
});	


/**
 * Initialize object
 */
Ext.onReady(function() {
    TYPO3.tx_aware_client = new Ext.ux.TYPO3.tx_aware_client();
});

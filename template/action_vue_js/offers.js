var app = new Vue({
	el:'#crudApp',
	data:{	
		statusOffters: [
            { id: 'stop', name: 'Stop' },				
            { id: 'run', name: 'Running' },        	
        ],
		allData:'',    
		offer_title:'Offers Settings',	
		showModal:false,
		myModel:false,	
		tableId:0,
		hideMore: true,
		hideStatus: false,
		limit_hidden:10,
		totalCount:0,
		errors:[],
		ads:[],
		delete_title: "Xóa Offers",
        delete_title_body: "Offers sẽ bị xóa vĩnh viễn khỏi hệ thống.Bạn thực sự muốn xóa Offers này?",				 
	},
	methods:{
		getAdsType: function(){			
			axios.get(URL+'/ajax.php?act=vue_api-loadAds') 
			.then(function (response) {
				app.ads = response.data;  				     
			}); 
		},
		fetchAllData:function(){			
			axios.post(URL+'/ajax.php?act=vue_api-offers', {
				action:'fetchall',
				limit: this.limit,				
			}).then(function(response){
				app.totalCount = response.data.length;
				app.myArray = response.data.length;   
				if(app.totalCount<app.limit_hidden){
					app.hideMore = false;  
				}
				app.allData = response.data;
			});
		},		
		deleteData:function(id){  				
			this.tableId = id; 
			this.showModal = true;
		},
		doRemovePostback:function(){   			
			axios.post(URL+'/ajax.php?act=vue_api-offers', {
			action:'delete',
			id:this.tableId
			}).then(function(response){        
				alert(response.data.message); 
				app.fetchAllData();   
				app.showModal =false;    
			});    
		}, 
		readMore:function(){  
			this.limit = parseInt(this.limit_hidden) + 10;   
			axios.post(URL+'/ajax.php?act=vue_api-offers', {
				action:'fetchall',   
				limit: this.limit, 	
				ts:this.ts,
				te:this.te, 
				types: this.types,
				offer_id: this.offer_id  						  
			}).then(function(response){    
				app.limit_hidden = app.limit_hidden + app.limit; 
				app.limit = 10;
				if(app.limit_hidden > app.totalCount){
					app.hideMore = false;
				}
				app.allData = response.data;
			});
		}, 
		openModel:function(){
            app.name = '';
			app.cost = '';
			app.payout = '';
			app.payout_type = '';
			app.price = '';
			app.price_bonus = '';
			app.price_deduct = '';
			app.price_ship = '';
			app.key = '';
			app.type_ads = 0;
			app.tracking_token = '';
			app.s2s_postback_url = '';
			app.hiddenId = '';				
			app.getAdsType();		
            app.actionButton = "Add Offer";
            app.dynamicTitle = "Add Offers";
            app.myModel = true;
            app.hideStatus = false;
		},
		fetchData:function(id){
            axios.post(URL+'/ajax.php?act=vue_api-offers', {
                action:'fetchSingle',
                id:id
            }).then(function(response){   
				app.getAdsType();  
				app.hideStatus = true;
				app.hiddenId  = response.data.id;	
				app.name = response.data.name;
				app.cost = response.data.cost;
				app.payout = response.data.payout;
				app.payout_type = response.data.payout_type;
				app.price = response.data.price;
				app.price_bonus = response.data.price_bonus;
				app.price_deduct = response.data.price_deduct;
				app.price_ship = response.data.price_ship;
				app.key = response.data.key;
				app.type_ads = response.data.type_ads;				
				app.tracking_token = response.data.tracking_token;
				app.s2s_postback_url = response.data.s2s_postback_url;		
				app.status = response.data.status;					
                app.myModel = true;
                app.actionButton = 'Update Offer';
                app.dynamicTitle = 'Edit Offer';
            });	
        },		
		submitData:function(){
			app.errors = [];
            if (!app.name) {
                app.errors.push('Name không để trống.');
              }
            else if (!app.key) {
                app.errors.push('Key Không để trống.');
            }else{
				if(app.actionButton == 'Add Offer'){					
                    axios.post(URL+'/ajax.php?act=vue_api-offers', {
                        action:'insert',
						name : app.name,
                        cost : app.cost,
                        payout : app.payout,
                        payout_type : app.payout_type,
                        price : app.price,
                        price_bonus : app.price_bonus,
                        price_deduct : app.price_deduct,
						price_ship : app.price_ship,
						key : app.key,
						type_ads : app.type_ads,
						tracking_token : app.tracking_token,
						s2s_postback_url : app.s2s_postback_url						
                    }).then(function(response){
                        app.myModel = false;
                        app.fetchAllData();
                        app.name = '';
                        app.cost = '';
                        app.payout = 0;
                        app.payout_type = 'fixed';
                        app.price = '';
                        app.price_bonus = 0;
						app.price_deduct = '';
						app.price_ship = '';
						app.key = '';
						app.type_ads = 0;
						app.tracking_token = '';
						app.s2s_postback_url = '';
                        alert(response.data.message);
                    });
				}else if(app.actionButton == 'Update Offer'){
                    axios.post(URL+'/ajax.php?act=vue_api-offers', {
                        action:'update',
                        name : app.name,
                        cost : app.cost,
                        payout : app.payout,
                        payout_type : app.payout_type,
                        price : app.price, 
                        price_bonus : app.price_bonus,
                        price_deduct : app.price_deduct,
						price_ship : app.price_ship,
						key : app.key,
						type_ads : app.type_ads,
						tracking_token : app.tracking_token,
						s2s_postback_url : app.s2s_postback_url,
						status : app.status,
                        hiddenId : app.hiddenId
                    }).then(function(response){
                        app.myModel = false;
                        app.fetchAllData();
                        app.name = '';
                        app.cost = '';
                        app.payout = 0;
                        app.payout_type = 'fixed';
                        app.price = '';
                        app.price_bonus = 0;
						app.price_deduct = '';
						app.price_ship = '';
						app.key = '';
						app.type_ads = 0;
						app.tracking_token = '';
						app.s2s_postback_url = '';
						app.status = '';
                        app.hiddenId = '';
                        alert(response.data.message);
                    });
                }
			}	
		}
	},
	created:function(){
		this.fetchAllData();			
	}
})
var app = new Vue({
    el:'#crudApp',
    data:{	
        ads_title: "Danh sách Ads",
        allData:[],
        showModal:false,
        tableId:0,
        myModel: false,
        errors:[],
        hiddenId:0,
        currentSort:'name',
        currentSortDir:'asc',
        delete_title: "Xóa Ads",
        delete_title_body: "Ads sẽ bị xóa vĩnh viễn khỏi hệ thống.Bạn thực sự muốn xóa ads này?",
    },
    methods:{
        fetchAllData: function(){			
            axios.post(URL+'/ajax.php?act=vue_api-ads', {
                action:'fetchall',								
            }).then(function(response){					
                app.allData = response.data;
            });
        },
        openModel: function(){
            app.actionButton = "Add Ads";
               app.dynamicTitle = "Add Ads";
            app.myModel = true;
            this.ads = '';
            app.hideStatus = false;
        },
        fetchData:function(id){
            axios.post(URL+'/ajax.php?act=vue_api-ads', {
                action:'fetchSingle',
                id:id
            }).then(function(response){
                app.ads = response.data.ads;						
                app.hiddenId  = response.data.id;				
                app.myModel = true;
                app.actionButton = 'Update';
                app.dynamicTitle = 'Edit Ads';
            });	
        },		
        submitData: function(){
            app.errors = [];
            if (!app.ads) {
                app.errors.push('Ads không để trống.');
            }else{
                if(app.actionButton == 'Add Ads'){
                    axios.post(URL+'/ajax.php?act=vue_api-ads', {
                        action:'insert',
                        ads : app.ads												
                    }).then(function(response){
                        app.myModel = false;
                        app.fetchAllData();
                        app.ads = '';							
                        alert(response.data.message);
                    });
                }else if(app.actionButton == 'Update'){
                    axios.post(URL+'/ajax.php?act=vue_api-ads', {
                        action:'update',
                        ads : app.ads,							
                        hiddenId : app.hiddenId
                    }).then(function(response){
                        app.myModel = false;
                        app.fetchAllData();
                        app.ads = '';							
                        app.hiddenId = '';
                        alert(response.data.message);
                    });
                }
            }
        },
        deleteData: function(id){  				
            this.tableId = id; 				
            this.showModal = true;
        },
        doRemovePostback: function(){   			
            axios.post(URL+'/ajax.php?act=vue_api-ads', {
            action:'delete',
            id:this.tableId
            }).then(function(response){        
                alert(response.data.message); 
                app.fetchAllData();   
                app.showModal =false;    
            });    
        }
    },
    created:function(){
        this.fetchAllData();			
    }
})

var app = new Vue({
    el:'#crudApp',
    data:{
        pageSizes: [
            { id: '10', name: '10' },				
            { id: '50', name: '50' },
            { id: '100', name: '100' },
            { id: '250', name: '250' },
            { id: '500', name: '500' },
            { id: '750', name: '750' },
            { id: '1000', name: '1000' },
        ],
        errors: [],
        hideMore: true,
        showModal:false,
        myModel:false,
        backlist_title: 'Backlist Phone Number',
        allData:'',
        limit:10,
        limit_hidden:10,
        totalCount:0,
        tableId:0,
        actionButton:'Insert',
        delete_title: "Xóa Backlist",
        delete_title_body: "Backlist sẽ bị xóa vĩnh viễn khỏi hệ thống.Bạn thực sự muốn xóa Backlist này?",
    },
    methods:{			
        fetchAllData:function(){
            axios.post(URL+'/ajax.php?act=vue_api-backlist&countLength=1', {					
                action:'fetchall'
            }).then(function(response){										
                app.totalCount = response.data.length;
            });
            axios.post(URL+'/ajax.php?act=vue_api-backlist', {
                limit: this.limit,
                action:'fetchall'
            }).then(function(response){
                app.allData = response.data;
            });
        },
        readMore:function(){  
            this.limit = parseInt(this.limit_hidden) + 10;   
            axios.post(URL+'/ajax.php?act=vue_api-backlist', {
                action:'fetchall',   
                limit: this.limit, 					  
            }).then(function(response){    
                app.limit_hidden = app.limit_hidden + app.limit; 
                app.limit = 10;
                if(app.limit_hidden > app.totalCount){
                    app.hideMore = false;
                }
                app.allData = response.data;
            });
        }, 
        changePage(event){
            this.limit_hidden = parseInt(event.target.value);								
            this.limit = parseInt(this.limit_hidden); 
            axios.post(URL+'/ajax.php?act=vue_api-backlist', {
                action:'fetchall',   
                limit: this.limit, 					   
            }).then(function(response){ 
                app.limit_hidden = app.limit_hidden; 
                app.limit = event.target.value;
                if(app.limit_hidden > app.totalCount){
                    app.hideMore = false;
                }     
                app.allData = response.data;
            });    
        },  
        openModel:function(){
            app.phone_number = '';
            app.note = '';
            app.hiddenId = '';
            app.actionButton = "Insert";
            app.dynamicTitle = "Add backlist";
            app.myModel = true;
        },			
        deleteData:function(id){  				
            this.tableId = id; 
            this.showModal = true;
        }, 
        doRemovePostback:function(){   
            axios.post(URL+'/ajax.php?act=vue_api-backlist', {
            action:'delete',
            id:this.tableId
            }).then(function(response){        
                alert(response.data.message); 
                app.fetchAllData();   
                app.showModal =false;    
            });    
        },
        submitData:function(){				
            app.errors = [];
            if (!app.phone_number) {
                app.errors.push('Điện thoại không để trống.');
              }
            else if (!app.note) {
                app.errors.push('Note Không để trống.');
            }else{
                if(app.actionButton == 'Insert'){
                    axios.post(URL+'/ajax.php?act=vue_api-backlist', {
                        action:'insert',
                        phone_number : app.phone_number, 
                        note : app.note
                    }).then(function(response){
                        app.myModel = false;
                        app.fetchAllData();
                        app.phone_number = '';
                        app.note = '';
                        alert(response.data.message);
                    });
                }
                if(app.actionButton == 'Update'){
                    axios.post(URL+'/ajax.php?act=vue_api-backlist', {
                        action:'update',
                        phone_number : app.phone_number,
                        note : app.note,
                        hiddenId : app.hiddenId
                    }).then(function(response){
                        app.myModel = false;
                        app.fetchAllData();
                        app.phone_number = '';
                        app.note = '';
                        app.hiddenId = '';
                        alert(response.data.message);
                    });
                }
            }
        },
        fetchData:function(id){
            axios.post(URL+'/ajax.php?act=vue_api-backlist', {
                action:'fetchSingle',
                id:id
            }).then(function(response){
                app.phone_number = response.data.phone_number;
                app.note = response.data.note;	
                app.hiddenId  = response.data.id;				
                app.myModel = true;
                app.actionButton = 'Update';
                app.dynamicTitle = 'Edit Backlist';
            });	
        }		
    },		
    created:function(){
        this.fetchAllData();
    }
});
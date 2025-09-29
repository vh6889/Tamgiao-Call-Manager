<?php



if(!$_user){

	echo '<div class="alert alert-danger" role="alert">
			  <h4 class="alert-heading">An error occurred!</h4>
			  <p>Oops!!! You have just accessed a link that does not exist on the system or you do not have enough permissions to access.</p>
			  <hr>
			  <p class="mb-0">Please return to the previous page.</p>
			</div>';

	goto end;
}
$_db->exec_query("update `core_notifications` set `status`='0' where `id` = '".$_GET['id']."' ");
?>
<div id="crudApp">
      <!-- First row -->
      <div class="row mb-5 pb-3">

        <!-- Second column -->
        <div class="col-md-12 mx-auto white z-depth-1">
        <div class="col-md-2" style="margin-bottom:15px;">
    <span class="custom_select_show">Hiện thị</span>
    <select name="pageSize" id="pageSize" class="custom_select" @change="changePage($event)">
        <option v-for="pageSize in pageSizes" :value="pageSize.id" :key="pageSize.name">{{ pageSize.name }}</option>
    </select>
    <span class="custom_select_record">bản ghi</span> 
    <div class="clear-fix"></div>

</div>
          <div class="row">
            <div class="col-sm-12 px-3">
              <h2 class="h2-responsive">Thông báo mới ({{myArray}})</h2>
            </div>
          </div>

          <div class="row">
            <div class="col-md-12 pb-3">

              <div class="">
                <table id="dtBasicExample" class="table table-sm table-hover table-bordered " cellspacing="0" width="100%">
                  <thead>
                    <tr class="bg-info text-light">
                      <th style="color:#fff;" width="15%" class="text-center">STT</th>
                      <th style="color:#fff;" width="15%" class="text-center">Time</th>
                      <th style="color:#fff;" width="20%" class="text-left">From</th>
                      <th style="color:#fff;" width="65%" class="text-left">Message</th>
                    </tr>
                  </thead>
                  <tbody>
                      <tr v-for="(row, index) in notifications">
                        <td class="text-center" v-if>{{index + 1}}</td>
                        <td class="text-center"><span v-html="row.time"></span></td>
                        <td class="text-left"><span v-html="row.user_from"></span></td>
                        <td class="text-left"><span v-html="row.text"></span></td>
                      </tr>                    
                  </tbody>
                </table>
              </div>
              <h1 v-bind:class="[isFinished ? 'finish' : 'load-more']" @click='getPosts()' v-cloak>{{ buttonText }}</h1>
            </div>
          </div>

        </div>
        <!-- Second column -->

      </div>
      <!-- First row -->
      </div>
<script>
  var app = new Vue({
  el: '#crudApp',
  data: {
    pageSizes: [
      { id: '10', name: '10' },      
      { id: '50', name: '50' },
      { id: '100', name: '100' },
      { id: '250', name: '250' },
      { id: '500', name: '500' },
      { id: '700', name: '700' },
      { id: '1000', name: '1000' },
    ],                
    myArray:0,
    showModal:false,
    isFinished: false,
    row: 0, // Record selction position
    rowperpage: 10, // Number of records fetch at a time
    buttonText: 'Xem thêm',
    notifications: []
  },
  methods: {     
    getCountPost: function(){
      axios.post('<?php echo $_url; ?>/ajax.php?act=vue_api-notification&countpost=1', {        
        ts:this.ts,
        te:this.te, 
        types: this.types,
        offer_id: this.offer_id 
      })
      .then(function (response) {              
         if(response.data !='' ){
           app.myArray = response.data.length;
         }else{
          app.myArray = 0;
         }
       });       
    }, 
    getPosts: function(){            
      axios.post('<?php echo $_url; ?>/ajax.php?act=vue_api-notification', {        
        row: this.row, 
        rowperpage: this.rowperpage,        
      })
      .then(function (response) {          
        app.getCountPost();      
         if(response.data !='' ){
          
           // Update rowperpage
           app.row+=app.rowperpage;

           var len = app.notifications.length;           
           if(len > 0){
             app.buttonText = "Loading ...";
             setTimeout(function() {
                app.buttonText = "Load More";
                // Loop on data and push in posts
                for (let i = 0; i < response.data.length; i++){
                   app.notifications.push(response.data[i]); 
                } 
             },500);
           }else{
              app.notifications = response.data;
           }           
         }else{
           app.buttonText = "No more records avaiable.";
           app.isFinished = true;
         }
       });       
     },
     changePage(event){
      var row_1 = parseInt(event.target.value);  
      this.rowperpage = row_1;
      app.getPosts(); 
     },     
   },
   created: function(){      
      this.getPosts();            
   }
})
</script>


<?php

end:

?>
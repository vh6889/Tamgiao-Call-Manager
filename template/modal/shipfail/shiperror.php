<div v-if="myModelShiperror">
  <transition name="modal">
    <div class="modal-mask">
        <div class="modal-wrapper">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">                
                <h4 class="modal-title" v-html="modelShiperrorTitle"></h4>
                <button type="button" class="close" @click="myModelShiperror=false">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" v-html="modelShiperrorBody">                
            </div>            
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" @click="myModelShiperror=false" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" @click="doModelShiperror">Save changes</button>
            </div>
            </div>
        </div>
        </div>
    </div>
  </transition>
</div>
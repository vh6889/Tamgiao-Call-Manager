<div v-if="myModelApprove">
  <transition name="modal">
    <div class="modal-mask">
        <div class="modal-wrapper">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">                
                <h4 class="modal-title" v-html="modelApproveTitle"></h4>
                <button type="button" class="close" @click="myModelApprove=false">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" v-html="modelApproveBody">                
            </div>            
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" @click="myModelApprove=false" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" @click="doModelApprove">Save changes</button>
            </div>
            </div>
        </div>
        </div>
    </div>
    </transition>
</div>
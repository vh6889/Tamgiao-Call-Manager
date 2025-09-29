<!--Modal: modalConfirmDelete-->
<div v-if="showModal">
    <transition name="modal">
    <div class="modal-mask">
        <div class="modal-wrapper">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">                
                <h4 class="modal-title" v-html="delete_title"></h4>
                <button type="button" class="close" @click="showModal=false">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" v-html="delete_title_body">                
            </div>
            <input type="hidden" v-model="tableId">
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" @click="showModal=false" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" @click="doRemovePostback">Save changes</button>
            </div>
            </div>
        </div>
        </div>
    </div>
    </transition>
</div>
<!--Modal: modalConfirmDelete-->
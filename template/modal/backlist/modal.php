<div v-if="myModel">
    <transition name="model">
        <div class="modal-mask">
            <div class="modal-wrapper">
                <div class="modal-dialog">
                    <div class="modal-content">
                    <div class="modal-header">
                    <h4 class="modal-title">{{ dynamicTitle }}</h4>
                    <button type="button" class="close" @click="myModel=false"><span aria-hidden="true">&times;</span></button>						
                    </div>
                    <div class="modal-body">							
                        <p v-if="errors.length">
                            <b>Please correct the following error(s):</b>
                            <ul class="text-danger" style="padding-left:17px;">
                                <li v-for="error in errors"><span class="text-danger" v-html="error"></span></li>
                            </ul>
                        </p>
                        <div class="form-group">
                            <label>Phone number</label>
                            <input type="text" class="form-control" v-model="phone_number" />
                        </div>
                        <div class="form-group">
                            <label>Note</label>
                            <input type="text" class="form-control" v-model="note" />
                        </div>
                        <br />
                        <div align="center">
                            <input type="hidden" v-model="hiddenId" />	
                            <button type="button" class="btn btn-danger waves-effect waves-light" data-dismiss="modal" @click="myModel=false">Hủy bỏ</button>						
                            <input type="button" class="btn btn-success btn-xs" v-model="actionButton" @click="submitData" />
                        </div>							
                    </div>						
                </div>
            </div>
        </div>
    </transition>
</div>
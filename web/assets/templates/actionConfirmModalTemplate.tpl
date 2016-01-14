<div class="modal fade" id="actionConfirmModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Confirm action</h4>
            </div>
            <div class="modal-body">
                <%= rc.message %>
            </div>
            <div class="modal-footer">
                <div class="col-md-6">
                    <button type="button" class="btn btn-primary action-confirm-ok"><i class="fa fa-check"></i> OK</button>
                </div>
                <div class="col-md-6">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>
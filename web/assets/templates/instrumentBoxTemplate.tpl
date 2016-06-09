<div class="row">
    <div class="col-md-5 col-sm-4 col-xs-8">
        <input type="hidden" name="fos_user_profile_form[instruments][<%- rc.num %>][instrument]" class="instrument-select form-control" />
    </div>
    <div class="col-md-4 col-sm-4 col-xs-6">
        <input type="hidden" name="fos_user_profile_form[instruments][<%- rc.num %>][skillLevel]" class="skill-select form-control" />
    </div>

    <div class="col-md-2 col-sm-2 col-xs-3 learn-options hidden" style="text-align: center; padding-top: 13px">
        <input type="checkbox" id="fos_user_profile_form_instruments_<%- rc.num %>_wouldLearn" name="fos_user_profile_form[instruments][<%- rc.num %>][wouldLearn]" class="would-learn" value="1"><label class="control-label" for="fos_user_profile_form_instruments_<%- rc.num %>_wouldLearn"><span></span></label>
    </div>

    <div class="col-md-1 col-sm-1 col-xs-1" style="padding: 10px 0 0 0">
        <a href="" class="remove-instrument text-danger" title="remove"><i class="fa fa-times"></i></a>&nbsp
    </div>
</div>
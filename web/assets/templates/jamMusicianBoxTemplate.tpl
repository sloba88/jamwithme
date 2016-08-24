<div class="row">

    <div class="col-md-6">
        <select id="jam_members_<%- rc.num %>_musician" name="jam[members][<%- rc.num %>][musician]" required="required" class="form-control member-user"></select>
    </div>

    <div class="col-md-5">
        <select id="jam_members_<%- rc.num %>_instrument" name="jam[members][<%- rc.num %>][instrument]" required="required" class="form-control member-instrument"></select>
    </div>

    <div class="col-md-1 col-sm-1 col-xs-1" style="padding: 10px 0 0 0">
        <a href="" class="remove-member text-danger" title="remove"><i class="fa fa-times"></i></a>&nbsp
    </div>

</div>
<li class="row">

    <div class="col-md-6">
        <div id="jam_members_<%- rc.num %>_invitee">
            <div class="form-group">
                <input type="email" id="jam_members_<%- rc.num %>_invitee_email" placeholder="Email" name="jam[members][<%- rc.num %>][invitee][email]" class="form-control">
            </div>
            <div class="form-group">
                <input type="text" id="jam_members_<%- rc.num %>_invitee_firstName" placeholder="First name" name="jam[members][<%- rc.num %>][invitee][firstName]" class="form-control">
            </div>
            <div class="form-group">
                <input type="text" id="jam_members_<%- rc.num %>_invitee_lastName" placeholder="Last name" name="jam[members][<%- rc.num %>][invitee][lastName]" class="form-control">
            </div>
        </div>
    </div>

    <div class="col-md-5">
        <select id="jam_members_<%- rc.num %>_instrument" name="jam[members][<%- rc.num %>][instrument]" required="required" class="form-control member-instrument"></select>
    </div>

    <div class="col-md-1 col-sm-1 col-xs-1" style="padding: 10px 0 0 0">
        <a href="" class="remove-member text-danger" title="remove"><i class="fa fa-times"></i></a>&nbsp
    </div>

</li>
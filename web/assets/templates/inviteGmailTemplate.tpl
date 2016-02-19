<div class="col-xs-6 col-sm-4 col-lg-3 musician-box-container invite-friend-box">
    <span class="people-grid">
        <div class="people-info">
            <h3 class="name" title="<%- rc.username %>" <% if (rc.title.$t.length > 21) { %> style="font-size:11px" <% } if (rc.title.$t.length > 16) { %> style="font-size:13px" <% } %>><%- rc.title.$t %></h3>
            <p>
                <%- rc.gd$email[0].address %>
            </p>
            <input type="checkbox" id="email_<%- rc.gd$email[0].address %>" name="email[]" value="<%- rc.gd$email[0].address %>" />
            <label class="control-label" for="email_<%- rc.gd$email[0].address %>"><span>&nbsp;</span></label>
        </div>
    </span>
</div>
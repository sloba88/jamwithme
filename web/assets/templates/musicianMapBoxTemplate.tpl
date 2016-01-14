<div class="musician-map-box <% if(rc.teacher){ %>teacher<% } %>">
    <a href="<%- rc.url %>"><img src="<%- rc.image %>" />
        <p><%- rc.username %></p>
    </a>
    <div class="clearfix"></div>
        <span class="musician-map-box-genres">
            <% _.each( rc.genres, function(v, k){ %>
                <% if (k!=0){ %>|<% } %>
                <%- v %>
            <% }); %>
        </span>
</div>
<div class="musician-map-box-container">
    <a href="<%- baseURL %>/m/<%- rc.username %>" class="clearfix">
        <img src="<%- baseURL %>/m/<%- rc.username %>/avatar/my_thumb" alt="<%- rc.username %>" class="img-responsive" onerror="imgError(this, 'medium_thumb');">
        <div class="people-info">
            <h3 class="name"><%- rc.displayName %></h3>
            <ul class="tags">
                <% _.each( rc.genres, function(v, k){ %>
                <% if (k < 3){ %><li><%- v %></li><% } %>
                <% }); %>
            </ul>
                <span class="instrument" title="<%- rc.instrument %>">
                    <% if (rc.instrument) {  %>
                        <img src="/assets/images/icons-svg/<%- rc.instrument %>.svg" class="inject-me" />
                    <% } %>
                    <%= rc.teacherIcon %>
                </span>
            <ul class="compatibility-box">
                <li class="compatibility">
                    <span class="compatibility-<%= rc.compatibility %>"><%= rc.compatibility %></span>
                    compatibility
                </li>
            </ul>
        </div>
    </a><!--people-grid ends-->
</div>
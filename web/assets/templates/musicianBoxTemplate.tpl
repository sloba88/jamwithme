<div class="col-xs-6 col-sm-4 col-lg-3 musician-box-container">
    <a href="<%= baseURL %>/m/<%- rc.username %>" class="people-grid" alt="<%- rc.username %>">
        <img src="<%= baseURL %>/m/<%- rc.username %>/avatar/medium_thumb" alt="<%- rc.username %>" class="img-responsive" onerror="imgError(this, 'medium_thumb');">
        <div class="people-info">
            <h3 class="name"><%- rc.username %></h3>
                    <span class="instrument" title="<%- rc.instrument %>">
                        <%= rc.icon %>
                    </span>
            <ul class="tags" >
                <% _.each( rc.genres, function(v, k){ %>
                <% if (k < 3){ %><li <% if (rc.genres.length > 3){ %>style="font-size:10px"<% } %>><%- v %></li><% } %>
                <% }); %>
            </ul>
            <ul class="compatibility-box">
                <li class="compatibility">
                    <span><%= rc.compatibility %>%</span>
                    compatibility
                </li>
            </ul>
        </div>
    </a><!--people-grid ends-->
</div>
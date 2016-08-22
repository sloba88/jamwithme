<div class="col-xs-6 col-sm-4 col-lg-3 musician-box-container">
    <a href="<%= baseURL %>/m/<%- rc.username %>" class="people-grid <% if (rc.teacher){ %>teacher<% } %>" alt="<%- rc.displayName %>">
        <img src="<%- rc.avatar %>" alt="<%- rc.username %>" class="img-responsive" onerror="imgError(this, 'medium_thumb');">
        <div class="people-info">
            <h3 class="name" title="<%- rc.username %>" <% if (rc.username.length > 21) { %> style="font-size:11px" <% } if (rc.username.length > 16) { %> style="font-size:13px" <% } %>><%- rc.displayName %></h3>
                <% if (rc.instrument) { %>
                <span class="instrument" title="<%- rc.instrument %>">
                    <img class="inject-me" src="/assets/images/icons-svg/<%- rc.instrument %>.svg">
                </span>
                <% } %>
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
    </a>
</div>
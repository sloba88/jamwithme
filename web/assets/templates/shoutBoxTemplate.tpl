<% mu = rc.musician %>
<article class="shout-box clearfix <% if(mu.teacher){ %>teacher<% } %>">
    <a href="<%= baseURL %>/m/<%- mu.username %>" class="musician-box-image-link img-circle">
        <img src="<%= baseURL %>/m/<%- mu.username %>/avatar" class="" width="50" />
    </a>
    <div class="shout-text">
        <% if (mu.me) { %>
        <a href="javascript:void(0)" class="remove-shout action-confirm" id="<%= rc.id %>" title="Remove shout"><i class="fa fa-times"></i></a>
        <% } %>
        <h4 class="name"><a href="<%= baseURL %>/m/<%- mu.username %>"><%- mu.username %></a></h4>
        <% if (mu.location) { %><span class="musician-box-location"><i class="glyphicon glyphicon-map-marker"></i> <%- mu.location %></span><% } %>
        <div class="time">
            <span><%- rc.createdAt %> </span>
        </div>
        <p><%= rc.text %></p>
    </div>
</article>
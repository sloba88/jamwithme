<% createdAt = new Date(rc.createdAt); %>
<div class="conversation-single">
    <a href="<%= baseURL %>/m/<%- rc.fromData.username %>">
        <img class="message-picture" src="<%= rc.avatar %>" alt="Pic">
        <h4 class="name"><%- rc.fromData.username %></h4>
    </a>

    <div class="time">
        <%- createdAt.toLocaleDateString() %> <%- createdAt.toLocaleTimeString() %>
    </div>
    <p class="text">
        <%- rc.message %>
    </p>
</div>
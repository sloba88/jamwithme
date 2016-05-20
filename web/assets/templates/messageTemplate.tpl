<% createdAt = new Date(rc.createdAt); %>
<div class="conversation-single">
    <a href="<%= baseURL %>/m/<%- utf8.decode(rc.fromData.username) %>">
        <img class="message-picture" src="<%= baseURL %>/m/<%- rc.fromData.id %>/avatar" alt="Pic">
        <h4 class="name"><%- utf8.decode(rc.fromData.username) %></h4>
    </a>

    <div class="time">
        <%- createdAt.toLocaleDateString() %> <%- createdAt.toLocaleTimeString() %>
    </div>
    <p class="text">
        <%= utf8.decode(rc.message) %>
    </p>
</div>
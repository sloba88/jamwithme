<div class="row open-conversation message-single <% if (rc.isRead == false) { %>unread<% } %> conversation-box <% if (rc.index == 0){ %> first<% } %>" data-id="<%- rc._id %>" data-user="<%- rc.fromData.username %>">
    <div class="col-xs-4 col-sm-3 message-info">
        <img class="message-picture" src="<%= baseURL %>/m/<%- rc.fromData.username %>/avatar" alt="<%- rc._lastMessage.from %>">

        <h3 class="name"><%- rc.fromData.username %></h3>

        <div class="time"><%- rc._lastMessage.createdAt.toLocaleDateString() %> <%- rc._lastMessage.createdAt.toLocaleTimeString() %></div>
    </div>
    <div class="col-xs-8 col-sm-9 message-content">
        <p><%- rc._lastMessage.message %></p>
        <i class="fa fa-angle-right"></i>
    </div>
</div><!--message-one ends-->
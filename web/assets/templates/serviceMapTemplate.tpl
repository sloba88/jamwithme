<div class="service-map-box-container">
    <h3 class="name"><%- rc.displayName %></h3>
    <% if (rc.address) { %><p>Address: <%- rc.address %></p><% } %>
    <% if (rc.phone) { %><p>Phone: <%- rc.phone %></p><% } %>
    <p>Email: <%- rc.email %></p>
    <p>Website: <a target="_blank" href="<%- rc.website %>"><%- rc.website %></a></p>
</div>
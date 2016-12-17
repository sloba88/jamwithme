<li>
    <a href="<%= baseURL %>/m/<%- rc.username %>" class="musician-box-image-link">
        <img src="<%= baseURL %>/m/<%- rc.username %>/avatar" class="" width="50" />
        <h4 class="name"><% if (rc.firstName) { %><%- rc.firstName %><br /><%-rc.lastName %><% }else{ %><%- rc.username %><% } %></h4>
    </a>
</li>
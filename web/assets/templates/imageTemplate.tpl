<div class="image-holder profile-media-wall-item image-type-<%- rc.type %>" data-image-id="<%- rc.id %>">
    <a class="fancybox" rel="group" href="<%- rc.url %>"><img src="<%- rc.thumbnailUrl %>" /></a>
    <div class="profile-media-image-commands">
        <a href="#" class="remove-image-ajax action-confirm" data-id="<%- rc.id %>" title="Remove Image"><i class="fa fa-times"></i></span><% if ([1, 4].indexOf(rc.imageType) == -1) { %> Remove<% } %></a>
        <a href="#" class="set-profile-photo" data-id="<%- rc.id %>" title="Set as profile photo"><span class="glyphicon glyphicon-user"></span><% if ([1, 4].indexOf(rc.imageType) == -1) { %> Set as profile photo<% } %></a>
    </div>
</div>
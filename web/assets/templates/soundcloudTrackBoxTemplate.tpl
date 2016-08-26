<li data-id="<%- rc.id %>" class="soundcloud-track-holder">

    <iframe id="sc_track_<%- rc.id %> " width="100%" height="166" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=<%- rc.url %>"></iframe>

    <input type="hidden" name="soundcloudTracks[]" value="<%- rc.id %>" />
    <a href="" class="remove-soundcloud-track action-confirm" title="remove"><i class="fa fa-times"></i> Remove</a>
</li>
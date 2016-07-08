<li data-id="<%- rc.id %>" class="ytvideo-holder">
    <a class="ytvideo" rel="group" href="<%- rc.url %>&autoplay=1&showinfo=0&controls=1">
        <span class="play-icon"></span>
        <img src="" height="160" width="260" />
    </a>
    <input type="hidden" name="video[]" value="<%- rc.id %>" />
    <a href="" class="remove-video action-confirm" title="remove"><i class="fa fa-times"></i> Remove</a>
</li>
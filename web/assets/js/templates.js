this["JST"] = this["JST"] || {};

this["JST"]["actionConfirmModalTemplate"] = function(rc) {
var __t, __p = '', __e = _.escape;
__p += '<div class="modal fade" id="actionConfirmModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">\n    <div class="modal-dialog" role="document">\n        <div class="modal-content">\n            <div class="modal-header">\n                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>\n                <h4 class="modal-title" id="myModalLabel">Confirm action</h4>\n            </div>\n            <div class="modal-body">\n                ' +
((__t = ( rc.message )) == null ? '' : __t) +
'\n            </div>\n            <div class="modal-footer">\n                <div class="col-md-6">\n                    <button type="button" class="btn btn-primary action-confirm-ok"><i class="fa fa-check"></i> OK</button>\n                </div>\n                <div class="col-md-6">\n                    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>\n                </div>\n            </div>\n        </div>\n    </div>\n</div>';
return __p
};

this["JST"]["conversationTemplate"] = function(rc) {
var __t, __p = '', __e = _.escape, __j = Array.prototype.join;
function print() { __p += __j.call(arguments, '') }
__p += '<div class="row open-conversation message-single ';
 if (rc.isRead == false) { ;
__p += 'unread';
 } ;
__p += ' conversation-box ';
 if (rc.index == 0){ ;
__p += ' first';
 } ;
__p += '" data-id="' +
__e( rc._id ) +
'" data-user="' +
__e( rc.fromData.username ) +
'">\n    <div class="col-xs-4 col-sm-3 message-info">\n        <img class="message-picture" src="' +
((__t = ( baseURL )) == null ? '' : __t) +
'/m/' +
__e( rc.fromData.username ) +
'/avatar" alt="' +
__e( rc._lastMessage.from ) +
'">\n\n        <h3 class="name">' +
__e( rc.fromData.username ) +
'</h3>\n\n        <div class="time">' +
__e( rc._lastMessage.createdAt.toLocaleDateString() ) +
' ' +
__e( rc._lastMessage.createdAt.toLocaleTimeString() ) +
'</div>\n    </div>\n    <div class="col-xs-8 col-sm-9 message-content">\n        <p>' +
__e( rc._lastMessage.message ) +
'</p>\n        <i class="fa fa-angle-right"></i>\n    </div>\n</div><!--message-one ends-->';
return __p
};

this["JST"]["imageCropModalTemplate"] = function(rc) {
var __t, __p = '', __e = _.escape;
__p += '<div class="modal fade" id="imageCropModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">\n    <div class="modal-dialog">\n        <div class="modal-content">\n            <div class="modal-header">\n                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>\n                <h4 class="modal-title" id="myModalLabel">Crop and upload photo</h4>\n            </div>\n            <div class="modal-body"></div>\n            <div class="modal-footer">\n                <div class="col-sm-4 first">\n                    <button type="button" class="btn btn-primary btn-save-changes" data-dismiss="modal">\n                        <i class="fa fa-check"></i>\n                        Save and upload\n                    </button>\n                </div>\n                <div class="col-sm-4">\n                    <button type="button" class="btn btn-default cancel-crop" data-dismiss="modal">\n                        <i class="fa fa-times"></i>\n                        Cancel\n                    </button>\n                </div>\n                <div class="col-sm-4 last">\n                    <button type="button" class="btn btn-danger btn-remove-crop-photo">\n                        <i class="fa fa-times"></i>\n                        Remove\n                    </button>\n                </div>\n            </div>\n        </div>\n    </div>\n</div>';
return __p
};

this["JST"]["imageTemplate"] = function(rc) {
var __t, __p = '', __e = _.escape, __j = Array.prototype.join;
function print() { __p += __j.call(arguments, '') }
__p += '<div class="image-holder profile-media-wall-item image-type-' +
__e( rc.type ) +
'" data-image-id="' +
__e( rc.id ) +
'">\n    <a class="fancybox" rel="group" href="' +
__e( rc.url ) +
'"><img src="' +
__e( rc.thumbnailUrl ) +
'" /></a>\n    <div class="profile-media-image-commands">\n        <a href="#" class="remove-image-ajax action-confirm" data-id="' +
__e( rc.id ) +
'" title="Remove Image"><i class="fa fa-times"></i></span>';
 if ([1, 4].indexOf(rc.imageType) == -1) { ;
__p += ' Remove';
 } ;
__p += '</a>\n        <a href="#" class="set-profile-photo" data-id="' +
__e( rc.id ) +
'" title="Set as profile photo"><span class="glyphicon glyphicon-user"></span>';
 if ([1, 4].indexOf(rc.imageType) == -1) { ;
__p += ' Set as profile photo';
 } ;
__p += '</a>\n    </div>\n</div>';
return __p
};

this["JST"]["instrumentBoxTemplate"] = function(rc) {
var __t, __p = '', __e = _.escape;
__p += '<div class="row">\n    <div class="col-md-5">\n        <input type="hidden" name="fos_user_profile_form[instruments][' +
__e( rc.num ) +
'][instrument]" class="instrument-select form-control" />\n    </div>\n    <div class="col-md-5">\n        <input type="hidden" name="fos_user_profile_form[instruments][' +
__e( rc.num ) +
'][skillLevel]" class="skill-select form-control" />\n    </div>\n    <div class="col-md-2" style="padding-top:10px">\n        <a href="" class="remove-instrument text-danger" title="remove"><i class="fa fa-times"></i></a>&nbsp\n        <a class="handle" title="re-order"><i class="fa fa-bars"></i></a>\n    </div>\n</div>';
return __p
};

this["JST"]["messageTemplate"] = function(rc) {
var __t, __p = '', __e = _.escape, __j = Array.prototype.join;
function print() { __p += __j.call(arguments, '') }

 createdAt = new Date(rc.createdAt); ;
__p += '\n<div class="conversation-single">\n    <a href="' +
((__t = ( baseURL )) == null ? '' : __t) +
'/m/' +
__e( rc.fromData.username ) +
'">\n        <img class="message-picture" src="' +
((__t = ( rc.avatar )) == null ? '' : __t) +
'" alt="Pic">\n        <h4 class="name">' +
__e( rc.fromData.username ) +
'</h4>\n    </a>\n\n    <div class="time">\n        ' +
__e( createdAt.toLocaleDateString() ) +
' ' +
__e( createdAt.toLocaleTimeString() ) +
'\n    </div>\n    <p class="text">\n        ' +
__e( rc.message ) +
'\n    </p>\n</div>';
return __p
};

this["JST"]["musicianBoxTemplate"] = function(rc) {
var __t, __p = '', __e = _.escape, __j = Array.prototype.join;
function print() { __p += __j.call(arguments, '') }
__p += '<div class="col-xs-6 col-sm-4 col-lg-3 musician-box-container">\n    <a href="' +
((__t = ( baseURL )) == null ? '' : __t) +
'/m/' +
__e( rc.username ) +
'" class="people-grid" alt="' +
__e( rc.username ) +
'">\n        <img src="' +
__e( rc.avatar ) +
'" alt="' +
__e( rc.username ) +
'" class="img-responsive" onerror="imgError(this, \'medium_thumb\');">\n        <div class="people-info">\n            <h3 class="name">' +
__e( rc.username ) +
'</h3>\n                    <span class="instrument" title="' +
__e( rc.instrument ) +
'">\n                        ' +
((__t = ( rc.icon )) == null ? '' : __t) +
'\n                    </span>\n            <ul class="tags" >\n                ';
 _.each( rc.genres, function(v, k){ ;
__p += '\n                ';
 if (k < 3){ ;
__p += '<li ';
 if (rc.genres.length > 3){ ;
__p += 'style="font-size:10px"';
 } ;
__p += '>' +
__e( v ) +
'</li>';
 } ;
__p += '\n                ';
 }); ;
__p += '\n            </ul>\n            <ul class="compatibility-box">\n                <li class="compatibility">\n                    <span>' +
((__t = ( rc.compatibility )) == null ? '' : __t) +
'%</span>\n                    compatibility\n                </li>\n            </ul>\n        </div>\n    </a><!--people-grid ends-->\n</div>';
return __p
};

this["JST"]["musicianMapBoxTemplate"] = function(rc) {
var __t, __p = '', __e = _.escape, __j = Array.prototype.join;
function print() { __p += __j.call(arguments, '') }
__p += '<div class="musician-map-box ';
 if(rc.teacher){ ;
__p += 'teacher';
 } ;
__p += '">\n    <a href="' +
__e( rc.url ) +
'"><img src="' +
__e( rc.image ) +
'" />\n        <p>' +
__e( rc.username ) +
'</p>\n    </a>\n    <div class="clearfix"></div>\n        <span class="musician-map-box-genres">\n            ';
 _.each( rc.genres, function(v, k){ ;
__p += '\n                ';
 if (k!=0){ ;
__p += '|';
 } ;
__p += '\n                ' +
__e( v ) +
'\n            ';
 }); ;
__p += '\n        </span>\n</div>';
return __p
};

this["JST"]["musicianMapTemplate"] = function(rc) {
var __t, __p = '', __e = _.escape, __j = Array.prototype.join;
function print() { __p += __j.call(arguments, '') }
__p += '<div class="musician-map-box-container">\n    <a href="' +
__e( baseURL ) +
'/m/' +
__e( rc.username ) +
'" class="clearfix">\n        <img src="' +
__e( baseURL ) +
'/m/' +
__e( rc.username ) +
'/avatar/my_thumb" alt="' +
__e( rc.username ) +
'" class="img-responsive" onerror="imgError(this, \'medium_thumb\');">\n        <div class="people-info">\n            <h3 class="name">' +
__e( rc.username ) +
'</h3>\n            <ul class="tags">\n                ';
 _.each( rc.genres, function(v, k){ ;
__p += '\n                ';
 if (k < 3){ ;
__p += '<li>' +
__e( v ) +
'</li>';
 } ;
__p += '\n                ';
 }); ;
__p += '\n            </ul>\n                <span class="instrument" title="' +
__e( rc.instrument ) +
'">\n                    ' +
((__t = ( rc.icon )) == null ? '' : __t) +
'\n                    ' +
((__t = ( rc.teacherIcon )) == null ? '' : __t) +
'\n                </span>\n            <ul class="compatibility-box">\n                <li class="compatibility">\n                    <span>' +
((__t = ( rc.compatibility )) == null ? '' : __t) +
'%</span>\n                    compatibility\n                </li>\n            </ul>\n        </div>\n    </a><!--people-grid ends-->\n</div>';
return __p
};

this["JST"]["notificationTemplate"] = function(rc) {
var __t, __p = '', __e = _.escape;
__p += '<div class="alert alert-' +
__e( rc.type ) +
' fade in ' +
__e( rc.temp ) +
'">\n    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>\n    ' +
__e( rc.message ) +
'\n</div>';
return __p
};

this["JST"]["searchAutocompleteTemplate"] = function(rc) {
var __t, __p = '', __e = _.escape;
__p += '<a href=\'' +
((__t = ( baseURL )) == null ? '' : __t) +
'/m/' +
__e( rc.username ) +
'\'>\n    <img src=\'' +
((__t = ( baseURL )) == null ? '' : __t) +
'/m/' +
__e( rc.username ) +
'/avatar/my_thumb\' />\n    <span class=\'search-text\'>' +
__e( rc.username ) +
'\n        <span class=\'search-location\'>' +
__e( rc.fullName ) +
'</span>\n    </span>\n</a>';
return __p
};

this["JST"]["shoutBoxTemplate"] = function(rc) {
var __t, __p = '', __e = _.escape, __j = Array.prototype.join;
function print() { __p += __j.call(arguments, '') }

 mu = rc.musician ;
__p += '\n<article class="shout-box clearfix ';
 if(mu.teacher){ ;
__p += 'teacher';
 } ;
__p += '">\n    <a href="' +
((__t = ( baseURL )) == null ? '' : __t) +
'/m/' +
__e( mu.username ) +
'" class="musician-box-image-link img-circle">\n        <img src="' +
((__t = ( baseURL )) == null ? '' : __t) +
'/m/' +
__e( mu.username ) +
'/avatar" class="" width="50" />\n    </a>\n    <div class="shout-text">\n        ';
 if (mu.me) { ;
__p += '\n        <a href="javascript:void(0)" class="remove-shout action-confirm" id="' +
((__t = ( rc.id )) == null ? '' : __t) +
'" title="Remove shout"><i class="fa fa-times"></i></a>\n        ';
 } ;
__p += '\n        <h4 class="name"><a href="' +
((__t = ( baseURL )) == null ? '' : __t) +
'/m/' +
__e( mu.username ) +
'">' +
__e( mu.username ) +
'</a></h4>\n        ';
 if (mu.location) { ;
__p += '<span class="musician-box-location"><i class="glyphicon glyphicon-map-marker"></i> ' +
__e( mu.location ) +
'</span>';
 } ;
__p += '\n        <div class="time">\n            <span>' +
__e( rc.createdAt ) +
' </span>\n        </div>\n        <p>' +
__e( rc.text ) +
'</p>\n    </div>\n</article>';
return __p
};

this["JST"]["videoAddBoxTemplate"] = function(rc) {
var __t, __p = '', __e = _.escape;
__p += '<li class="add-video-box">\n    <div class="input-group">\n        <input type="text" class="form-control youtube-url" placeholder="Paste Youtube link here">\n          <span class="input-group-btn">\n            <button class="btn btn-default save-video" type="button">Save</button>\n          </span>\n    </div><!-- /input-group -->\n</li>';
return __p
};

this["JST"]["videoBoxTemplate"] = function(rc) {
var __t, __p = '', __e = _.escape;
__p += '<li data-id="' +
__e( rc.id ) +
'" class="ytvideo-holder">\n    <a class="ytvideo" rel="group" href="' +
__e( rc.url ) +
'&autoplay=1&showinfo=0&controls=1">\n        <span class="play-icon"></span>\n        <img src="" height="140" width="260" />\n    </a>\n    <a href="" class="remove-video action-confirm" title="remove"><i class="fa fa-times"></i> Remove</a>\n</li>';
return __p
};
var SocialLoginPopup = new Class.create();
SocialLoginPopup.prototype = {
    initialize: function (config) {
        this.screenX = typeof window.screenX != 'undefined' ? window.screenX : window.screenLeft;
        this.screenY = typeof window.screenY != 'undefined' ? window.screenY : window.screenTop;
        this.outerWidth = typeof window.outerWidth != 'undefined' ? window.outerWidth : document.body.clientWidth;
        this.outerHeight = typeof window.outerHeight != 'undefined' ? window.outerHeight : (document.body.clientHeight - 22);
        this.width = config.w ? config.w : 500;
        this.height = config.h ? config.h : 270;
        this.left = config.l ? config.l : parseInt(this.screenX + ((this.outerWidth - this.width) / 2), 10);
        this.top = config.t ? config.t : parseInt(this.screenY + ((this.outerHeight - this.height) / 2.5), 10);
        this.features = (
            'width=' + this.width +
            ',height=' + this.height +
            ',left=' + this.left +
            ',top=' + this.top
        );
        this.url = config.url;
        this.title = config.title;
        this.btnElement = $(config.btnElement);
        this.initObservers();
    },
    initObservers: function () {
        if(this.btnElement) {
            this.btnElement.observe('click', function () {
                this.open(this.url, this.title, this.features)
            }.bind(this));
        }
    },
    open: function (url, title, features) {
        this.opener = window.open(url, title, features);
    }
};

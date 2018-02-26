window.Vue = require('vue');

Vue.component('blog-create-form', require('./components/BlogCreateForm'));

new Vue({
    el: '#wrapper'
});
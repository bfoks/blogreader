<script>

    import Echo from 'laravel-echo'

    window.Pusher = require('pusher-js');

    const INITIAL_AFTER_SUBMIT_VALUE = 5;

    export default {

        data() {
            return {
                blogSubmitInProgress: false,
                percentageIndexingValue: 0
            }
        },

        created() {

            window.Echo = new Echo({
                broadcaster: 'pusher',
                key: process.env.MIX_PUSHER_APP_KEY,
                cluster: process.env.MIX_PUSHER_APP_CLUSTER,
                encrypted: true
            });

            window.Echo.channel('blog-indexing')
                .listen('.status-updated', (e) => {
                    this.percentageIndexingValue = e.percentageIndexingProgress + INITIAL_AFTER_SUBMIT_VALUE;
                });

        },

        // mounted() {
        //     this.$refs.url.placeholder = "Blog url...";
        // },

        methods: {

            submitBlog() {
                this.blogSubmitInProgress = true;
                this.$refs.blogForm.submit();
                this.percentageIndexingValue = INITIAL_AFTER_SUBMIT_VALUE;
            }

        }
    }
</script>
<style>
    progress {
        opacity: 0;
        transition: opacity 1s linear;
        background-color: #eeeeee;
    }

    ::-webkit-progress-value {
        background-color: #f6993f;
        -webkit-transition: width 3s linear;
        -moz-transition: width 3s linear;
        -o-transition: width 3s linear;
        transition: width 3s linear;
    }

    ::-webkit-progress-bar {
        background-color: #eeeeee;
    }

</style>
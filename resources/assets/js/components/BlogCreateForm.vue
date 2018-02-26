<script>
    export default{

        data() {
            return {
                blogSubmitInProgress: false
            }
        },

        methods: {

            submitBlog() {
                this.blogSubmitInProgress = true;
                this.$refs.blogForm.submit();
            }

        }
    }
</script>
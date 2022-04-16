<template>
    <div class="layout">
        <div class="panel">
            <div class="panel-body">
                <div class="page-center">
                    <h1>Sorry, the page you are looking for could not be found.</h1>
                </div>
            </div>
        </div>
    </div>
</template>
<script type="text/javascript">
    export default {
        beforeRouteEnter(to, from, next) {
            next(vm => vm.setData())
        },
        beforeRouteUpdate (to, from, next) {
            this.setData()
            next()
        },
        methods: {
            setData() {
                this.$title.set('Page Not Found')
                this.$bar.finish()
            }
        }
    }
</script>

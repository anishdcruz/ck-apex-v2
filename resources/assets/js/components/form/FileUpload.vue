<template>
    <input type="file" class="form-control" accept="images/*" @change="upload">
</template>
<script type="text/javascript">
    export default {
        methods: {
            upload(e) {
                const files = e.target.files
                if(files && files.length > 0) {
                    this.$emit('ready', {
                        target: {
                            value: files[0]
                        }
                    })
                }
            }
        }
    }
</script>

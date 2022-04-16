<template>
    <div>
        <div class="image-preview" :style="styleObject">
            <img v-if="source" :src="source" style="width: 100%; display: block;">
            <i v-if="source" class="fa fa-times image-close" @click="close"></i>
            <label v-if="!source" :for="`file${id}`" class="image-icon">
                <i class="fa fa-upload fa-3x"></i>
            </label>
            <input v-if="!source" style="display: none" type="file" :name="`file${id}`" :id="`file${id}`"
                accept="images/*" @change="upload">
        </div>
    </div>
</template>
<script type="text/javascript">
    export default {
        props: {
            preview: {
                default: null
            },
            width: {
                default: 100
            },
            height: {
                default: 100
            }
        },
        data() {
            return {
                id: Math.random().toString(36).substring(7),
                styleObject: {
                    height: `${this.height}px`,
                    width: `${this.width}px`
                },
                uploadedFile: null
            }
        },
        computed: {
            source() {
                return this.preview ? `/uploads/${this.preview}` : this.uploadedFile
            }
        },
        methods: {
            close() {
                this.uploadedFile = null
                this.$emit('remove')
            },
            upload(e) {
                const files = e.target.files
                if(files && files.length > 0) {
                    const fileReader = new FileReader()

                    fileReader.onload = (event) => {
                        this.uploadedFile = event.target.result
                    }

                    fileReader.readAsDataURL(files[0])
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

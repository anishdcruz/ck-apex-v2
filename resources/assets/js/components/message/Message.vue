<template>
    <div class="message-group">
        <transition-group name="list" tag="div">
        <div :class="`message message-${message.type}`" v-for="message in messages" :key="message.id">
             <span>{{message.text}}</span>
             <i class="fa fa-times" @click="clear(message)"></i>
        </div>
        </transition-group>
    </div>
</template>
<script type="text/javascript">
    export default {
        data() {
            return {
                messages: []
            }
        },
        methods: {
            add(text, type, timeout) {
                const id = Math.random().toString(36).substring(7)

                const message = {
                    id: id,
                    text: text,
                    type: type
                }

                this.messages.unshift(message)

                setTimeout(() => {
                    this.clear(message)
                }, timeout);
            },
            success(message) {
                this.add(message, 'success', 3000)
            },
            plain(message) {
                this.add(message, 'plain', 3000)
            },
            error(message) {
                this.add(message, 'danger', 3000)
            },
            info(message) {
                this.add(message, 'info', 3000)
            },
            clear(message) {
                const index = this.messages.indexOf(message)
                if(index !== -1) {
                    this.messages.splice(index, 1)
                }
            }
        }
    }
</script>

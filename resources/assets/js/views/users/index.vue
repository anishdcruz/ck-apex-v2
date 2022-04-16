<template>
    <div>
        <panel ref="panel" :heading="heading" :resource="resource">
            <span slot="title">Users</span>
            <router-link slot="create" to="/users/create" class="btn btn-primary">
                New User
            </router-link>
            <tr slot-scope="props" @click="$router.push(`${resource}/${props.item.id}`)">
                    <td class="width-1">{{ props.item.id }}</td>
                    <td class="width-3">{{ props.item.name }}</td>
                    <td class="width-4">{{ props.item.email }}</td>
                    <td class="width-2">
                        <span class="status status-accepted" v-if="props.item.is_admin">Yes</span>
                        <span class="status status-draft" v-else>No</span>
                    </td>
                    <td class="width-2">
                        <span class="status status-paid" v-if="props.item.is_active">Yes</span>
                        <span class="status status-draft" v-else>No</span>
                    </td>
                </tr>
        </panel>
    </div>
</template>
<script type="text/javascript">
    import Panel from '../../components/search/panel.vue'
    import { get } from '../../lib/api'

    export default {
        components: { Panel },
        data() {
            return {
                resource: '/users',
                heading: [
                    {title: 'ID', name: 'id', sort: true},
                    {title: 'Name', name: 'name', sort: true},
                    {title: 'Email', name: 'email', sort: true},
                    {title: 'Is Admin', name: 'is_admin', sort: true},
                    {title: 'Is Active', name: 'is_active', sort: true}
                ]
            }
        },
        beforeRouteEnter(to, from, next) {
            get('/api/users', to.query)
                .then(res => {
                    next(vm => vm.setData(res))
                })
                // catch 422
        },
        beforeRouteUpdate (to, from, next) {
            get('/api/users', to.query)
                .then(res => {
                    this.setData(res)
                    next()
                })
                //catch 422
        },
        methods: {
            setData(res) {
                this.$title.set(`Users`)
                this.$refs.panel.setData(res)
            }
        }
    }
</script>

<template>
    <div>
        <panel ref="panel" :heading="heading" :resource="resource">
            <span slot="title">Clients</span>
            <router-link slot="create" to="/clients/create" class="btn btn-primary">
                New Client
            </router-link>
            <tr slot-scope="props" @click="$router.push(`${resource}/${props.item.id}`)">
                <td class="width-1">{{ props.item.id }}</td>
                <td class="width-3">{{ props.item.person }}</td>
                <td class="width-3">{{ props.item.company }}</td>
                <td class="width-3">{{ props.item.email }}</td>
                <td class="width-2">{{ props.item.created_at }}</td>
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
                resource: '/clients',
                heading: [
                    {title: 'ID', name: 'id', sort: true},
                    {title: 'Person', name: 'person', sort: true},
                    {title: 'Company', name: 'company', sort: true},
                    {title: 'Email', name: 'email', sort: true},
                    {title: 'Created At', name: 'created_at', sort: true}
                ]
            }
        },
        beforeRouteEnter(to, from, next) {
            get('/api/clients', to.query)
                .then(res => {
                    next(vm => vm.setData(res))
                })
                // catch 422
        },
        beforeRouteUpdate (to, from, next) {
            get('/api/clients', to.query)
                .then(res => {
                    this.setData(res)
                    next()
                })
                //catch 422
        },
        methods: {
            setData(res) {
                this.$title.set('Clients')
                this.$refs.panel.setData(res)
            }
        }
    }
</script>

<template>
    <div>
        <panel ref="panel" :heading="heading" :resource="resource">
            <span slot="title">Bills</span>
            <router-link slot="create" to="/bills/create" class="btn btn-primary">
                New Bill
            </router-link>
            <tr slot-scope="props" @click="$router.push(`${resource}/${props.item.id}`)">
                <td>{{ props.item.id }}</td>
                <td class="width-2">{{ props.item.date }}</td>
                <td class="width-2">{{ props.item.number }}</td>
                <td class="width-4" :title="props.item.vendor.text">
                    {{ props.item.vendor.text | trim(40) }}
                </td>
                <td class="width-2">{{ props.item.total | formatMoney(props.item.currency) }}</td>
                <td class="width-2">
                    <status :id="props.item.status_id"></status>
                </td>
            </tr>
        </panel>
    </div>
</template>
<script type="text/javascript">
    import Panel from '../../components/search/panel.vue'
    import Status from '../../components/status/Bill.vue'

    import { get } from '../../lib/api'

    export default {
        components: { Panel, Status },
        data() {
            return {
                resource: '/bills',
                heading: [
                    {title: 'ID', name: 'id', sort: true},
                    {title: 'Date', name: 'date', sort: true},
                    {title: 'Number', name: 'number', sort: true},
                    {title: 'Vendor', name: 'vendor', sort: false},
                    {title: 'Amount', name: 'total', sort: true},
                    {title: 'Status', name: 'status_id', sort: true}
                ]
            }
        },
        beforeRouteEnter(to, from, next) {
            get('/api/bills', to.query)
                .then(res => {
                    next(vm => vm.setData(res))
                })
                // catch 422
        },
        beforeRouteUpdate (to, from, next) {
            get('/api/bills', to.query)
                .then(res => {
                    this.setData(res)
                    next()
                })
                //catch 422
        },
        methods: {
            setData(res) {
                this.$title.set(`Bills`)
                this.$refs.panel.setData(res)
            }
        }
    }
</script>

<template>
    <div>
        <panel ref="panel" :heading="heading" :resource="resource">
            <span slot="title">Client Payments</span>
            <router-link slot="create" to="/client_payments/create" class="btn btn-primary">
                New Client Payment
            </router-link>
            <tr slot-scope="props" @click="$router.push(`${resource}/${props.item.id}`)">
                <td>{{ props.item.id }}</td>
                <td class="width-2">{{ props.item.payment_date }}</td>
                <td class="width-2">{{ props.item.number }}</td>
                <td class="width-4" :title="props.item.client.text">
                    {{ props.item.client.text | trim(40) }}
                </td>
                <td class="width-2">{{ props.item.amount_received | formatMoney(props.item.currency) }}</td>
                <td class="width-2">
                    <status :id="props.item.status_id"></status>
                </td>
            </tr>
        </panel>
    </div>
</template>
<script type="text/javascript">
    import Panel from '../../components/search/panel.vue'
    import Status from '../../components/status/ClientPayment.vue'
    import { get } from '../../lib/api'

    export default {
        components: { Panel, Status },
        data() {
            return {
                resource: '/client_payments',
                heading: [
                    {title: 'ID', name: 'id', sort: true},
                    {title: 'Date', name: 'date', sort: true},
                    {title: 'Number', name: 'number', sort: true},
                    {title: 'Client', name: 'client', sort: false},
                    {title: 'Amount Received', name: 'amount_received', sort: true},
                    {title: 'Status', name: 'status_id', sort: true}
                ]
            }
        },
        beforeRouteEnter(to, from, next) {
            get('/api/client_payments', to.query)
                .then(res => {
                    next(vm => vm.setData(res))
                })
                // catch 422
        },
        beforeRouteUpdate (to, from, next) {
            get('/api/client_payments', to.query)
                .then(res => {
                    this.setData(res)
                    next()
                })
                //catch 422
        },
        methods: {
            setData(res) {
                this.$title.set(`Client Payment`)
                this.$refs.panel.setData(res)
            }
        }
    }
</script>

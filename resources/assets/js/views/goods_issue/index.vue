<template>
    <div>
        <panel ref="panel" :heading="heading" :resource="resource">
            <span slot="title">Goods Issue</span>
            <tr slot-scope="props" @click="$router.push(`${resource}/${props.item.id}`)">
                    <td>{{ props.item.id }}</td>
                    <td class="width-2">{{ props.item.date }}</td>
                    <td class="width-2">{{ props.item.number }}</td>
                    <td class="width-6" :title="props.item.client.text">
                        {{ props.item.client.text | trim(40) }}
                    </td>
                    <td class="width-2">
                        <status :id="props.item.status_id"></status>
                    </td>
                </tr>
        </panel>
    </div>
</template>
<script type="text/javascript">
    import Panel from '../../components/search/panel.vue'
    import Status from '../../components/status/ReceiveOrder.vue'

    import { get } from '../../lib/api'

    export default {
        components: { Panel, Status },
        data() {
            return {
                resource: '/goods_issue',
                heading: [
                    {title: 'ID', name: 'id', sort: true},
                    {title: 'Date', name: 'date', sort: true},
                    {title: 'Number', name: 'number', sort: true},
                    {title: 'Client', name: 'client', sort: false},
                    {title: 'Status', name: 'status_id', sort: true}
                ]
            }
        },
        beforeRouteEnter(to, from, next) {
            get('/api/goods_issue', to.query)
                .then(res => {
                    next(vm => vm.setData(res))
                })
                // catch 422
        },
        beforeRouteUpdate (to, from, next) {
            get('/api/goods_issue', to.query)
                .then(res => {
                    this.setData(res)
                    next()
                })
                //catch 422
        },
        methods: {
            setData(res) {
                this.$title.set(`Receive Order`)
                this.$refs.panel.setData(res)
            }
        }
    }
</script>

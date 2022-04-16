<template>
    <div>
        <panel ref="panel" :heading="heading" :resource="resource">
            <span slot="title">Products</span>
            <router-link slot="create" to="/products/create" class="btn btn-primary">
                New Product
            </router-link>
            <tr slot-scope="props" @click="$router.push(`${resource}/${props.item.id}`)">
                    <td class="width-1">{{ props.item.id }}</td>
                    <td class="width-2">{{ props.item.item_code }}</td>
                    <td class="width-7">{{ props.item.description | trim(80) }}</td>
                    <td class="width-2">{{ props.item.unit_price | formatMoney(props.item.currency) }}</td>
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
                resource: '/products',
                heading: [
                    {title: 'ID', name: 'id', sort: true},
                    {title: 'Item Code', name: 'item_code', sort: true},
                    {title: 'Description', name: 'description', sort: true},
                    {title: 'Unit Price', name: 'unit_price', sort: true}
                ]
            }
        },
        beforeRouteEnter(to, from, next) {
            get('/api/products', to.query)
                .then(res => {
                    next(vm => vm.setData(res))
                })
                // catch 422
        },
        beforeRouteUpdate (to, from, next) {
            get('/api/products', to.query)
                .then(res => {
                    this.setData(res)
                    next()
                })
                //catch 422
        },
        methods: {
            setData(res) {
                this.$title.set(`Products`)
                this.$refs.panel.setData(res)
            }
        }
    }
</script>

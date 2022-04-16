<template>
    <div v-if="show">
        <div class="panel">
            <div class="panel-heading">
                <span class="panel-title">{{model.item_code}}</span>
                <div>
                    <router-link :to="`/products`" class="btn" title="Go Back">
                        <i class="fa fa-arrow-left"></i>
                    </router-link>
                    <router-link :to="`/products/${model.id}/edit`"
                        class="btn" title="Edit">
                        <i class="fa fa-pencil"></i>
                    </router-link>
                    <a @click.stop="deleteModel" class="btn btn-danger">
                        <i class="fa fa-trash"></i>
                    </a>
                </div>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col col-8">
                        <div class="bg-grey">
                            <div class="row">
                                <div class="col col-5">
                                    <strong>Item Code</strong><br><br>
                                    <span>{{model.item_code}}</span>
                                    <hr>
                                    <strong>Unit Price</strong><br><br>
                                    <span>{{model.unit_price | formatMoney(model.currency)}}</span>
                                    <hr>
                                    <strong>Currency</strong><br><br>
                                    <span>{{model.currency.text}}</span>
                                </div>
                                 <div class="col col-7">
                                    <strong>Description</strong><br><br>
                                    <pre>{{model.description}}</pre>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col col-4">
                        <div class="bg-grey" v-if="model.has_inventory">
                            <h3>Inventory</h3>
                            <hr>
                            <div class="row">
                                <div class="col col-12">
                                    <div class="row">
                                        <div class="col col-9">
                                            Qty on Hand
                                        </div>
                                        <div class="col col-3">
                                            {{model.qty_on_hand}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel" v-if="model.taxes.length">
            <div class="panel-heading">
                <span class="panel-title">Product Taxes</span>
            </div>
            <div class="panel-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tax Name</th>
                            <th>Rate</th>
                            <th>Tax Authority</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in model.taxes">
                            <td class="width-4">{{item.name}}</td>
                            <td class="width-2">{{item.rate}}%</td>
                            <td class="width-6">{{item.tax_authority}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="panel" v-if="model.items.length">
            <div class="panel-heading">
                <span class="panel-title">Product Vendors</span>
            </div>
            <div class="panel-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Vendor</th>
                            <th>Reference</th>
                            <th>Price</th>
                            <th>Currency</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in model.items">
                            <td class="width-6">
                                <router-link :to="`/vendors/${item.vendor_id}`">
                                    {{item.vendor.text}}
                                </router-link>
                            </td>
                            <td class="width-2">{{item.reference}}</td>
                            <td class="width-2">{{item.price | formatMoney(item.currency)}}</td>
                            <td class="width-2">{{item.currency.text}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
<script type="text/javascript">
    import Vue from 'vue'
    import { get, byMethod } from '../../lib/api'
    export default {
        data() {
            return {
                show: false,
                model: {
                    currency: {},
                    items: []
                }
            }
        },
        beforeRouteEnter(to, from, next) {
            get(`/api/products/${to.params.id}`)
                .then(res => {
                    next(vm => vm.setData(res))
                })
                // catch 422
        },
        beforeRouteUpdate (to, from, next) {
            this.show = false
            get(`/api/products/${to.params.id}`)
                .then(res => {
                    this.setData(res)
                    next()
                })
                //catch 422
        },
        methods: {
            deleteModel() {
                const r = confirm("Are you sure!")
                if(r != true) { return }
                this.$bar.start()
                byMethod('delete', `/api/products/${this.model.id}`)
                    .then(({data}) => {
                        if(data.deleted) {
                            this.$router.push('/products')
                            this.$message.success(`You have successfully deleted quotation!`)
                        }
                        this.$bar.finish()
                    })
                    .catch((err) => {})
            },
            setData(res) {
                Vue.set(this.$data, 'model', res.data.data)
                this.$title.set(`Products - ${this.model.item_code}`)
                this.$bar.finish()
                this.show = true
            }
        }
    }
</script>

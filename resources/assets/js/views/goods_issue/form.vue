<template>
    <div v-if="show">
        <div class="panel">
            <div class="panel-heading">
                <span class="panel-title">{{title}} Goods Issue</span>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col col-6">
                        <div class="form-group">
                            <label>Client</label>
                            <span class="form-control">{{form.client.text}}</span>
                        </div>
                    </div>
                    <div class="col col-3">
                        <div class="form-group">
                            <label>Sales Order</label>
                            <span class="form-control">
                                {{form.sales_order_number}}
                            </span>
                        </div>
                    </div>
                    <div class="col col-3">
                        <div class="form-group">
                            <label>
                                Number
                                <small>(Auto Generated)</small>
                            </label>
                            <span class="form-control">{{form.number}}</span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col col-3">
                        <div class="form-group">
                            <label>Date</label>
                            <input type="date" class="form-control" v-model="form.date">
                            <error-text :error="error.date"></error-text>
                        </div>
                    </div>
                </div>
                <hr>
                <table class="item-table">
                    <thead>
                        <tr>
                            <th>Item Code</th>
                            <th>Description</th>
                            <th>Qty on Hand</th>
                            <th>Qty in Order</th>
                            <th>Qty Issued</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template v-for="(item, index) in form.items">
                            <tr>
                                <td class="width-2">
                                    <span class="form-control">{{item.product.item_code}}</span>
                                </td>
                                <td class="width-5">
                                    <span class="form-control">{{item.product.description | trim(65)}}</span>
                                </td>
                                <td class="width-1">
                                    <span class="form-control">{{item.product.qty_on_hand}}</span>
                                </td>
                                <td class="width-1">
                                    <span class="form-control">{{item.qty}}</span>
                                </td>
                                <td :class="['width-1', errors(`items.${index}.qty_issued`)]">
                                    <input type="text" class="form-control" v-model="item.qty_issued"
                                        :disabled="item.product.qty_on_hand < 1">
                                    <error-text :error="error[`items.${index}.qty_issued`]"></error-text>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
                <hr>
                <div class="row">
                    <div class="col col-6">
                        <div class="form-group">
                            <label>Internal Note</label>
                            <textarea class="form-control" v-model="form.note"></textarea>
                            <error-text :error="error.note"></error-text>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel-footer">
                <spinner v-if="isProcessing"></spinner>
                <div class="btn-group" v-else>
                    <button :disabled="isProcessing" @click="save" class="btn btn-primary">
                        Save
                    </button>
                    <router-link :disabled="isProcessing" :to="`${resource}/${$route.params.id}`"
                        class="btn" v-if="isEdit">
                        Cancel
                    </router-link>
                    <router-link :disabled="isProcessing" :to="`${resource}`"
                        class="btn" v-else>
                        Cancel
                    </router-link>
                </div>
            </div>
        </div>
    </div>
</template>
<script type="text/javascript">
    import Vue from 'vue'
    import ErrorText from '../../components/form/ErrorText.vue'
    import Typeahead from '../../components/form/Typeahead.vue'
    import FileUpload from '../../components/form/FileUpload.vue'
    import Spinner from '../../components/loading/Spinner.vue'
    import { get, byMethod } from '../../lib/api'
    import { form } from '../../lib/mixins'

    function initializeUrl (to) {
        let urls = {
            'sales_order': `/api/sales_orders/${to.params.id}/edit?mode=goods_issue`,
        }

        return (urls[to.meta.mode] || urls['create'])
    }

    export default {
        components: { ErrorText, Typeahead, Spinner, FileUpload },
        mixins: [ form ],
        data () {
            return {
                resource: '/goods_issue',
                store: '/api/goods_issue',
                method: 'POST',
                title: 'Create',
                message: 'You have successfully created receive_order!',
                currencyURL: '/api/search/currencies',
                productURL: '/api/search/products',
                clientURL: '/api/search/clients'
            }
        },
        beforeRouteEnter(to, from, next) {
            get(initializeUrl(to))
                .then(res => {
                    next(vm => vm.setData(res))
                })
                // catch 422
        },
        beforeRouteUpdate (to, from, next) {
            this.show = false

            get(initializeUrl(to))
                .then(res => {
                    this.setData(res)
                    next()
                })
                //catch 422
        },
        methods: {
            onDocument(e) {
                Vue.set(this.$data.form, 'document', e.target.value)
            },
            removeProduct(item, index) {
                if(this.form.items.length > 1) {
                    this.form.items.splice(index, 1)
                }
            },
            onClientUpdate(e) {
                const client = e.target.value

                // client
                Vue.set(this.form, 'client', client)
                Vue.set(this.form, 'client_id', client.id)
            },
            save() {
                this.submitMultipartForm(this.form, (data) => {
                    this.success()
                    this.$router.push(`${this.resource}/${data.id}`)
                })
                this.endProcessing()
            },
            saveAndNew() {
                this.submitMultipartForm(this.form, (data) => {
                    const id = Math.random().toString(36).substring(7)
                    this.endProcessing()
                    this.success()
                    this.$router.push(`${this.resource}/create?new=${id}`)
                })
            },
            setData(res) {
                Vue.set(this.$data, 'form', res.data.form)
                this.$title.set(`Goods Issue ${this.title}`)
                this.$bar.finish()
                this.show = true
            }
        }
    }
</script>

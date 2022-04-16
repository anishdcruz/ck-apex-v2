<template>
    <transition name="modal">
        <div class="modal-mask">
            <div class="modal-wrapper">
                <div class="modal-container">
                    <div class="modal-heading">Apply Advance Payment to Invoices</div>
                    <div class="modal-body" v-if="show">
                        <div class="row">
                            <div class="col col-6">
                                <div class="form-group">
                                    <label>Client</label>
                                    <span class="form-control">{{form.client.text}}</span>
                                </div>
                            </div>
                            <div class="col col-3">
                                <div class="form-group">
                                    <label>Currency</label>
                                    <span class="form-control">{{form.currency.text}}</span>
                                </div>
                            </div>
                            <div class="col col-3">
                                <div class="form-group">
                                    <label>Advance Payment Number</label>
                                    <span class="form-control">{{form.number}}</span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col col-4">
                                <div class="form-group">
                                    <label>Balance Amount</label>
                                    <span class="form-control">{{form.amount_received | formatMoney(form.currency)}}</span>
                                </div>
                            </div>
                        </div>
                        <div class="alert alert-danger" v-if="!form.items.length">
                            <span>There are no sent and partially paid invoices for this client.</span>
                        </div>
                        <div v-else>
                            <hr>
                            <table class="item-table">
                                <thead>
                                    <tr>
                                        <th>Invoice Date</th>
                                        <th>Invoice Number</th>
                                        <th>Invoice Total</th>
                                        <th>Balance Due</th>
                                        <th>Amount Applied</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, index) in form.items">
                                        <td class="width-2">
                                            <span class="form-control">{{item.date}}</span>
                                        </td>
                                        <td class="width-3">
                                            <span class="form-control">{{item.number}}</span>
                                        </td>
                                        <td class="width-2">
                                            <span class="form-control">{{item.total}}</span>
                                        </td>
                                        <td class="width-2">
                                            <span class="form-control">{{item.total - item.amount_paid}}</span>
                                        </td>
                                        <td :class="['width-3', errors(`items.${index}.amount_applied`)]">
                                            <input type="text" class="form-control" v-model="item.amount_applied">
                                            <error-text :error="error[`items.${index}.amount_applied`]"></error-text>
                                        </td>
                                        <td>
                                            <button class="item-remove" @click="removeItem(item, index)">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td class="item-footer" colspan="4">Balance Amount</td>
                                        <td class="item-footer">
                                            <strong class="item-dark form-control">
                                                {{form.amount_received | formatMoney(form.currency)}}
                                            </strong>
                                            <error-text :error="error.amount_received"></error-text>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="item-footer" colspan="4">Amount Applied to Invoices</td>
                                        <td class="item-footer">
                                            <strong class="item-dark form-control">
                                                {{total | formatMoney(form.currency)}}
                                            </strong>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="panel-footer">
                        <spinner v-if="isProcessing"></spinner>
                        <div class="btn-group" v-else>
                            <button :disabled="isProcessing" @click="save" class="btn btn-primary" v-if="form.items.length">
                                Save
                            </button>
                            <button :disabled="isProcessing" @click="closeModal" class="btn">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
      </transition>
</template>
<script type="text/javascript">
    import Vue from 'vue'
    import { get } from '../../lib/api'
    import { form } from '../../lib/mixins'
    import ErrorText from '../../components/form/ErrorText.vue'
    import Spinner from '../../components/loading/Spinner.vue'
    export default {
        components: {ErrorText, Spinner},
        mixins: [form],
        data() {
            return {
                resource: `/advance_payments/${this.$route.params.id}`,
                url: `/api/search/advance_payment_invoices/${this.$route.params.id}`,
                store: `/api/advance_payment_invoices/${this.$route.params.id}/apply`,
                method: 'POST',
                message: 'You have successfully applied payment to invoices!',
                form: {
                    currency: {},
                    client: {},
                    items: []
                }
            }
        },
        computed: {
            total() {
                return this.form.items.reduce((carry, item) => {
                    return carry + (Number(item.amount_applied))
                }, 0)
            }
        },
        created() {
            this.fetchData()
        },
        methods: {
            closeModal() {
                this.$emit('close')
            },
            fetchData() {
                get(this.url)
                    .then((response) => {
                        if(response.data) {
                            Vue.set(this.$data, 'form', response.data.data)
                            this.show = true
                        }
                    })
                    .catch((error) => {
                        if(error.response.status === 422) {
                            Vue.set(this.$data, 'error', error.response.data)
                        }
                    })
            },
            save() {
                this.submit((data) => {
                    const id = Math.random().toString(36).substring(7)
                    this.success()
                    this.closeModal()
                    this.$router.push(`${this.resource}?success=${id}`)
                })
            }
        }
    }
</script>

<template>
    <div v-if="show">
        <div class="panel">
            <div class="panel-heading">
                <span class="panel-title">{{title}} Client</span>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col col-4">
                        <div class="form-group">
                            <label>Person</label>
                            <input type="text" class="form-control" v-model="form.person">
                            <error-text :error="error.person"></error-text>
                        </div>
                        <div class="form-group">
                            <label>
                                Company
                            </label>
                            <input type="text" class="form-control" v-model="form.company">
                            <error-text :error="error.company"></error-text>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="text" class="form-control" v-model="form.email">
                            <error-text :error="error.email"></error-text>
                        </div>
                    </div>
                    <div class="col col-4">
                        <div class="form-group">
                            <label>
                                Mobile Number
                                <small>(optional)</small>
                            </label>
                            <input type="text" class="form-control" v-model="form.mobile_number">
                            <error-text :error="error.mobile_number"></error-text>
                        </div>
                        <div class="form-group">
                            <label>
                                Work Phone
                                <small>(optional)</small>
                            </label>
                            <input type="text" class="form-control" v-model="form.work_phone">
                            <error-text :error="error.work_phone"></error-text>
                        </div>
                    </div>
                    <div class="col col-4">
                        <div class="form-group">
                            <label>Currency</label>
                            <typeahead :initial="form.currency"
                                :url="currencyURL"
                                @input="onCurrencyUpdate"
                            >
                            </typeahead>
                            <error-text :error="error.currency_id"></error-text>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col col-4">
                        <div class="form-group">
                            <label>Billing Address</label>
                            <textarea class="form-control" v-model="form.billing_address">
                            </textarea>
                            <error-text :error="error.billing_address"></error-text>
                        </div>
                    </div>
                    <div class="col col-4">
                        <div class="form-group">
                            <label>
                                <span>Shipping Address</span>
                                <a @click.stop="copyShippingAddress">Copy Shipping Address</a>
                            </label>
                            <textarea class="form-control" v-model="form.shipping_address">
                            </textarea>
                            <error-text :error="error.shipping_address"></error-text>
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
                    <button :disabled="isProcessing" v-if="!isEdit"
                        @click="saveAndNew" class="btn btn-secondary">
                        Save and New
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
    import Spinner from '../../components/loading/Spinner.vue'
    import { get, byMethod } from '../../lib/api'
    import { form } from '../../lib/mixins'

    function initializeUrl (to) {
        let urls = {
            'create': `/api/clients/create`,
            'edit': `/api/clients/${to.params.id}/edit`,
            'clone': `/api/clients/${to.params.id}/edit?mode=clone`,
        }

        return (urls[to.meta.mode] || urls['create'])
    }

    export default {
        components: { ErrorText, Typeahead, Spinner },
        mixins: [ form ],
        data () {
            return {
                resource: '/clients',
                store: '/api/clients',
                method: 'POST',
                title: 'Create',
                message: 'You have successfully created client!',
                currencyURL: '/api/search/currencies'
            }
        },
        created() {
            if(this.mode === 'edit') {
                this.store = `/api/clients/${this.$route.params.id}`
                this.message = 'You have successfully updated client!'
                this.method = 'PUT'
                this.title = 'Edit'
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
            save() {
                this.submit((data) => {
                    this.success()
                    this.$router.push(`${this.resource}/${data.id}`)
                })
            },
            saveAndNew() {
                this.submit((data) => {
                    const id = Math.random().toString(36).substring(7)
                    this.endProcessing()
                    this.success()
                    this.$router.push(`${this.resource}/create?new=${id}`)
                })
            },
            copyShippingAddress() {
                this.form.shipping_address = this.form.billing_address
            },
            onCurrencyUpdate(e) {
                const currency = e.target.value
                Vue.set(this.form, 'currency_id', currency.id)
                Vue.set(this.form, 'currency', currency)
            },
            setData(res) {
                Vue.set(this.$data, 'form', res.data.form)
                this.$title.set(`Client ${this.title}`)
                this.$bar.finish()
                this.show = true
            }
        }
    }
</script>

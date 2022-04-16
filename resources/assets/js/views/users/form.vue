<template>
    <div v-if="show">
        <div class="panel">
            <div class="panel-heading">
                <span class="panel-title">{{title}} User</span>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col col-4">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" class="form-control" v-model="form.name">
                            <error-text :error="error.name"></error-text>
                        </div>
                        <div class="form-group">
                            <label>Job Title</label>
                            <input type="text" class="form-control" v-model="form.title">
                            <error-text :error="error.title"></error-text>
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
                                Telephone
                                <small>(optional)</small>
                            </label>
                            <input type="text" class="form-control" v-model="form.telephone">
                            <error-text :error="error.telephone"></error-text>
                        </div>
                        <div class="form-group">
                            <label>
                                Extension
                                <small>(optional)</small>
                            </label>
                            <input type="text" class="form-control" v-model="form.extension">
                            <error-text :error="error.extension"></error-text>
                        </div>
                    </div>
                    <div class="col col-4">
                        <div class="form-group">
                            <label>
                                Is Admin
                            </label>
                            <select class="form-control" v-model="form.is_admin">
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                            <error-text :error="error.is_admin"></error-text>
                        </div>
                        <div class="form-group">
                            <label>
                                Is Active
                            </label>
                            <select class="form-control" v-model="form.is_active">
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                            <error-text :error="error.is_active"></error-text>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col col-4">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="text" class="form-control" v-model="form.email">
                            <error-text :error="error.email"></error-text>
                        </div>
                    </div>
                    <div class="col col-4">
                        <div class="form-group">
                            <label>
                                Password
                                <small v-if="mode == 'edit'">Optional</small>
                            </label>
                            <input type="password" class="form-control" v-model="form.password">
                            <error-text :error="error.password"></error-text>
                        </div>
                    </div>
                    <div class="col col-4">
                        <div class="form-group">
                            <label>
                                Confirm Password
                                <small v-if="mode == 'edit'">Optional</small>
                            </label>
                            <input type="password" class="form-control" v-model="form.password_confirmation">
                            <error-text :error="error.password_confirmation"></error-text>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col col-4">
                        <div class="form-group">
                            <label>
                                <span>Email Signature</span>
                                <a @click.stop="generate">Generate</a>
                            </label>
                            <textarea class="form-control" v-model="form.email_signature">
                            </textarea>
                            <error-text :error="error.email_signature"></error-text>
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
            'create': `/api/users/create`,
            'edit': `/api/users/${to.params.id}/edit`,
            'clone': `/api/users/${to.params.id}/edit?mode=clone`,
        }

        return (urls[to.meta.mode] || urls['create'])
    }

    export default {
        components: { ErrorText, Typeahead, Spinner },
        mixins: [ form ],
        data () {
            return {
                resource: '/users',
                store: '/api/users',
                method: 'POST',
                title: 'Create',
                message: 'You have successfully created user!',
                currencyURL: '/api/search/currencies'
            }
        },
        created() {
            if(this.mode === 'edit') {
                this.store = `/api/users/${this.$route.params.id}`
                this.message = 'You have successfully updated user!'
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
                    if(this.form.id === window.apex.user.id) {
                        window.apex.user.name = this.form.name
                    }
                    this.$router.push(`${this.resource}/${data.id}`)
                })
            },
            saveAndNew() {
                this.submit((data) => {
                    const id = Math.random().toString(36).substring(7)
                    this.endProcessing()
                    this.success()
                    if(this.form.id === window.apex.user.id) {
                        window.apex.user.name = this.form.name
                    }
                    this.$router.push(`${this.resource}/create?new=${id}`)
                })
            },
            generate() {
                this.form.email_signature = `Best Regards\n\n${this.form.name}\n${this.form.title}, ${this.form.company}\nEmail: ${this.form.email}\nTel: ${this.form.telephone} Ext: ${this.form.extension}\nMob: ${this.form.mobile_number}`
            },
            onCurrencyUpdate(e) {
                const currency = e.target.value
                Vue.set(this.form, 'currency_id', currency.id)
                Vue.set(this.form, 'currency', currency)
            },
            setData(res) {
                Vue.set(this.$data, 'form', res.data.form)
                this.$title.set(`Users ${this.title}`)
                this.$bar.finish()
                this.show = true
            }
        }
    }
</script>
